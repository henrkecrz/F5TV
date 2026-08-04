<?php
/**
 * F5TV Client Area - Basic Tests
 * Run with: phpunit tests/
 */

if (!defined('ABSPATH')) {
    exit;
}

// Teste simples de ativação do plugin
function test_plugin_activation_creates_tables(): void
{
    global $wpdb;

    f5tv_client_area_activate();

    $tables = [
        $wpdb->prefix . 'f5tv_profiles',
        $wpdb->prefix . 'f5tv_subscriptions',
        $wpdb->prefix . 'f5tv_watch_history',
        $wpdb->prefix . 'f5tv_my_list',
        $wpdb->prefix . 'f5tv_devices',
    ];

    foreach ($tables as $table) {
        $exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) === $table;
        assert($exists, "Table $table should exist after activation");
    }
}

// Teste de REST API: login
function test_rest_login_requires_params(): void
{
    $request = new WP_REST_Request('POST', '/f5tv/v1/auth/login');
    $auth = new F5TV_Auth();
    $response = $auth->rest_login($request);
    assert($response->get_status() === 400, 'Should return 400 when email/password missing');
}

// Teste de REST API: logout
function test_rest_logout(): void
{
    $request = new WP_REST_Request('POST', '/f5tv/v1/auth/logout');
    $auth = new F5TV_Auth();
    $response = $auth->rest_logout($request);
    assert($response->get_status() === 200, 'Should return 200 on logout');
}

// Teste de capacidade customizada
function test_content_access_capability(): void
{
    $user = wp_get_current_user();
    $caps = [];
    $allcaps = apply_filters('user_has_cap', $caps, ['f5tv_access_content'], [0, 0, 0], $user);
    assert(is_array($allcaps), 'Capability check should return array');
}
