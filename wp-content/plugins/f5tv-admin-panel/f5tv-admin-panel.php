<?php
/**
 * Plugin Name: F5TV Admin Panel
 * Description: Extensões administrativas para a plataforma F5 TV, incluindo estatísticas financeiras, MRR, cupons e grade de programação.
 * Version: 1.0.0
 * Author: F5 TV Team
 * Text Domain: f5tv-admin-panel
 */

if (!defined('ABSPATH')) {
    exit;
}

define('F5TV_ADMIN_PANEL_VERSION', '1.0.0');
define('F5TV_ADMIN_PANEL_DIR', plugin_dir_path(__FILE__));
define('F5TV_ADMIN_PANEL_URI', plugin_dir_url(__FILE__));

// Inicializar Menus Administrativos do WordPress
add_action('admin_menu', 'f5tv_admin_add_menus');

function f5tv_admin_add_menus(): void
{
    add_menu_page(
        __('Dashboard F5 TV', 'f5tv-admin-panel'),
        __('F5 Dashboard', 'f5tv-admin-panel'),
        'manage_options',
        'f5tv-dashboard',
        'f5tv_admin_render_dashboard',
        'dashicons-chart-area',
        6
    );

    add_submenu_page(
        'f5tv-dashboard',
        __('Assinantes F5 TV', 'f5tv-admin-panel'),
        __('Assinantes', 'f5tv-admin-panel'),
        'manage_options',
        'f5tv-subscribers',
        'f5tv_admin_render_subscribers'
    );

    add_submenu_page(
        'f5tv-dashboard',
        __('Financeiro F5 TV', 'f5tv-admin-panel'),
        __('Financeiro', 'f5tv-admin-panel'),
        'manage_options',
        'f5tv-finance',
        'f5tv_admin_render_finance'
    );

    add_submenu_page(
        'f5tv-dashboard',
        __('Configurações F5 TV', 'f5tv-admin-panel'),
        __('Configurações', 'f5tv-admin-panel'),
        'manage_options',
        'f5tv-settings',
        'f5tv_admin_render_settings'
    );

    add_submenu_page(
        'f5tv-dashboard',
        __('Cupons F5 TV', 'f5tv-admin-panel'),
        __('Cupons', 'f5tv-admin-panel'),
        'manage_options',
        'f5tv-coupons',
        'f5tv_admin_render_coupons'
    );

    add_submenu_page(
        'f5tv-dashboard',
        __('Programação F5 TV', 'f5tv-admin-panel'),
        __('Programação', 'f5tv-admin-panel'),
        'manage_options',
        'f5tv-live-schedule',
        'f5tv_admin_render_live_schedule'
    );
}

function f5tv_admin_render_dashboard(): void
{
    if (class_exists('F5TV_Admin_Dashboard')) {
        (new F5TV_Admin_Dashboard())->render_page();
    } else {
        echo '<div class="wrap"><h1>Dashboard Administrativo F5 TV</h1><p>Visão geral de assinaturas, conteúdos mais assistidos e status dos servidores.</p></div>';
    }
}

function f5tv_admin_render_subscribers(): void
{
    if (class_exists('F5TV_Admin_Subscribers')) {
        (new F5TV_Admin_Subscribers())->render_page();
    } else {
        echo '<div class="wrap"><h1>Gestão de Assinantes</h1><p>Filtre, edite e exporte a lista completa de assinantes da plataforma F5 TV.</p></div>';
    }
}

function f5tv_admin_render_finance(): void
{
    if (class_exists('F5TV_Admin_Finance')) {
        (new F5TV_Admin_Finance())->render_page();
    } else {
        echo '<div class="wrap"><h1>Relatórios Financeiros</h1><p>Acompanhe receitas recorrentes (MRR), taxa de cancelamento (Churn) e faturamento histórico.</p></div>';
    }
}

function f5tv_admin_render_settings(): void
{
    if (class_exists('F5TV_Admin_Settings')) {
        (new F5TV_Admin_Settings())->render_page();
    } else {
        echo '<div class="wrap"><h1>Configurações do Streaming</h1><p>Gerencie URLs do CDN Bunny.net/Cloudflare, chaves de API e integrações de pagamento.</p></div>';
    }
}

function f5tv_admin_render_coupons(): void
{
    if (class_exists('F5TV_Admin_Coupons')) {
        (new F5TV_Admin_Coupons())->render_page();
    } else {
        echo '<div class="wrap"><h1>Cupons de Desconto</h1><p>Gerencie cupons e descontos da plataforma.</p></div>';
    }
}

function f5tv_admin_render_live_schedule(): void
{
    if (class_exists('F5TV_Admin_Live_Schedule')) {
        (new F5TV_Admin_Live_Schedule())->render_page();
    } else {
        echo '<div class="wrap"><h1>Grade de Programação</h1><p>Gerenciamento da programação ao vivo dos canais.</p></div>';
    }
}

// Carregar classes do painel admin
add_action('plugins_loaded', function () {
    $classes = [
        'includes/class-dashboard.php',
        'includes/class-subscribers.php',
        'includes/class-finance.php',
        'includes/class-settings.php',
        'includes/class-coupons.php',
        'includes/class-live-schedule.php',
        'includes/class-content-studio.php',
    ];

    foreach ($classes as $class) {
        $path = F5TV_ADMIN_PANEL_DIR . $class;
        if (file_exists($path)) {
            require_once $path;
        }
    }

    if (class_exists('F5TV_Admin_Content_Studio')) {
        new F5TV_Admin_Content_Studio();
    }
});
