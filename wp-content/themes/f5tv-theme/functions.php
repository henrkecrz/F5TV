<?php
/**
 * F5TV Theme - Functions.php
 * Configuração principal do tema
 */

if (!defined('ABSPATH')) {
    exit;
}

define('F5TV_THEME_VERSION', '1.0.2');
define('F5TV_THEME_DIR', get_template_directory());
define('F5TV_THEME_URI', get_template_directory_uri());
define('F5TV_ASSETS_DIR', F5TV_THEME_DIR . '/assets');
define('F5TV_ASSETS_URI', F5TV_THEME_URI . '/assets');

// Carregar dependências do Composer (se usar)
if (file_exists(F5TV_THEME_DIR . '/vendor/autoload.php')) {
    require_once F5TV_THEME_DIR . '/vendor/autoload.php';
}

// Componentes reutilizáveis
require_once F5TV_THEME_DIR . '/inc/minha-lista-button.php';

/**
 * Fallback de segurança para get_field() quando o ACF não estiver ativado
 */
if (!function_exists('get_field')) {
    function get_field($selector, $post_id = false, $format_value = true) {
        $id = $post_id;
        if (!$id) {
            $id = get_the_ID();
        }
        if (is_object($id) && isset($id->ID)) {
            $id = $id->ID;
        }
        if (!is_numeric($id)) {
            return null;
        }
        $val = get_post_meta($id, $selector, true);
        return ($val !== '') ? $val : null;
    }
}

if (!function_exists('f5tv_get_field')) {
    function f5tv_get_field($selector, $post_id = false, $default = null) {
        $val = get_field($selector, $post_id);
        return ($val !== null && $val !== false && $val !== '') ? $val : $default;
    }
}

/**
 * Funções auxiliares para parser de URLs de Vídeo (Vimeo, YouTube, MP4, HLS)
 */
if (!function_exists('f5tv_get_vimeo_id')) {
    function f5tv_get_vimeo_id(string $url): string {
        if (preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/[^\/]*\/videos\/|album\/\d+\/video\/|video\/|)(\d+)/i', $url, $matches)) {
            return $matches[1];
        }
        if (preg_match('/player\.vimeo\.com\/video\/(\d+)/i', $url, $matches)) {
            return $matches[1];
        }
        return '';
    }
}

if (!function_exists('f5tv_get_youtube_id')) {
    function f5tv_get_youtube_id(string $url): string {
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $url, $matches)) {
            return $matches[1];
        }
        return '';
    }
}

if (!function_exists('f5tv_render_video_player')) {
    function f5tv_render_video_player(string $video_url, string $poster_url = '', string $title = ''): string {
        $vimeo_id = f5tv_get_vimeo_id($video_url);
        if (!empty($vimeo_id)) {
            return sprintf(
                '<iframe src="https://player.vimeo.com/video/%s?autoplay=1&title=0&byline=0&portrait=0" class="w-full h-full border-0" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen title="%s"></iframe>',
                esc_attr($vimeo_id),
                esc_attr($title)
            );
        }

        $youtube_id = f5tv_get_youtube_id($video_url);
        if (!empty($youtube_id)) {
            return sprintf(
                '<iframe src="https://www.youtube.com/embed/%s?autoplay=1&rel=0" class="w-full h-full border-0" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen title="%s"></iframe>',
                esc_attr($youtube_id),
                esc_attr($title)
            );
        }

        // Default HTML5 MP4 / HLS Player
        return sprintf(
            '<video id="f5tv-main-player" controls autoplay poster="%s" class="w-full h-full object-cover">
                <source src="%s" type="video/mp4">
                Seu navegador não suporta a reprodução deste formato.
            </video>',
            esc_url($poster_url),
            esc_url($video_url)
        );
    }
}

/**
 * Inicialização do tema
 */
