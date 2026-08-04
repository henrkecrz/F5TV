<?php
/**
 * Plugin Name: F5TV Client Area
 * Description: Área exclusiva do assinante da plataforma F5 TV, contendo perfis de usuários, player de vídeo integrado, histórico e favoritos.
 * Version: 1.0.0
 * Author: F5 TV Team
 * Text Domain: f5tv-client-area
 */

if (!defined('ABSPATH')) {
    exit;
}

define('F5TV_CLIENT_AREA_VERSION', '1.0.0');
define('F5TV_CLIENT_AREA_DIR', plugin_dir_path(__FILE__));
define('F5TV_CLIENT_AREA_URI', plugin_dir_url(__FILE__));

// Hooks de ativação e desativação
register_activation_hook(__FILE__, 'f5tv_client_area_activate');
register_deactivation_hook(__FILE__, 'f5tv_client_area_deactivate');

function f5tv_client_area_activate(): void
{
    require_once F5TV_CLIENT_AREA_DIR . 'database/install.php';
    f5tv_client_area_install_tables();
}

function f5tv_client_area_deactivate(): void
{
    // Nada por enquanto; manter dados entre desativações
}

// Carregar classes
require_once F5TV_CLIENT_AREA_DIR . 'includes/class-auth.php';
require_once F5TV_CLIENT_AREA_DIR . 'includes/class-subscription.php';
require_once F5TV_CLIENT_AREA_DIR . 'includes/class-rest-api.php';
require_once F5TV_CLIENT_AREA_DIR . 'includes/class-content-access.php';
require_once F5TV_CLIENT_AREA_DIR . 'includes/class-watch-history.php';
require_once F5TV_CLIENT_AREA_DIR . 'includes/class-my-list.php';
require_once F5TV_CLIENT_AREA_DIR . 'includes/class-devices.php';
require_once F5TV_CLIENT_AREA_DIR . 'includes/class-live-tv.php';
require_once F5TV_CLIENT_AREA_DIR . 'includes/class-checkout.php';

// Inicializar classes
add_action('plugins_loaded', function () {
    new F5TV_Auth();
    new F5TV_Subscription();
    new F5TV_Client_REST_API();
    new F5TV_Content_Access();
    new F5TV_Watch_History();
    new F5TV_My_List();
    new F5TV_Devices();
    new F5TV_Live_TV();
    new F5TV_Checkout();
});

// Enqueue assets apenas nas páginas do client area
add_action('wp_enqueue_scripts', 'f5tv_client_area_enqueue_assets');
function f5tv_client_area_enqueue_assets(): void
{
    if (!is_singular() || !has_shortcode(get_post()->post_content, 'f5tv_dashboard')) {
        return;
    }

    wp_enqueue_style('f5tv-client-area-css', F5TV_CLIENT_AREA_URI . 'assets/css/client-area.css', [], F5TV_CLIENT_AREA_VERSION);
    wp_enqueue_script('f5tv-client-area-js', F5TV_CLIENT_AREA_URI . 'assets/js/client-app.js', ['wp-element', 'wp-components'], F5TV_CLIENT_AREA_VERSION, true);

    wp_localize_script('f5tv-client-area-js', 'f5tvClientData', [
        'ajaxUrl'  => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('f5tv_client_nonce'),
        'restUrl'  => rest_url('f5tv/v1/'),
        'pluginUri'=> F5TV_CLIENT_AREA_URI,
        'userId'   => get_current_user_id(),
    ]);
}

// Shortcodes
add_shortcode('f5tv_dashboard', 'f5tv_shortcode_dashboard');
function f5tv_shortcode_dashboard(array $atts = [], ?string $content = null): string
{
    ob_start();
    $template = F5TV_CLIENT_AREA_DIR . 'templates/shortcode-dashboard.php';
    if (file_exists($template)) {
        include $template;
    } else {
        echo '<div id="f5tv-client-dashboard" class="f5tv-client-dashboard"><div id="f5tv-dashboard-content">Carregando...</div></div>';
    }
    return ob_get_clean();
}

add_shortcode('f5tv_player', 'f5tv_shortcode_player');
function f5tv_shortcode_player(array $atts = []): string
{
    $video_url = sanitize_text_field($atts['url'] ?? '');
    $poster_url = sanitize_text_field($atts['poster'] ?? '');
    $content_id = intval($atts['id'] ?? 0);

    if (!$video_url || !$content_id) {
        return '<p class="f5tv-player-error">Vídeo não encontrado.</p>';
    }

    ob_start();
    ?>
    <div class="f5tv-player-wrapper" data-content-id="<?php echo esc_attr($content_id); ?>">
        <video id="f5tv-player" controls poster="<?php echo esc_url($poster_url); ?>">
            <source src="<?php echo esc_url($video_url); ?>" type="application/x-mpegURL">
            Seu navegador não suporta reprodução de vídeo.
        </video>
    </div>
    <?php
    return ob_get_clean();
}

add_shortcode('f5tv_profile_selector', 'f5tv_shortcode_profile_selector');
function f5tv_shortcode_profile_selector(array $atts = []): string
{
    if (!is_user_logged_in()) {
        return '<p><a href="' . esc_url(wp_login_url()) . '">Faça login para selecionar um perfil.</a></p>';
    }

    $user_id = get_current_user_id();
    global $wpdb;
    $profiles = $wpdb->get_results($wpdb->prepare(
        "SELECT id, name, avatar_color, is_kids FROM {$wpdb->prefix}f5tv_profiles WHERE user_id = %d ORDER BY created_at ASC",
        $user_id
    ));

    if (!$profiles) {
        return '<p>Nenhum perfil encontrado.</p>';
    }

    ob_start();
    ?>
    <div class="f5tv-profile-selector">
        <h3>Quem está assistindo?</h3>
        <div class="f5tv-profile-grid">
            <?php foreach ($profiles as $profile): ?>
                <button class="f5tv-profile-card" data-profile-id="<?php echo esc_attr($profile->id); ?>" data-is-kids="<?php echo esc_attr($profile->is_kids ? '1' : '0'); ?>">
                    <div class="f5tv-profile-avatar <?php echo esc_attr($profile->avatar_color); ?>">
                        <?php echo esc_html(mb_substr($profile->name, 0, 1)); ?>
                    </div>
                    <span><?php echo esc_html($profile->name); ?></span>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
