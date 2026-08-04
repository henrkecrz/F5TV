<?php
/**
 * F5TV Theme - Hooks and Filters
 * Ações e filtros globais do tema
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Filtro de Capability Customizada: Controla o acesso de visualização a posts/vídeos
 */
add_filter('user_has_cap', 'f5tv_check_content_access_capability', 10, 4);
function f5tv_check_content_access_capability(array $allcaps, array $caps, array $args, WP_User $user): array
{
    // Verifica se a cap sendo testada é 'f5tv_access_content'
    if (empty($args[0]) || $args[0] !== 'f5tv_access_content') {
        return $allcaps;
    }

    $post_id = isset($args[2]) ? intval($args[2]) : 0;
    if (!$post_id) {
        return $allcaps;
    }

    // Admins e editores sempre têm acesso
    if (in_array('administrator', $user->roles) || in_array('editor', $user->roles)) {
        $allcaps['f5tv_access_content'] = true;
        return $allcaps;
    }

    // Se o conteúdo for livre, qualquer um pode ver
    $is_free = get_field('is_free', $post_id);
    if ($is_free) {
        $allcaps['f5tv_access_content'] = true;
        return $allcaps;
    }

    // Se não estiver logado, não tem acesso
    if (!$user->ID) {
        $allcaps['f5tv_access_content'] = false;
        return $allcaps;
    }

    // Caso o usuário esteja logado, precisamos verificar a assinatura
    // Aqui fazemos uma busca na tabela de assinaturas customizada wp_f5tv_subscriptions
    global $wpdb;
    $table_name = $wpdb->prefix . 'f5tv_subscriptions';

    // Se a tabela ainda não existir (ex: antes da ativação do plugin f5tv-client-area), fallback para usermeta
    $has_table = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) === $table_name;
    
    if ($has_table) {
        $sub = $wpdb->get_row($wpdb->prepare(
            "SELECT status, plan_id FROM $table_name WHERE user_id = %d AND status = 'active' LIMIT 1",
            $user->ID
        ));
        if ($sub) {
            // Se tiver assinatura ativa, verifica se o conteúdo é exclusivo premium
            $is_exclusive = get_field('is_exclusive', $post_id);
            $plan_id = $sub->plan_id;

            if ($is_exclusive && $plan_id === 'plano-basico') {
                // Conteúdo exclusivo só pode ser acessado por planos Família ou Premium
                $allcaps['f5tv_access_content'] = false;
            } else {
                $allcaps['f5tv_access_content'] = true;
            }
            return $allcaps;
        }
    } else {
        // Fallback usando user meta provisório
        $user_plan = get_user_meta($user->ID, 'f5tv_plan', true);
        $user_sub_status = get_user_meta($user->ID, 'f5tv_subscription_status', true);

        if ($user_sub_status === 'active' && !empty($user_plan)) {
            $is_exclusive = get_field('is_exclusive', $post_id);
            if ($is_exclusive && $user_plan === 'plano-basico') {
                $allcaps['f5tv_access_content'] = false;
            } else {
                $allcaps['f5tv_access_content'] = true;
            }
            return $allcaps;
        }
    }

    $allcaps['f5tv_access_content'] = false;
    return $allcaps;
}

/**
 * Modificar WP Query principal para garantir que rascunhos não vazem para não-admins na API
 */
add_action('pre_get_posts', 'f5tv_modify_main_queries');
function f5tv_modify_main_queries(WP_Query $query): void
{
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    $cpts = ['f5tv_conteudo', 'f5tv_serie', 'f5tv_canal', 'f5tv_programacao'];
    $post_type = $query->get('post_type');
    if (is_string($post_type) && in_array($post_type, $cpts)) {
        $query->set('post_status', 'publish');
    } elseif (is_array($post_type) && array_intersect($post_type, $cpts)) {
        $query->set('post_status', 'publish');
    }
}

/**
 * Auto-criar páginas internas do WordPress se não existirem no DB
 */