add_action('after_setup_theme', 'f5tv_theme_setup');
function f5tv_theme_setup(): void
{
    // Suporte a recursos modernos do WordPress
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'script', 'style']);
    add_theme_support('align-wide');
    add_theme_support('responsive-embeds');
    add_theme_support('wp-block-styles');
    add_theme_support('editor-styles');
    add_editor_style('assets/dist/css/editor.css');

    // Custom Logo
    add_theme_support('custom-logo', [
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
        'unlink-homepage-logo' => true,
    ]);

    // Registrar navegação
    register_nav_menus([
        'primary'   => __('Menu Principal', 'f5tv-theme'),
        'footer'    => __('Menu Rodapé', 'f5tv-theme'),
        'account'   => __('Menu Conta', 'f5tv-theme'),
    ]);

    // Image sizes
    add_image_size('f5tv-hero', 1920, 1080, true);
    add_image_size('f5tv-content-cover', 300, 450, true);
    add_image_size('f5tv-content-banner', 1280, 720, true);
    add_image_size('f5tv-channel-logo', 120, 120, true);
    add_image_size('f5tv-episode-thumb', 320, 180, true);
}

/**
 * Enqueue scripts e styles
 */
add_action('wp_enqueue_scripts', 'f5tv_enqueue_assets');
function f5tv_enqueue_assets(): void
{
    $version = F5TV_THEME_VERSION;

    // CSS principal (Tailwind compilado)
    wp_enqueue_style('f5tv-main-css', F5TV_ASSETS_URI . '/dist/css/style.css', [], $version);
    wp_enqueue_style('f5tv-mobile-fixes', F5TV_ASSETS_URI . '/dist/css/f5tv-mobile-fixes.css', ['f5tv-main-css'], $version . '.mobile');

    // JS principal (blocos Gutenberg leves)
    wp_enqueue_script('f5tv-main-js', F5TV_ASSETS_URI . '/dist/js/main.js', [], $version, true);

    // Localizar dados para JS
    wp_localize_script('f5tv-main-js', 'f5tvThemeData', [
        'ajaxUrl'        => admin_url('admin-ajax.php'),
        'nonce'          => wp_create_nonce('f5tv_nonce'),
        'restUrl'        => rest_url('f5tv/v1/'),
        'themeUri'       => F5TV_THEME_URI,
        'isUserLoggedIn' => is_user_logged_in(),
        'currentUser'    => wp_get_current_user()->ID,
        'swUrl'          => home_url('/sw.js'),
        'manifestUrl'    => home_url('/manifest.json'),
    ]);
}

/**
 * PWA — Registrar Service Worker + Banner de Instalação (inline no footer)
 */
