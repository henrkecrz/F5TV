<?php
/**
 * F5TV Client Area - Database Install
 * Criação/atualização das tabelas customizadas do plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

function f5tv_client_area_install_tables(): void
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $table_profiles = $wpdb->prefix . 'f5tv_profiles';
    $sql_profiles = "CREATE TABLE $table_profiles (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        name VARCHAR(100) NOT NULL,
        avatar_color VARCHAR(50) DEFAULT 'bg-f5-red',
        is_kids BOOLEAN DEFAULT FALSE,
        pin_hash VARCHAR(255),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id)
    ) $charset_collate;";
    dbDelta($sql_profiles);

    $table_subs = $wpdb->prefix . 'f5tv_subscriptions';
    $sql_subs = "CREATE TABLE $table_subs (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        plan_id VARCHAR(50) NOT NULL,
        status VARCHAR(50) DEFAULT 'active',
        gateway VARCHAR(50) DEFAULT 'stripe',
        gateway_subscription_id VARCHAR(255),
        current_period_start DATETIME,
        current_period_end DATETIME,
        cancel_at_period_end BOOLEAN DEFAULT FALSE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id),
        INDEX idx_gateway_sub (gateway_subscription_id)
    ) $charset_collate;";
    dbDelta($sql_subs);

    $table_history = $wpdb->prefix . 'f5tv_watch_history';
    $sql_history = "CREATE TABLE $table_history (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        profile_id BIGINT UNSIGNED,
        content_id BIGINT UNSIGNED NOT NULL,
        episode_id BIGINT UNSIGNED,
        watched_seconds INT UNSIGNED DEFAULT 0,
        total_seconds INT UNSIGNED,
        progress DECIMAL(5,2) DEFAULT 0,
        completed BOOLEAN DEFAULT FALSE,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_user_profile (user_id, profile_id),
        INDEX idx_content (content_id),
        UNIQUE KEY uniq_user_content_episode (user_id, profile_id, content_id, episode_id)
    ) $charset_collate;";
    dbDelta($sql_history);

    $table_mylist = $wpdb->prefix . 'f5tv_my_list';
    $sql_mylist = "CREATE TABLE $table_mylist (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        profile_id BIGINT UNSIGNED,
        content_id BIGINT UNSIGNED NOT NULL,
        added_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user_profile (user_id, profile_id),
        UNIQUE KEY uniq_user_content (user_id, profile_id, content_id)
    ) $charset_collate;";
    dbDelta($sql_mylist);

    $table_devices = $wpdb->prefix . 'f5tv_devices';
    $sql_devices = "CREATE TABLE $table_devices (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        device_name VARCHAR(255),
        device_type VARCHAR(50),
        device_fingerprint VARCHAR(255),
        user_agent TEXT,
        ip_address VARCHAR(45),
        location VARCHAR(255),
        last_active DATETIME,
        is_active BOOLEAN DEFAULT TRUE,
        revoked_at DATETIME NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id),
        INDEX idx_fingerprint (device_fingerprint)
    ) $charset_collate;";
    dbDelta($sql_devices);
}