add_action('init', 'f5tv_auto_create_pages');
function f5tv_auto_create_pages(): void
{
    $default_contents = [
        'area-do-assinante' => '[f5tv_dashboard]',
    ];

    $pages = [
        'planos'               => 'Planos e Assinaturas',
        'home'                 => 'Página Inicial F5 TV',
        'ao-vivo'              => 'Ao Vivo',
        'programacao'          => 'Programação',
        'minha-lista'          => 'Minha Lista',
        'continuar-assistindo' => 'Continuar Assistindo',
        'busca'                => 'Busca',
        'minha-conta'          => 'Minha Conta',
        'dispositivos'         => 'Dispositivos',
        'checkout'             => 'Checkout',
        'assista'              => 'Assista',
        'termos'               => 'Termos de Serviço',
        'privacidade'          => 'Política de Privacidade',
        'sobre'                => 'Sobre a F5 TV',
        'contato'              => 'Contato & Suporte',
        'area-do-assinante'    => 'Área do Assinante',
        'series'               => 'Catálogo',
    ];

    foreach ($pages as $slug => $title) {
        $existing = get_page_by_path($slug);
        $content = $default_contents[$slug] ?? '';
        if (!$existing) {
            wp_insert_post([
                'post_title'     => $title,
                'post_name'      => $slug,
                'post_content'   => $content,
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'comment_status' => 'closed',
            ]);
        }
    }

    // Garantir que a página estática inicial seja a Página Inicial F5 TV (slug home)
    $home_page = get_page_by_path('home');
    if ($home_page) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $home_page->ID);
    }
}

/**
 * Filtro template_include: rotear URLs internas diretamente para os modelos PHP sem erro 404
 */
add_filter('template_include', 'f5tv_route_internal_pages', 99);
function f5tv_route_internal_pages($template)
{
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $path = trim(parse_url($uri, PHP_URL_PATH), '/');
    $parts = array_filter(explode('/', $path));
    $slug = end($parts);

    $routes = [
        'planos'               => 'page-planos.php',
        'ao-vivo'              => 'page-ao-vivo.php',
        'programacao'          => 'page-programacao.php',
        'minha-lista'          => 'page-minha-lista.php',
        'continuar-assistindo' => 'page-continuar-assistindo.php',
        'busca'                => 'page-busca.php',
        'minha-conta'          => 'page-minha-conta.php',
        'dispositivos'         => 'page-dispositivos.php',
        'checkout'             => 'page-checkout.php',
        'assista'              => 'page-assista.php',
        'termos'               => 'page-termos.php',
        'privacidade'          => 'page-privacidade.php',
        'sobre'                => 'page-sobre.php',
        'contato'              => 'page-contato.php',
        'area-do-assinante'    => 'page-area-do-assinante.php',
        'series'               => 'page-series.php',
    ];

    if ($slug && isset($routes[$slug])) {
        $file = F5TV_THEME_DIR . '/' . $routes[$slug];
        if (file_exists($file)) {
            global $wp_query, $post;
            $wp_query->is_404 = false;
            $wp_query->is_archive = false;
            $wp_query->is_post_type_archive = false;
            $wp_query->is_tax = false;
            $wp_query->is_category = false;
            $wp_query->is_tag = false;
            $wp_query->is_page = true;
            $wp_query->is_singular = true;

            $page = get_page_by_path($slug);
            if ($page) {
                $wp_query->queried_object = $page;
                $wp_query->queried_object_id = $page->ID;
                $wp_query->post = $page;
                $wp_query->posts = [$page];
                $wp_query->post_count = 1;
                $post = $page;
            }
            status_header(200);
            return $file;
        }
    }

    return $template;
}

/**
 * Injetar Estilos e Scripts do Design System F5 TV no Admin do WordPress
 */
