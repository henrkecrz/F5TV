<?php
/**
 * Plugin Name:  F5TV — Setup Wizard
 * Plugin URI:   https://f5tv.com.br
 * Description:  Tour de instalação guiado para o tema F5TV. Ative este plugin após instalar o tema.
 * Version:      1.0.0
 * Author:       F5 TV
 * Author URI:   https://f5tv.com.br
 * Text Domain:  f5tv-setup
 * Requires PHP: 8.1
 * Requires at least: 6.4
 */

if (!defined('ABSPATH')) exit;

define('F5TV_SETUP_VERSION', '1.0.0');
define('F5TV_SETUP_DIR',     plugin_dir_path(__FILE__));
define('F5TV_SETUP_URL',     plugin_dir_url(__FILE__));

/* ──────────────────────────────────────────────
   ACTIVAÇÃO → redirecionar para o wizard
────────────────────────────────────────────── */
register_activation_hook(__FILE__, function () {
    set_transient('f5tv_setup_redirect', true, 30);
});

add_action('admin_init', function () {
    if (get_transient('f5tv_setup_redirect')) {
        delete_transient('f5tv_setup_redirect');
        if (!isset($_GET['activate-multi'])) {
            wp_redirect(admin_url('admin.php?page=f5tv-setup'));
            exit;
        }
    }
});

/* ──────────────────────────────────────────────
   REGISTAR PÁGINA DE ADMIN
────────────────────────────────────────────── */
add_action('admin_menu', function () {
    add_menu_page(
        'F5TV Setup',
        'F5TV Setup',
        'manage_options',
        'f5tv-setup',
        'f5tv_setup_render_page',
        'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white"><path d="M4 5v14l7-4 7 4V5z"/></svg>'),
        2
    );
});

/* ──────────────────────────────────────────────
   AJAX HANDLERS
────────────────────────────────────────────── */

// Instalar + ativar plugin
add_action('wp_ajax_f5tv_install_plugin', function () {
    check_ajax_referer('f5tv_setup_nonce', 'nonce');
    if (!current_user_can('install_plugins')) wp_send_json_error('Permissão negada');

    $slug = sanitize_key($_POST['slug'] ?? '');
    if (!$slug) wp_send_json_error('Slug inválido');

    require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    require_once ABSPATH . 'wp-admin/includes/plugin.php';

    // Verificar se já está instalado
    $plugin_file = f5tv_find_plugin_file($slug);
    if ($plugin_file && is_plugin_active($plugin_file)) {
        wp_send_json_success(['status' => 'already_active', 'message' => 'Já ativo']);
    }

    if ($plugin_file) {
        // Só ativar
        activate_plugin($plugin_file);
        wp_send_json_success(['status' => 'activated', 'message' => 'Ativado com sucesso']);
    }

    // Instalar do repositório WordPress.org
    $api = plugins_api('plugin_information', ['slug' => $slug, 'fields' => ['sections' => false]]);
    if (is_wp_error($api)) {
        wp_send_json_error('Plugin não encontrado: ' . $api->get_error_message());
    }

    $skin     = new WP_Ajax_Upgrader_Skin();
    $upgrader = new Plugin_Upgrader($skin);
    $result   = $upgrader->install($api->download_link);

    if (is_wp_error($result) || !$result) {
        wp_send_json_error('Falha na instalação: ' . ($skin->get_upgrade_messages() ? implode(' ', $skin->get_upgrade_messages()) : 'erro desconhecido'));
    }

    $plugin_file = f5tv_find_plugin_file($slug);
    if ($plugin_file) {
        activate_plugin($plugin_file);
        wp_send_json_success(['status' => 'installed', 'message' => 'Instalado e ativado']);
    }

    wp_send_json_error('Instalado mas ficheiro principal não encontrado');
});

// Ativar plugin custom (já presente na pasta /plugins)
add_action('wp_ajax_f5tv_activate_plugin', function () {
    check_ajax_referer('f5tv_setup_nonce', 'nonce');
    if (!current_user_can('activate_plugins')) wp_send_json_error('Permissão negada');

    $file = sanitize_text_field($_POST['file'] ?? '');
    if (!$file) wp_send_json_error('Ficheiro inválido');

    require_once ABSPATH . 'wp-admin/includes/plugin.php';

    if (is_plugin_active($file)) {
        wp_send_json_success(['status' => 'already_active', 'message' => 'Já ativo']);
    }

    $result = activate_plugin($file);
    if (is_wp_error($result)) {
        wp_send_json_error($result->get_error_message());
    }

    wp_send_json_success(['status' => 'activated', 'message' => 'Ativado com sucesso']);
});

