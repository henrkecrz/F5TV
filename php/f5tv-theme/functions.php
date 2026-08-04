<?php
/**
 * F5TV Theme - Functions.php
 * Configuração principal do tema
 */

if (!defined('ABSPATH')) {
    exit;
}

define('F5TV_THEME_VERSION', '1.0.0');
define('F5TV_THEME_DIR', get_template_directory());
define('F5TV_THEME_URI', get_template_directory_uri());
define('F5TV_ASSETS_DIR', F5TV_THEME_DIR . '/assets');
define('F5TV_ASSETS_URI', F5TV_THEME_URI . '/assets');

// Carregar dependências do Composer (se usar)
if (file_exists(F5TV_THEME_DIR . '/vendor/autoload.php')) {
    require_once F5TV_THEME_DIR . '/vendor/autoload.php';
}

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

    // JS principal (blocos Gutenberg leves)
    wp_enqueue_script('f5tv-main-js', F5TV_ASSETS_URI . '/dist/js/main.js', [], $version, true);

    // Localizar dados para JS
    wp_localize_script('f5tv-main-js', 'f5tvThemeData', [
        'ajaxUrl'      => admin_url('admin-ajax.php'),
        'nonce'        => wp_create_nonce('f5tv_nonce'),
        'restUrl'      => rest_url('f5tv/v1/'),
        'themeUri'     => F5TV_THEME_URI,
        'isUserLoggedIn' => is_user_logged_in(),
        'currentUser'  => wp_get_current_user()->ID,
    ]);
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