add_action('admin_enqueue_scripts', 'f5tv_admin_custom_assets');
function f5tv_admin_custom_assets($hook): void
{
    wp_add_inline_style('wp-admin', "
        /* ==========================================================
           F5 TV Streaming Product Official Design System Overhaul
           ========================================================== */
        body.wp-admin, #wpwrap, #wpcontent, #wpbody-content, .wrap {
            background-color: #03040a !important;
            color: #f4f4f5 !important;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        }

        /* Top WP Admin Bar */
        #wpadminbar {
            background: #060913 !important;
            border-bottom: 1px solid #1a2336 !important;
        }
        #wpadminbar .ab-item, #wpadminbar a.ab-item {
            color: #e4e4e7 !important;
            font-weight: 600 !important;
        }
        #wpadminbar .ab-item:hover, #wpadminbar a.ab-item:hover {
            color: #ffffff !important;
            background: #e50914 !important;
        }

        /* Menu Lateral Esquerdo (Admin Menu) */
        #adminmenuback, #adminmenuwrap, #adminmenu {
            background: #060913 !important;
            border-right: 1px solid #1a2336 !important;
        }
        #adminmenu a {
            color: #a1a1aa !important;
            font-weight: 600 !important;
            font-size: 13px !important;
        }
        #adminmenu li.menu-top:hover, 
        #adminmenu li.opensub>a.menu-top, 
        #adminmenu li.wp-has-current-submenu a.wp-has-current-submenu,
        #adminmenu li.current a.menu-top {
            background: #e50914 !important;
            color: #ffffff !important;
            font-weight: 800 !important;
        }
        #adminmenu .wp-submenu {
            background: #090e1d !important;
            border-left: 2px solid #e50914 !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.8) !important;
        }
        #adminmenu .wp-submenu a {
            color: #d4d4d8 !important;
        }
        #adminmenu .wp-submenu a:hover {
            color: #ffffff !important;
            background: rgba(229, 9, 20, 0.2) !important;
        }

        /* Cards, Postboxes e Paineis */
        .postbox, .card, .notice, div.error, div.updated {
            background: #0c101d !important;
            border: 1px solid #1f293d !important;
            border-radius: 14px !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.6) !important;
            color: #ffffff !important;
        }
        .postbox-header, .handlediv, .hndle {
            background: #0e1424 !important;
            border-bottom: 1px solid #1f293d !important;
            border-radius: 14px 14px 0 0 !important;
            color: #ffffff !important;
        }
        .postbox-header h2, .hndle span {
            color: #ffffff !important;
            font-weight: 800 !important;
            font-size: 13px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
        }
        .acf-field {
            border-color: #1f293d !important;
            background: transparent !important;
        }
        label {
            color: #d1d5db !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            font-size: 11px !important;
            letter-spacing: 0.05em !important;
        }

        /* Campos de Formulário */
        input[type=text], input[type=password], input[type=email], input[type=number], input[type=url], input[type=search], select, textarea {
            background: #060913 !important;
            border: 1px solid #27344d !important;
            color: #ffffff !important;
            border-radius: 8px !important;
            padding: 8px 12px !important;
            font-size: 13px !important;
        }
        input:focus, select:focus, textarea:focus {
            border-color: #e50914 !important;
            box-shadow: 0 0 0 3px rgba(229, 9, 20, 0.25) !important;
            outline: none !important;
        }

        /* Botões de Ação */
        .button, .button-primary, .button-secondary, .page-title-action {
            background: #e50914 !important;
            color: #ffffff !important;
            border: none !important;
            border-radius: 8px !important;
            padding: 8px 16px !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            font-size: 11px !important;
            box-shadow: 0 4px 15px rgba(229, 9, 20, 0.3) !important;
            transition: all 0.2s ease !important;
            cursor: pointer !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            text-shadow: none !important;
        }
        .button:hover, .button-primary:hover, .button-secondary:hover, .page-title-action:hover {
            background: #b80710 !important;
            color: #ffffff !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 8px 25px rgba(229, 9, 20, 0.4) !important;
        }

        /* Tabelas do WordPress */
        .wp-list-table {
            background: #0c101d !important;
            border: 1px solid #1f293d !important;
            border-radius: 12px !important;
            overflow: hidden !important;
        }
        .wp-list-table th {
            background: #060913 !important;
            color: #9ca3af !important;
            border-bottom: 1px solid #1f293d !important;
            text-transform: uppercase !important;
            font-size: 10px !important;
            font-weight: 800 !important;
        }
        .wp-list-table td {
            color: #e4e4e7 !important;
            border-bottom: 1px solid #151d2f !important;
        }
        .wp-list-table tr:hover td {
            background: rgba(229, 9, 20, 0.06) !important;
        }
        .tablenav .displaying-num, .tablenav-pages {
            color: #9ca3af !important;
        }

        /* Telas de Edição de Post e Taxonomias (#addtag, #edittag, #titlediv, #postdivrich) */
        #addtag, #edittag, .form-wrap, .edit-tag-actions {
            background: #0c101d !important;
            border: 1px solid #1f293d !important;
            border-radius: 14px !important;
            padding: 1.5rem !important;
            color: #ffffff !important;
        }
        #addtag h2, .form-wrap h2, .edit-tag-actions h2 {
            color: #ffffff !important;
            font-weight: 800 !important;
            font-size: 1.2rem !important;
        }
        .form-field input[type=text], .form-field select, .form-field textarea {
            background: #060913 !important;
            border: 1px solid #27344d !important;
            color: #ffffff !important;
            border-radius: 8px !important;
            padding: 8px 12px !important;
        }
        #titlediv #title {
            background: #060913 !important;
            border: 1px solid #27344d !important;
            color: #ffffff !important;
            border-radius: 10px !important;
            padding: 12px 16px !important;
            font-size: 1.35rem !important;
            font-weight: 800 !important;
        }
        #titlediv #title-prompt-text {
            color: #6b7280 !important;
            font-size: 1.2rem !important;
            padding: 12px 16px !important;
        }
        #wp-content-editor-container, .wp-editor-container {
            background: #060913 !important;
            border: 1px solid #27344d !important;
            border-radius: 10px !important;
        }
        .mce-tinymce, .mce-toolbar, .mce-edit-area, .mce-statusbar {
            background: #060913 !important;
            border-color: #1f293d !important;
            color: #ffffff !important;
        }
        #submitdiv, #postimagediv, #tagsdiv-f5tv_categoria, #tagsdiv-f5tv_genero {
            background: #0c101d !important;
            border: 1px solid #1f293d !important;
            border-radius: 14px !important;
        }
        #publishing-action #publish, #save-action #save-post {
            background: #e50914 !important;
            color: #ffffff !important;
            border-radius: 8px !important;
            font-weight: 900 !important;
            padding: 10px 22px !important;
            font-size: 11px !important;
            text-transform: uppercase !important;
            box-shadow: 0 4px 15px rgba(229, 9, 20, 0.4) !important;
        }
    ");
}