add_action('wp_footer', 'f5tv_pwa_scripts', 99);
function f5tv_pwa_scripts(): void
{
    $icon_url = get_template_directory_uri() . '/assets/icons/icon-192.png';
    $sw_url   = home_url('/sw.js');
    ?>
    <script>
    (function () {
        'use strict';

        /* ── 1. Registrar Service Worker ── */
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('<?php echo esc_js($sw_url); ?>', { scope: '/' })
                    .then(function (reg) {
                        console.log('[F5TV SW] Registado:', reg.scope);
                    })
                    .catch(function (err) {
                        console.warn('[F5TV SW] Erro:', err);
                    });
            });
        }

        /* ── 2. Detectar plataforma ── */
        const ua        = navigator.userAgent || '';
        const isIOS     = /iphone|ipad|ipod/i.test(ua);
        const isAndroid = /android/i.test(ua);
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches
                          || window.navigator.standalone === true;

        /* Já instalado — não mostrar banner */
        if (isStandalone) return;

        /* Não mostrar se já rejeitou nas últimas 7 dias */
        const DISMISSED_KEY = 'f5tv_pwa_dismissed';
        const dismissed     = localStorage.getItem(DISMISSED_KEY);
        if (dismissed && Date.now() - parseInt(dismissed) < 7 * 24 * 60 * 60 * 1000) return;

        /* ── 3. Criar o banner HTML ── */
        function createBanner(isIOS) {
            const banner = document.createElement('div');
            banner.id    = 'f5tv-pwa-banner';
            banner.setAttribute('role', 'dialog');
            banner.setAttribute('aria-label', 'Instalar F5 TV');

            banner.innerHTML = `
                <div id="f5tv-pwa-inner">
                    <img src="<?php echo esc_js($icon_url); ?>" alt="F5 TV" id="f5tv-pwa-icon">
                    <div id="f5tv-pwa-text">
                        <strong>Instalar F5 TV</strong>
                        ${isIOS
                            ? '<span>Toque em <svg width="16" height="16" viewBox="0 0 24 24" fill="#dc2626" style="vertical-align:middle;margin:0 2px"><path d="M16 5l-1.42 1.42-1.59-1.59V16h-1.98V4.83L9.42 6.42 8 5l4-4 4 4zm4 5v11c0 1.1-.9 2-2 2H6c-1.11 0-2-.9-2-2V10c0-1.11.89-2 2-2h3v2H6v11h12V10h-3V8h3c1.1 0 2 .89 2 2z"/></svg> e depois <b>"Adicionar à Tela de Início"</b></span>'
                            : '<span>Adicione à tela inicial para acesso rápido, como um app nativo</span>'
                        }
                    </div>
                    <div id="f5tv-pwa-actions">
                        ${isIOS ? '' : '<button id="f5tv-pwa-install">Instalar</button>'}
                        <button id="f5tv-pwa-dismiss">✕</button>
                    </div>
                </div>
            `;

            /* Estilos inline — independentes do Tailwind */
            banner.style.cssText = `
                position:fixed; bottom:0; left:0; right:0; z-index:9999;
                background:linear-gradient(135deg,#0f0f2e 0%,#1a0a1a 100%);
                border-top:1px solid rgba(220,38,38,.35);
                box-shadow:0 -8px 40px rgba(0,0,0,.6);
                padding:0; font-family:'Inter',ui-sans-serif,system-ui,sans-serif;
                animation:f5BannerSlide .4s cubic-bezier(.16,1,.3,1) both;
            `;

            const style = document.createElement('style');
            style.textContent = `
                @keyframes f5BannerSlide { from { transform:translateY(100%); opacity:0; } to { transform:translateY(0); opacity:1; } }

                #f5tv-pwa-inner {
                    display:flex; align-items:center; gap:14px;
                    max-width:640px; margin:0 auto; padding:14px 18px;
                }
                #f5tv-pwa-icon {
                    width:52px; height:52px; border-radius:12px;
                    flex-shrink:0; box-shadow:0 4px 16px rgba(0,0,0,.5);
                }
                #f5tv-pwa-text {
                    flex:1; display:flex; flex-direction:column; gap:3px;
                }
                #f5tv-pwa-text strong {
                    color:#fff; font-size:14px; font-weight:700;
                }
                #f5tv-pwa-text span {
                    color:#a1a1aa; font-size:12px; line-height:1.4;
                }
                #f5tv-pwa-text b { color:#dc2626; }
                #f5tv-pwa-actions {
                    display:flex; align-items:center; gap:8px; flex-shrink:0;
                }
                #f5tv-pwa-install {
                    background:#dc2626; color:#fff; border:none;
                    font-size:12px; font-weight:700; font-family:monospace;
                    text-transform:uppercase; letter-spacing:.08em;
                    padding:9px 18px; border-radius:8px;
                    cursor:pointer; transition:background .2s;
                    white-space:nowrap;
                }
                #f5tv-pwa-install:hover { background:#b91c1c; }
                #f5tv-pwa-dismiss {
                    background:transparent; border:1px solid rgba(255,255,255,.18);
                    color:#71717a; width:32px; height:32px; border-radius:50%;
                    cursor:pointer; font-size:14px; display:flex;
                    align-items:center; justify-content:center;
                    transition:color .2s, border-color .2s;
                }
                #f5tv-pwa-dismiss:hover { color:#fff; border-color:rgba(255,255,255,.4); }
            `;
            document.head.appendChild(style);
            return banner;
        }

        /* ── 4. Android: usar beforeinstallprompt ── */
        let deferredPrompt = null;

        if (isAndroid || (!isIOS && !isAndroid)) {
            window.addEventListener('beforeinstallprompt', function (e) {
                e.preventDefault();
                deferredPrompt = e;

                const banner = createBanner(false);
                document.body.appendChild(banner);

                const installBtn = document.getElementById('f5tv-pwa-install');
                const dismissBtn = document.getElementById('f5tv-pwa-dismiss');

                if (installBtn) {
                    installBtn.addEventListener('click', function () {
                        banner.remove();
                        deferredPrompt.prompt();
                        deferredPrompt.userChoice.then(function (choice) {
                            if (choice.outcome === 'accepted') {
                                console.log('[F5TV PWA] Instalado!');
                                localStorage.removeItem('<?php echo DISMISSED_KEY; ?>');
                            }
                            deferredPrompt = null;
                        });
                    });
                }

                if (dismissBtn) {
                    dismissBtn.addEventListener('click', function () {
                        banner.style.animation = 'none';
                        banner.style.transform = 'translateY(100%)';
                        banner.style.opacity = '0';
                        banner.style.transition = 'transform .3s, opacity .3s';
                        setTimeout(() => banner.remove(), 300);
                        localStorage.setItem('<?php echo esc_js(DISMISSED_KEY); ?>', Date.now());
                    });
                }
            });
        }

        /* ── 5. iOS: mostrar instrução após 3s ── */
        if (isIOS) {
            setTimeout(function () {
                const banner    = createBanner(true);
                document.body.appendChild(banner);
                const dismissBtn = document.getElementById('f5tv-pwa-dismiss');
                if (dismissBtn) {
                    dismissBtn.addEventListener('click', function () {
                        banner.style.transform = 'translateY(100%)';
                        banner.style.opacity = '0';
                        banner.style.transition = 'transform .3s, opacity .3s';
                        setTimeout(() => banner.remove(), 300);
                        localStorage.setItem('<?php echo esc_js(DISMISSED_KEY); ?>', Date.now());
                    });
                }
            }, 3000);
        }
    })();
    </script>
    <?php
}