// Criar páginas
add_action('wp_ajax_f5tv_create_pages', function () {
    check_ajax_referer('f5tv_setup_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('Permissão negada');

    $pages = [
        ['title' => 'Página Inicial F5 TV', 'slug' => 'home',                'template' => ''],
        ['title' => 'Login',                 'slug' => 'login',               'template' => 'page-login.php'],
        ['title' => 'Cadastro',              'slug' => 'cadastro',            'template' => 'page-cadastro.php'],
        ['title' => 'Assista',               'slug' => 'assista',             'template' => 'page-assista.php'],
        ['title' => 'Séries',                'slug' => 'series',              'template' => 'page-series.php'],
        ['title' => 'Ao Vivo',               'slug' => 'ao-vivo',             'template' => 'page-ao-vivo.php'],
        ['title' => 'Minha Lista',           'slug' => 'minha-lista',         'template' => 'page-minha-lista.php'],
        ['title' => 'Minha Conta',           'slug' => 'minha-conta',         'template' => 'page-minha-conta.php'],
        ['title' => 'Área do Assinante',     'slug' => 'area-do-assinante',   'template' => 'page-area-do-assinante.php'],
        ['title' => 'Planos',                'slug' => 'planos',              'template' => 'page-planos.php'],
        ['title' => 'Checkout',              'slug' => 'checkout',            'template' => 'page-checkout.php'],
        ['title' => 'Busca',                 'slug' => 'busca',               'template' => 'page-busca.php'],
        ['title' => 'Continuar Assistindo',  'slug' => 'continuar-assistindo','template' => 'page-continuar-assistindo.php'],
        ['title' => 'Contato',               'slug' => 'contato',             'template' => 'page-contato.php'],
        ['title' => 'Sobre a F5 TV',         'slug' => 'sobre',               'template' => 'page-sobre.php'],
        ['title' => 'Política de Privacidade','slug' => 'privacidade',        'template' => 'page-privacidade.php'],
    ];

    $created = 0; $existed = 0;
    $home_id = 0;

    foreach ($pages as $p) {
        $existing = get_page_by_path($p['slug'], OBJECT, 'page');
        if ($existing) {
            $existed++;
            if ($p['slug'] === 'home') $home_id = $existing->ID;
            if ($p['template']) {
                update_post_meta($existing->ID, '_wp_page_template', $p['template']);
            }
            continue;
        }

        $id = wp_insert_post([
            'post_title'   => $p['title'],
            'post_name'    => $p['slug'],
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '',
            'post_author'  => 1,
        ]);

        if ($id && !is_wp_error($id)) {
            if ($p['template']) update_post_meta($id, '_wp_page_template', $p['template']);
            if ($p['slug'] === 'home') $home_id = $id;
            $created++;
        }
    }

    // Definir página inicial
    if ($home_id) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $home_id);
    }

    wp_send_json_success([
        'created' => $created,
        'existed' => $existed,
        'message' => "$created páginas criadas, $existed já existiam.",
    ]);
});