/**
 * Injetar Top Header de Estúdio de Produção nas telas de edição de Conteúdos/Séries/Canais
 */
add_action('edit_form_top', 'f5tv_render_editor_studio_header');
function f5tv_render_editor_studio_header(WP_Post $post): void
{
    $f5_cpts = ['f5tv_conteudo', 'f5tv_serie', 'f5tv_temporada', 'f5tv_episodio', 'f5tv_canal'];
    if (!in_array($post->post_type, $f5_cpts)) return;

    $cpt_object = get_post_type_object($post->post_type);
    $cpt_title = $cpt_object ? $cpt_object->labels->singular_name : 'Conteúdo';
    $video_url = get_field('video_url', $post->ID);
    ?>
    <div style="background: linear-gradient(135deg, #0c101d 0%, #151b2e 100%); border: 1px solid #1f293d; padding: 1.25rem 1.5rem; border-radius: 1rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <span style="font-family: monospace; font-size: 10px; padding: 3px 8px; background: rgba(229,9,20,0.15); color: #e50914; border: 1px solid rgba(229,9,20,0.3); border-radius: 4px; font-weight: 900; text-transform: uppercase;">
                    F5 STREAMING STUDIO EDITOR
                </span>
                <?php if ($video_url): ?>
                    <span style="font-family: monospace; font-size: 10px; padding: 3px 8px; background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 4px; font-weight: 800;">
                        ● Vídeo Configurado
                    </span>
                <?php endif; ?>
            </div>
            <h2 style="font-size: 1.35rem; font-weight: 900; color: #fff; margin: 0.35rem 0 0 0; letter-spacing: -0.02em;">
                🎬 Edição de <?php echo esc_html($cpt_title); ?>: <?php echo esc_html($post->post_title ?: 'Novo Título'); ?>
            </h2>
            <p style="color: #9ca3af; font-size: 0.8rem; margin: 0.2rem 0 0 0;">
                Insira a URL do vídeo (Vimeo, YouTube, MP4, HLS), defina o banner HD (1280x720) e as capas de exibição.
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <?php if ($post->ID && get_post_status($post->ID) === 'publish'): ?>
                <a href="<?php echo esc_url(home_url('/assista?id=' . $post->ID)); ?>" target="_blank" style="background: #e50914; color: #fff; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; padding: 0.6rem 1.1rem; border-radius: 0.5rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem; box-shadow: 0 4px 15px rgba(229,9,20,0.3);">
                    ▶️ Testar Player
                </a>
            <?php endif; ?>
            <a href="<?php echo esc_url(admin_url('edit.php?post_type=' . $post->post_type)); ?>" style="background: #1f293d; color: #d1d5db; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; padding: 0.6rem 1.1rem; border-radius: 0.5rem; text-decoration: none;">
                &larr; Voltar para Lista
            </a>
        </div>
    </div>
    <?php
}