/**
 * Enqueue editor styles
 */
add_action('enqueue_block_editor_assets', 'f5tv_enqueue_editor_assets');
function f5tv_enqueue_editor_assets(): void
{
    wp_enqueue_style('f5tv-editor-css', F5TV_ASSETS_URI . '/dist/css/editor.css', [], F5TV_THEME_VERSION);
    wp_enqueue_script('f5tv-blocks-js', F5TV_ASSETS_URI . '/dist/js/blocks.js', ['wp-blocks', 'wp-element', 'wp-components', 'wp-editor'], F5TV_THEME_VERSION, true);
}

/**
 * Carregar módulos do tema
 */
$theme_modules = [
    'inc/custom-post-types.php',
    'inc/taxonomies.php',
    'inc/acf-fields.php',
    'inc/rest-api.php',
    'inc/hooks.php',
    'inc/block-patterns.php',
    'inc/template-tags.php',
    'inc/elementor-widgets.php',
];

foreach ($theme_modules as $module) {
    $path = F5TV_THEME_DIR . '/' . $module;
    if (file_exists($path)) {
        require_once $path;
    }
}

/**
 * Suporte a ACF Pro (se instalado)
 */
if (function_exists('acf_add_local_field_groups')) {
    $acf_path = F5TV_THEME_DIR . '/inc/acf-json';
    if (is_dir($acf_path)) {
        add_filter('acf/settings/load_json', function($paths) use ($acf_path) {
            $paths[] = $acf_path;
            return $paths;
        });
        add_filter('acf/settings/save_json', function($path) use ($acf_path) {
            return $acf_path;
        });
    }
}

/**
 * Limpeza de head
 */
add_action('init', function() {
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
}, 1);

/**
 * Desativar emojis
 */
add_action('init', function() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
});

/**
 * Adicionar body classes customizadas
 */
add_filter('body_class', 'f5tv_body_classes');
function f5tv_body_classes(array $classes): array
{
    if (is_singular('f5tv_conteudo')) {
        $classes[] = 'f5tv-single-content';
        $content_type = get_field('content_type');
        if ($content_type) {
            $classes[] = 'f5tv-type-' . sanitize_html_class($content_type);
        }
    }
    if (is_post_type_archive('f5tv_conteudo') || is_tax('f5tv_categoria') || is_tax('f5tv_genero')) {
        $classes[] = 'f5tv-content-archive';
    }
    if (is_page_template('templates/page-landing.html')) {
        $classes[] = 'f5tv-landing-page';
    }
    return $classes;
}

/**
 * Salvar rating de avaliação (estrelas) submetido via formulário dos templates de série/conteúdo
 */