// Configurar permalinks
add_action('wp_ajax_f5tv_configure_permalinks', function () {
    check_ajax_referer('f5tv_setup_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('Permissão negada');

    global $wp_rewrite;
    $wp_rewrite->set_permalink_structure('/%postname%/');
    $wp_rewrite->flush_rules(true);
    update_option('permalink_structure', '/%postname%/');

    wp_send_json_success(['message' => 'Permalinks configurados como "/%postname%/"']);
});

// Copiar manifest.json + sw.js para raiz
add_action('wp_ajax_f5tv_copy_pwa', function () {
    check_ajax_referer('f5tv_setup_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('Permissão negada');

    $root   = ABSPATH;
    $theme  = get_template_directory();
    $copied = [];
    $errors = [];

    $files = [
        $theme . '/manifest.json' => $root . 'manifest.json',
        $theme . '/sw.js'         => $root . 'sw.js',
    ];

    foreach ($files as $src => $dst) {
        if (file_exists($src)) {
            if (copy($src, $dst)) {
                $copied[] = basename($dst);
            } else {
                $errors[] = basename($dst) . ' (sem permissão de escrita)';
            }
        } else {
            // Ficheiro pode estar na raiz do site (caso já foi copiado antes)
            $altSrc = $root . basename($src);
            if (file_exists($altSrc)) {
                $copied[] = basename($dst) . ' (já existe na raiz)';
            } else {
                $errors[] = basename($dst) . ' (source não encontrado)';
            }
        }
    }

    if ($errors) {
        wp_send_json_success([
            'message' => 'Parcialmente copiado. Erros: ' . implode(', ', $errors) . '. Copie manualmente via FTP.',
            'copied'  => $copied,
        ]);
    }

    wp_send_json_success(['message' => 'Copiados: ' . implode(', ', $copied), 'copied' => $copied]);
});

// Finalizar — desativar o plugin de setup
add_action('wp_ajax_f5tv_finish_setup', function () {
    check_ajax_referer('f5tv_setup_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('Permissão negada');

    update_option('f5tv_setup_complete', true);
    update_option('f5tv_setup_date', current_time('mysql'));

    // Desativar este plugin de setup
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    deactivate_plugins(plugin_basename(__FILE__));

    wp_send_json_success(['redirect' => admin_url()]);
});

/* ──────────────────────────────────────────────
   HELPER: encontrar ficheiro principal de plugin
────────────────────────────────────────────── */
function f5tv_find_plugin_file(string $slug): string
{
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    $all = get_plugins();
    foreach ($all as $file => $data) {
        if (strpos($file, $slug . '/') === 0) return $file;
    }
    return '';
}

/* ──────────────────────────────────────────────
   VERIFICAR STATUS DOS PLUGINS
────────────────────────────────────────────── */
function f5tv_plugin_status(string $slug_or_file): string
{
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    $all = get_plugins();
    foreach ($all as $file => $data) {
        if ($file === $slug_or_file || strpos($file, $slug_or_file . '/') === 0) {
            return is_plugin_active($file) ? 'active' : 'inactive';
        }
    }
    return 'not_installed';
}

/* ──────────────────────────────────────────────
   RENDER WIZARD PAGE
────────────────────────────────────────────── */
function f5tv_setup_render_page(): void
{
    $nonce = wp_create_nonce('f5tv_setup_nonce');

    $s_elementor     = f5tv_plugin_status('elementor');
    $s_admin_panel   = f5tv_plugin_status('f5tv-admin-panel');
    $s_client_area   = f5tv_plugin_status('f5tv-client-area');
    $theme_active    = (get_stylesheet() === 'f5tv-theme') ? 'active' : 'inactive';
    $permalinks_ok   = (get_option('permalink_structure') === '/%postname%/') ? true : false;
    $setup_complete  = get_option('f5tv_setup_complete', false);

    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>F5TV — Setup Wizard</title>
        <style>
            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
            body {
                background: #030315;
                color: #fff;
                font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                align-items: center;
                padding: 40px 16px 80px;
            }
            /* Glow */
            body::before {
                content: '';
                position: fixed;
                top: -200px;
                left: 50%;
                transform: translateX(-50%);
                width: 800px;
                height: 600px;
                background: radial-gradient(ellipse, rgba(220,38,38,.15) 0%, transparent 70%);
                pointer-events: none;
            }

            .logo {
                font-size: 32px;
                font-weight: 900;
                letter-spacing: -.04em;
                text-transform: uppercase;
                margin-bottom: 8px;
            }
            .logo span { color: #dc2626; }
            .subtitle { color: #71717a; font-size: 14px; margin-bottom: 48px; }

            /* Steps indicator */
            .steps {
                display: flex;
                align-items: center;
                gap: 0;
                margin-bottom: 40px;
            }
            .step-dot {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 6px;
            }
            .step-dot .circle {
                width: 36px;
                height: 36px;
                border-radius: 50%;
                border: 2px solid #27272a;
                background: #0a0a1a;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 13px;
                font-weight: 700;
                color: #52525b;
                transition: all .3s;
                position: relative;
                z-index: 1;
            }
            .step-dot .label {
                font-size: 10px;
                color: #52525b;
                white-space: nowrap;
                font-family: monospace;
                text-transform: uppercase;
                letter-spacing: .05em;
                transition: color .3s;
            }
            .step-dot.active .circle   { border-color: #dc2626; background: #dc2626; color: #fff; }
            .step-dot.active .label    { color: #dc2626; }
            .step-dot.done .circle     { border-color: #16a34a; background: #052e16; color: #4ade80; }
            .step-dot.done .label      { color: #4ade80; }
            .step-line {
                flex: 1;
                height: 2px;
                background: #18181b;
                min-width: 32px;
                margin-bottom: 22px;
                transition: background .3s;
            }
            .step-line.done { background: #16a34a; }

            /* Card */
            .card {
                width: 100%;
                max-width: 640px;
                background: #0a0a1a;
                border: 1px solid #18181b;
                border-radius: 20px;
                padding: 36px;
                position: relative;
            }

            /* Panels */
            .panel { display: none; }
            .panel.active { display: block; animation: fadeUp .35s ease both; }
            @keyframes fadeUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }

            h2 {
                font-size: 22px;
                font-weight: 800;
                margin-bottom: 6px;
                color: #fff;
            }
            .panel-desc { color: #71717a; font-size: 13px; margin-bottom: 28px; line-height: 1.6; }

            /* Item de checklist */
            .check-item {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 14px 16px;
                border-radius: 10px;
                border: 1px solid #18181b;
                background: #060613;
                margin-bottom: 10px;
                gap: 12px;
            }
            .check-item .info { flex: 1; }
            .check-item .info strong { font-size: 14px; display: block; margin-bottom: 2px; }
            .check-item .info small { color: #52525b; font-size: 11px; font-family: monospace; }
            .badge {
                font-size: 10px;
                font-family: monospace;
                font-weight: 700;
                text-transform: uppercase;
                padding: 4px 10px;
                border-radius: 20px;
                white-space: nowrap;
                flex-shrink: 0;
            }
            .badge.active   { background: #052e16; color: #4ade80; border: 1px solid #166534; }
            .badge.inactive { background: #27272a; color: #a1a1aa; border: 1px solid #3f3f46; }
            .badge.missing  { background: #1f0a0a; color: #f87171; border: 1px solid #7f1d1d; }
            .badge.loading  { background: #1a1a0a; color: #fbbf24; border: 1px solid #78350f; animation: pulse 1s infinite; }
            @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.5} }

            /* Buttons */
            .btn {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 12px 24px;
                border-radius: 10px;
                font-size: 13px;
                font-weight: 700;
                font-family: monospace;
                text-transform: uppercase;
                letter-spacing: .08em;
                border: none;
                cursor: pointer;
                transition: all .2s;
            }
            .btn-red  { background: #dc2626; color: #fff; }
            .btn-red:hover  { background: #b91c1c; }
            .btn-ghost { background: transparent; color: #71717a; border: 1px solid #27272a; }
            .btn-ghost:hover { border-color: #52525b; color: #a1a1aa; }
            .btn-green { background: #16a34a; color: #fff; }
            .btn-green:hover { background: #15803d; }
            .btn:disabled { opacity: .5; cursor: not-allowed; }

            .btn-row {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 10px;
                margin-top: 28px;
                padding-top: 20px;
                border-top: 1px solid #18181b;
            }

            /* Log */
            .log {
                background: #000;
                border: 1px solid #18181b;
                border-radius: 8px;
                padding: 12px 16px;
                font-size: 12px;
                font-family: monospace;
                color: #71717a;
                min-height: 60px;
                margin-top: 16px;
                line-height: 1.7;
                display: none;
            }
            .log.visible { display: block; }
            .log .ok   { color: #4ade80; }
            .log .err  { color: #f87171; }
            .log .info { color: #60a5fa; }

            /* Final */
            .final-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
                margin: 20px 0;
            }
            .final-card {
                background: #060613;
                border: 1px solid #18181b;
                border-radius: 10px;
                padding: 16px;
                text-align: center;
            }
            .final-card .icon { font-size: 24px; margin-bottom: 6px; }
            .final-card strong { font-size: 13px; display: block; }
            .final-card small { color: #52525b; font-size: 11px; }
        </style>
    </head>
    <body>

        <div class="logo">F5 <span>TV</span></div>
        <p class="subtitle">Assistente de Instalação</p>

        <!-- Steps indicator -->
        <div class="steps" id="steps-indicator">
            <div class="step-dot active" data-step="1">
                <div class="circle">1</div>
                <span class="label">Bem-vindo</span>
            </div>
            <div class="step-line" id="line-1"></div>
            <div class="step-dot" data-step="2">
                <div class="circle">2</div>
                <span class="label">Tema</span>
            </div>
            <div class="step-line" id="line-2"></div>
            <div class="step-dot" data-step="3">
                <div class="circle">3</div>
                <span class="label">Plugins</span>
            </div>
            <div class="step-line" id="line-3"></div>
            <div class="step-dot" data-step="4">
                <div class="circle">4</div>
                <span class="label">Páginas</span>
            </div>
            <div class="step-line" id="line-4"></div>
            <div class="step-dot" data-step="5">
                <div class="circle">5</div>
                <span class="label">PWA</span>
            </div>
            <div class="step-line" id="line-5"></div>
            <div class="step-dot" data-step="6">
                <div class="circle">6</div>
                <span class="label">Pronto</span>
            </div>
        </div>

        <div class="card">

            <!-- ─── PASSO 1: BEM-VINDO ─── -->
            <div class="panel active" id="panel-1">
                <h2>👋 Bem-vindo ao F5 TV</h2>
                <p class="panel-desc">
                    Este assistente vai configurar tudo automaticamente:<br>
                    tema, plugins, páginas, permalinks e PWA.<br>
                    Duração estimada: <strong style="color:#fff;">menos de 1 minuto.</strong>
                </p>
                <div class="check-item">
                    <div class="info">
                        <strong>WordPress</strong>
                        <small><?php echo get_bloginfo('version'); ?></small>
                    </div>
                    <span class="badge active">✓ OK</span>
                </div>
                <div class="check-item">
                    <div class="info">
                        <strong>PHP</strong>
                        <small><?php echo PHP_VERSION; ?></small>
                    </div>
                    <span class="badge <?php echo version_compare(PHP_VERSION, '8.1', '>=') ? 'active' : 'missing'; ?>">
                        <?php echo version_compare(PHP_VERSION, '8.1', '>=') ? '✓ OK' : '✗ PHP 8.1+ necessário'; ?>
                    </span>
                </div>
                <div class="check-item">
                    <div class="info">
                        <strong>Memória PHP</strong>
                        <small>Recomendado: 256 MB</small>
                    </div>
                    <span class="badge active"><?php echo ini_get('memory_limit'); ?></span>
                </div>
                <div class="btn-row">
                    <button class="btn btn-red" onclick="goTo(2)">
                        Começar →
                    </button>
                </div>
            </div>

            <!-- ─── PASSO 2: TEMA ─── -->
            <div class="panel" id="panel-2">
                <h2>🎨 Tema F5TV</h2>
                <p class="panel-desc">Verifique se o tema está ativo e configurado como tema principal.</p>

                <div class="check-item">
                    <div class="info">
                        <strong>f5tv-theme</strong>
                        <small>Tema principal F5 TV Streaming</small>
                    </div>
                    <span class="badge <?php echo $theme_active === 'active' ? 'active' : 'missing'; ?>" id="badge-theme">
                        <?php echo $theme_active === 'active' ? '✓ Ativo' : '✗ Inativo'; ?>
                    </span>
                </div>

                <div class="check-item">
                    <div class="info">
                        <strong>Permalinks</strong>
                        <small>Estrutura: /nome-do-post/</small>
                    </div>
                    <span class="badge <?php echo $permalinks_ok ? 'active' : 'inactive'; ?>" id="badge-permalinks">
                        <?php echo $permalinks_ok ? '✓ Configurado' : '— Não configurado'; ?>
                    </span>
                </div>

                <div class="log" id="log-2"></div>

                <div class="btn-row">
                    <button class="btn btn-ghost" onclick="goTo(1)">← Voltar</button>
                    <?php if (!$permalinks_ok): ?>
                    <button class="btn btn-ghost" id="btn-fix-permalinks" onclick="fixPermalinks()">
                        Corrigir Permalinks
                    </button>
                    <?php endif; ?>
                    <button class="btn btn-red" onclick="goTo(3)">Próximo →</button>
                </div>
            </div>

            <!-- ─── PASSO 3: PLUGINS ─── -->
            <div class="panel" id="panel-3">
                <h2>🔌 Plugins necessários</h2>
                <p class="panel-desc">Clique em "Instalar Tudo" para instalar e ativar automaticamente.</p>

                <div class="check-item" id="item-elementor">
                    <div class="info">
                        <strong>Elementor</strong>
                        <small>elementor/elementor.php · wordpress.org</small>
                    </div>
                    <span class="badge <?php echo $s_elementor === 'active' ? 'active' : ($s_elementor === 'inactive' ? 'inactive' : 'missing'); ?>" id="badge-elementor">
                        <?php echo $s_elementor === 'active' ? '✓ Ativo' : ($s_elementor === 'inactive' ? 'Inativo' : '✗ Não instalado'); ?>
                    </span>
                </div>

                <div class="check-item" id="item-admin">
                    <div class="info">
                        <strong>F5TV Admin Panel</strong>
                        <small>f5tv-admin-panel/f5tv-admin-panel.php · custom</small>
                    </div>
                    <span class="badge <?php echo $s_admin_panel === 'active' ? 'active' : ($s_admin_panel === 'inactive' ? 'inactive' : 'missing'); ?>" id="badge-admin">
                        <?php echo $s_admin_panel === 'active' ? '✓ Ativo' : ($s_admin_panel === 'inactive' ? 'Inativo' : '✗ Não instalado'); ?>
                    </span>
                </div>

                <div class="check-item" id="item-client">
                    <div class="info">
                        <strong>F5TV Client Area</strong>
                        <small>f5tv-client-area/f5tv-client-area.php · custom</small>
                    </div>
                    <span class="badge <?php echo $s_client_area === 'active' ? 'active' : ($s_client_area === 'inactive' ? 'inactive' : 'missing'); ?>" id="badge-client">
                        <?php echo $s_client_area === 'active' ? '✓ Ativo' : ($s_client_area === 'inactive' ? 'Inativo' : '✗ Não instalado'); ?>
                    </span>
                </div>

                <div class="log" id="log-3"></div>

                <div class="btn-row">
                    <button class="btn btn-ghost" onclick="goTo(2)">← Voltar</button>
                    <button class="btn btn-ghost" id="btn-install-all" onclick="installAll()">
                        ⬇ Instalar Tudo
                    </button>
                    <button class="btn btn-red" onclick="goTo(4)">Próximo →</button>
                </div>
            </div>

            <!-- ─── PASSO 4: PÁGINAS ─── -->
            <div class="panel" id="panel-4">
                <h2>📄 Páginas do site</h2>
                <p class="panel-desc">
                    Cria automaticamente todas as páginas necessárias com os templates corretos
                    e define a página inicial.
                </p>

                <div class="check-item">
                    <div class="info">
                        <strong>16 páginas</strong>
                        <small>/login · /assista · /series · /ao-vivo · /minha-lista · /area-do-assinante · ...</small>
                    </div>
                    <span class="badge inactive" id="badge-pages">— Pendente</span>
                </div>

                <div class="check-item">
                    <div class="info">
                        <strong>Página inicial</strong>
                        <small>Configurar "Página Inicial F5 TV" como front page</small>
                    </div>
                    <span class="badge inactive" id="badge-front">— Pendente</span>
                </div>

                <div class="log" id="log-4"></div>

                <div class="btn-row">
                    <button class="btn btn-ghost" onclick="goTo(3)">← Voltar</button>
                    <button class="btn btn-ghost" id="btn-create-pages" onclick="createPages()">
                        🗂 Criar Páginas
                    </button>
                    <button class="btn btn-red" onclick="goTo(5)">Próximo →</button>
                </div>
            </div>

            <!-- ─── PASSO 5: PWA ─── -->
            <div class="panel" id="panel-5">
                <h2>📱 App Web (PWA)</h2>
                <p class="panel-desc">
                    Copia o <code style="color:#60a5fa;font-size:12px;">manifest.json</code> e o
                    <code style="color:#60a5fa;font-size:12px;">sw.js</code> para a raiz do servidor,
                    necessários para "Adicionar à tela inicial" no Android/iOS/Windows.
                </p>

                <div class="check-item">
                    <div class="info">
                        <strong>manifest.json</strong>
                        <small>Metadados do app — nome, ícones, cores</small>
                    </div>
                    <span class="badge inactive" id="badge-manifest">— Pendente</span>
                </div>

                <div class="check-item">
                    <div class="info">
                        <strong>sw.js</strong>
                        <small>Service Worker — cache offline</small>
                    </div>
                    <span class="badge inactive" id="badge-sw">— Pendente</span>
                </div>

                <div class="log" id="log-5"></div>

                <div class="btn-row">
                    <button class="btn btn-ghost" onclick="goTo(4)">← Voltar</button>
                    <button class="btn btn-ghost" id="btn-copy-pwa" onclick="copyPWA()">
                        📋 Copiar Ficheiros
                    </button>
                    <button class="btn btn-red" onclick="goTo(6)">Próximo →</button>
                </div>
            </div>

            <!-- ─── PASSO 6: CONCLUÍDO ─── -->
            <div class="panel" id="panel-6">
                <h2>🎉 Instalação Concluída!</h2>
                <p class="panel-desc">O F5 TV está pronto. Este assistente será desativado automaticamente.</p>

                <div class="final-grid">
                    <div class="final-card">
                        <div class="icon">🌐</div>
                        <strong>Ver Site</strong>
                        <small>Página inicial pública</small>
                    </div>
                    <div class="final-card">
                        <div class="icon">⚙️</div>
                        <strong>Painel WP</strong>
                        <small>Configurações avançadas</small>
                    </div>
                    <div class="final-card">
                        <div class="icon">📱</div>
                        <strong>Instalar App</strong>
                        <small>PWA na tela inicial</small>
                    </div>
                    <div class="final-card">
                        <div class="icon">🔒</div>
                        <strong>Login</strong>
                        <small>/login — novo visual</small>
                    </div>
                </div>

                <div class="btn-row" style="justify-content:center; gap:12px;">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-ghost" style="text-decoration:none;">
                        🌐 Ver Site
                    </a>
                    <button class="btn btn-green" id="btn-finish" onclick="finishSetup()">
                        ✓ Concluir e Fechar Wizard
                    </button>
                </div>
            </div>

        </div><!-- .card -->

    <script>
    var NONCE   = '<?php echo $nonce; ?>';
    var AJAX    = '<?php echo admin_url('admin-ajax.php'); ?>';
    var current = 1;

    function goTo(step) {
        // Marcar step anterior como done
        var prev = document.querySelector('.step-dot[data-step="' + current + '"]');
        if (prev && step > current) {
            prev.classList.remove('active');
            prev.classList.add('done');
            prev.querySelector('.circle').textContent = '✓';
            var line = document.getElementById('line-' + current);
            if (line) line.classList.add('done');
        }

        // Esconder painel actual
        document.getElementById('panel-' + current).classList.remove('active');

        // Ativar novo passo
        current = step;
        var panel = document.getElementById('panel-' + step);
        panel.classList.add('active');

        // Atualizar indicator
        document.querySelectorAll('.step-dot').forEach(function(d) {
            var s = parseInt(d.dataset.step);
            d.classList.remove('active');
            if (s === step) d.classList.add('active');
        });

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function log(panelNum, msg, type) {
        var el = document.getElementById('log-' + panelNum);
        el.classList.add('visible');
        el.innerHTML += '<div class="' + (type || 'info') + '">' + msg + '</div>';
        el.scrollTop = el.scrollHeight;
    }

    function setBadge(id, text, type) {
        var el = document.getElementById(id);
        if (!el) return;
        el.textContent = text;
        el.className = 'badge ' + type;
    }

    function ajax(action, extra, cb) {
        var data = new FormData();
        data.append('action', action);
        data.append('nonce', NONCE);
        if (extra) Object.keys(extra).forEach(function(k) { data.append(k, extra[k]); });

        fetch(AJAX, { method: 'POST', body: data })
            .then(function(r) { return r.json(); })
            .then(function(res) { cb(null, res); })
            .catch(function(err) { cb(err); });
    }

    /* ── Fix Permalinks ── */
    function fixPermalinks() {
        var btn = document.getElementById('btn-fix-permalinks');
        btn.disabled = true; btn.textContent = '⏳ A configurar...';
        log(2, '→ Configurando permalinks...', 'info');

        ajax('f5tv_configure_permalinks', {}, function(err, res) {
            if (err || !res.success) {
                log(2, '✗ Erro: ' + (res?.data || err), 'err');
                btn.disabled = false; btn.textContent = 'Corrigir Permalinks';
                return;
            }
            setBadge('badge-permalinks', '✓ Configurado', 'active');
            log(2, '✓ ' + res.data.message, 'ok');
            btn.style.display = 'none';
        });
    }

    /* ── Install All Plugins ── */
    function installAll() {
        var btn = document.getElementById('btn-install-all');
        btn.disabled = true; btn.textContent = '⏳ A instalar...';
        log(3, '→ Iniciando instalação de plugins...', 'info');

        var tasks = [
            { action: 'f5tv_install_plugin',  slug: 'elementor',         badge: 'badge-elementor', label: 'Elementor' },
            { action: 'f5tv_activate_plugin',  file: 'f5tv-admin-panel/f5tv-admin-panel.php', badge: 'badge-admin',  label: 'F5TV Admin Panel' },
            { action: 'f5tv_activate_plugin',  file: 'f5tv-client-area/f5tv-client-area.php', badge: 'badge-client', label: 'F5TV Client Area' },
        ];

        function runNext(i) {
            if (i >= tasks.length) {
                btn.textContent = '✓ Concluído';
                log(3, '✓ Todos os plugins processados.', 'ok');
                return;
            }
            var t = tasks[i];
            setBadge(t.badge, '⏳ Instalando...', 'loading');
            log(3, '→ Processando: ' + t.label + '...', 'info');

            ajax(t.action, t.slug ? { slug: t.slug } : { file: t.file }, function(err, res) {
                if (err || !res.success) {
                    setBadge(t.badge, '✗ Erro', 'missing');
                    log(3, '✗ ' + t.label + ': ' + (res?.data || err), 'err');
                } else {
                    setBadge(t.badge, '✓ Ativo', 'active');
                    log(3, '✓ ' + t.label + ': ' + res.data.message, 'ok');
                }
                runNext(i + 1);
            });
        }
        runNext(0);
    }

    /* ── Create Pages ── */
    function createPages() {
        var btn = document.getElementById('btn-create-pages');
        btn.disabled = true; btn.textContent = '⏳ A criar...';
        setBadge('badge-pages', '⏳ Criando...', 'loading');
        setBadge('badge-front', '⏳ Configurando...', 'loading');
        log(4, '→ Criando páginas e configurando página inicial...', 'info');

        ajax('f5tv_create_pages', {}, function(err, res) {
            if (err || !res.success) {
                setBadge('badge-pages', '✗ Erro', 'missing');
                log(4, '✗ ' + (res?.data || err), 'err');
                btn.disabled = false; btn.textContent = '🗂 Criar Páginas';
                return;
            }
            setBadge('badge-pages', '✓ Criadas', 'active');
            setBadge('badge-front', '✓ Configurada', 'active');
            log(4, '✓ ' + res.data.message, 'ok');
            btn.textContent = '✓ Concluído';
        });
    }

    /* ── Copy PWA Files ── */
    function copyPWA() {
        var btn = document.getElementById('btn-copy-pwa');
        btn.disabled = true; btn.textContent = '⏳ A copiar...';
        setBadge('badge-manifest', '⏳ Copiando...', 'loading');
        setBadge('badge-sw', '⏳ Copiando...', 'loading');
        log(5, '→ Copiando ficheiros PWA para a raiz...', 'info');

        ajax('f5tv_copy_pwa', {}, function(err, res) {
            if (err || !res.success) {
                setBadge('badge-manifest', '✗ Erro', 'missing');
                setBadge('badge-sw', '✗ Erro', 'missing');
                log(5, '✗ ' + (res?.data || err), 'err');
                btn.disabled = false; btn.textContent = '📋 Tentar novamente';
                return;
            }
            setBadge('badge-manifest', '✓ Copiado', 'active');
            setBadge('badge-sw', '✓ Copiado', 'active');
            log(5, '✓ ' + res.data.message, 'ok');
            btn.textContent = '✓ Concluído';
        });
    }

    /* ── Finish ── */
    function finishSetup() {
        var btn = document.getElementById('btn-finish');
        btn.disabled = true; btn.textContent = '⏳ A finalizar...';

        ajax('f5tv_finish_setup', {}, function(err, res) {
            if (err || !res.success) {
                btn.disabled = false; btn.textContent = '✓ Concluir';
                return;
            }
            window.location.href = res.data.redirect;
        });
    }
    </script>

    </body>
    </html>
    <?php
}