/**
 * Redirecionar o login do WP-Admin diretamente para a Central F5 Streaming
 */
add_filter('login_redirect', 'f5tv_custom_login_redirect', 10, 3);
function f5tv_custom_login_redirect(string $redirect_to, string $requested_redirect_to, $user): string
{
    if (is_a($user, 'WP_User') && (in_array('administrator', $user->roles) || in_array('editor', $user->roles))) {
        return admin_url('admin.php?page=f5tv-content-studio');
    }
    return $redirect_to;
}

/**
 * Redirecionar a dashboard padrão do WordPress (index.php) e cadastros para a Central F5 Streaming
 */
add_action('admin_init', 'f5tv_redirect_default_dashboard');
function f5tv_redirect_default_dashboard(): void
{
    global $pagenow;
    if ($pagenow === 'index.php' && !isset($_GET['page'])) {
        wp_redirect(admin_url('admin.php?page=f5tv-content-studio'));
        exit;
    }

    if ($pagenow === 'post-new.php') {
        $post_type = sanitize_text_field($_GET['post_type'] ?? '');
        if (in_array($post_type, ['f5tv_conteudo', 'f5tv_serie'])) {
            wp_redirect(admin_url('admin.php?page=f5tv-content-studio&action=new&type=' . $post_type));
            exit;
        }
    }
}

/**
 * Reordenar o menu lateral para colocar o F5 Streaming e F5 Dashboard no topo absoluto
 */
add_filter('custom_menu_order', '__return_true');
add_filter('menu_order', 'f5tv_custom_menu_order');
function f5tv_custom_menu_order(array $menu_order): array
{
    $custom_order = [
        'edit.php?post_type=f5tv_conteudo',
        'f5tv-dashboard',
    ];

    foreach (array_reverse($custom_order) as $page) {
        $key = array_search($page, $menu_order);
        if ($key !== false) {
            unset($menu_order[$key]);
            array_unshift($menu_order, $page);
        }
    }
    return $menu_order;
}