add_action('comment_post', function(int $comment_id, $comment_approved) {
    if (isset($_POST['f5tv_rating'])) {
        $rating = (int) $_POST['f5tv_rating'];
        $rating = max(1, min(5, $rating));
        add_comment_meta($comment_id, 'rating', $rating, true);
    }
}, 10, 2);

/**
 * Registrar image sizes para blocos
 */
add_filter('image_size_names_choose', 'f5tv_custom_image_sizes');
function f5tv_custom_image_sizes(array $sizes): array
{
    return array_merge($sizes, [
        'f5tv-hero'            => __('F5TV Hero (1920x1080)', 'f5tv-theme'),
        'f5tv-content-cover'   => __('F5TV Cover (300x450)', 'f5tv-theme'),
        'f5tv-content-banner'  => __('F5TV Banner (1280x720)', 'f5tv-theme'),
        'f5tv-channel-logo'    => __('F5TV Canal Logo (120x120)', 'f5tv-theme'),
        'f5tv-episode-thumb'   => __('F5TV Episode Thumb (320x180)', 'f5tv-theme'),
    ]);
}

/**
 * REST API — Minha Lista (Watchlist / Favorites)
 * GET    /wp-json/f5tv/v1/minha-lista          → retorna lista do user logado
 * POST   /wp-json/f5tv/v1/minha-lista/toggle   → adiciona ou remove um post_id
 */
add_action('rest_api_init', function () {

    // GET: retorna lista de IDs salvos
    register_rest_route('f5tv/v1', '/minha-lista', [
        'methods'             => 'GET',
        'callback'            => function (WP_REST_Request $req) {
            if (!is_user_logged_in()) {
                return new WP_Error('not_logged_in', 'Usuário não autenticado', ['status' => 401]);
            }
            $user_id = get_current_user_id();
            $list    = get_user_meta($user_id, 'f5tv_minha_lista', true) ?: [];
            return rest_ensure_response(['list' => array_values(array_unique((array)$list))]);
        },
        'permission_callback' => '__return_true',
    ]);

    // POST: toggle add/remove
    register_rest_route('f5tv/v1', '/minha-lista/toggle', [
        'methods'             => 'POST',
        'callback'            => function (WP_REST_Request $req) {
            if (!is_user_logged_in()) {
                return new WP_Error('not_logged_in', 'Usuário não autenticado', ['status' => 401]);
            }

            $nonce = $req->get_header('X-WP-Nonce') ?: ($req->get_param('nonce') ?: '');
            if (!wp_verify_nonce($nonce, 'wp_rest')) {
                return new WP_Error('invalid_nonce', 'Nonce inválido', ['status' => 403]);
            }

            $post_id = (int) $req->get_param('post_id');
            if (!$post_id || !get_post($post_id)) {
                return new WP_Error('invalid_post', 'post_id inválido', ['status' => 400]);
            }

            $user_id = get_current_user_id();
            $list    = (array)(get_user_meta($user_id, 'f5tv_minha_lista', true) ?: []);
            $list    = array_map('intval', $list);

            if (in_array($post_id, $list, true)) {
                $list    = array_values(array_filter($list, fn($id) => $id !== $post_id));
                $action  = 'removed';
            } else {
                $list[]  = $post_id;
                $list    = array_values(array_unique($list));
                $action  = 'added';
            }

            update_user_meta($user_id, 'f5tv_minha_lista', $list);

            return rest_ensure_response([
                'action'  => $action,
                'post_id' => $post_id,
                'list'    => $list,
                'in_list' => in_array($post_id, $list, true),
            ]);
        },
        'permission_callback' => '__return_true',
    ]);
});

/**
 * Localizar nonce REST para uso no JS inline dos templates (single pages)
 * Adiciona f5tvRestNonce como variável JS global
 */
add_action('wp_head', function () {
    if (!is_singular(['f5tv_conteudo', 'f5tv_serie'])) return;
    $user_id  = get_current_user_id();
    $list     = $user_id ? (array)(get_user_meta($user_id, 'f5tv_minha_lista', true) ?: []) : [];
    echo '<script>window.f5tvRest=' . json_encode([
        'nonce'   => wp_create_nonce('wp_rest'),
        'restUrl' => esc_url(rest_url('f5tv/v1')),
        'myList'  => array_map('intval', $list),
        'userId'  => $user_id,
        'loginUrl'=> esc_url(home_url('/login/')),
    ]) . ';</script>' . "\n";
});

/**
 * ══════════════════════════════════════════
 *  LOGIN CUSTOMIZADO — /login
 * ══════════════════════════════════════════
 *
 * 1. Filtra login_url() para apontar para /login
 * 2. Redireciona wp-login.php → /login (exceto submissão de form e admin)
 * 3. Cria a página /login automaticamente se não existir
 * 4. Trata erros de autenticação com parâmetro amigável
 */

/**
 * 1. Substituir a URL de login em todo o WordPress
 */
add_filter('login_url', function ($login_url, $redirect, $force_reauth) {
    $custom = home_url('/login/');
    if ($redirect) {
        $custom = add_query_arg('redirect_to', urlencode($redirect), $custom);
    }
    return $custom;
}, 10, 3);

/**
 * 2. Redirecionar wp-login.php para /login
 *    Exceções: POST (submissão do form WP nativo), acção de logout, resetpassword
 */
add_action('init', function () {
    // Só actua em pedidos ao wp-login.php
    $request = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    if (strpos($request, 'wp-login.php') === false) return;

    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

    // Deixar passar: POST (form), logout, lostpassword, resetpass, confirmação de email
    $allow_actions = ['logout', 'lostpassword', 'rp', 'resetpass', 'postpass', 'confirm_admin_email'];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') return;
    if (in_array($action, $allow_actions)) return;

    // Tratar ?login=failed vindo do WP após falha
    // O WP redireciona para wp-login.php?login=failed — reenviar para /login?login=failed
    if (isset($_GET['login']) && $_GET['login'] === 'failed') {
        wp_redirect(home_url('/login/?login=failed'));
        exit;
    }

    // Redirecionar tudo o resto para /login
    wp_redirect(home_url('/login/'));
    exit;
});

/**
 * 3. Criar a página /login automaticamente se não existir
 */
add_action('after_switch_theme', 'f5tv_create_login_page');
add_action('init', 'f5tv_ensure_login_page_exists');

function f5tv_create_login_page(): void
{
    f5tv_ensure_login_page_exists();
}

function f5tv_ensure_login_page_exists(): void
{
    // Só correr uma vez por request (evitar loops)
    static $ran = false;
    if ($ran) return;
    $ran = true;

    // Verificar se já existe página com slug 'login'
    $existing = get_page_by_path('login', OBJECT, 'page');
    if ($existing) {
        // Garantir que tem o template certo
        $current_template = get_post_meta($existing->ID, '_wp_page_template', true);
        if ($current_template !== 'page-login.php') {
            update_post_meta($existing->ID, '_wp_page_template', 'page-login.php');
        }
        return;
    }

    // Criar a página
    $page_id = wp_insert_post([
        'post_title'   => 'Login',
        'post_name'    => 'login',
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_content' => '',
        'post_author'  => 1,
    ]);

    if ($page_id && !is_wp_error($page_id)) {
        update_post_meta($page_id, '_wp_page_template', 'page-login.php');
    }
}

/**
 * 4. Após falha de login WP (wp-login.php?login=failed),
 *    garantir que o redirect vai para /login?login=failed
 */
add_filter('login_redirect', function ($redirect_to, $requested_redirect_to, $user) {
    return $redirect_to;
}, 10, 3);

add_action('wp_login_failed', function ($username) {
    $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    // Só redirecionar se viemos do nosso form (não do wp-login.php nativo)
    if ($referrer && strpos($referrer, 'wp-login.php') === false) {
        wp_redirect(home_url('/login/?login=failed'));
        exit;
    }
});

/**
 * 5. Filtrar o link "Perdeu a senha?" para ficar em /login
 *    (WordPress usa wp-login.php?action=lostpassword por padrão)
 */
add_filter('lostpassword_url', function ($lostpassword_url, $redirect) {
    return home_url('/login/?action=lostpassword');
}, 10, 2);

/**
 * 6. FORÇAR template page-login.php quando o slug da página for 'login'
 *    Método mais confiável — não depende de post_meta
 */
add_filter('template_include', function ($template) {
    if (is_page('login')) {
        $custom = get_template_directory() . '/page-login.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }
    return $template;
}, 99);




