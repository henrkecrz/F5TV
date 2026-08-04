<?php
/**
 * Template: Shortcode Seletor de Perfil
 * Uso: [f5tv_profile_selector]
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!is_user_logged_in()) {
    echo '<p><a href="' . esc_url(wp_login_url()) . '">Faça login para selecionar um perfil.</a></p>';
    return;
}

$user_id = get_current_user_id();
global $wpdb;
$profiles = $wpdb->get_results($wpdb->prepare(
    "SELECT id, name, avatar_color, is_kids FROM {$wpdb->prefix}f5tv_profiles WHERE user_id = %d ORDER BY created_at ASC",
    $user_id
));

if (!$profiles) {
    echo '<p>Nenhum perfil encontrado.</p>';
    return;
}

$redirect_url = isset($_GET['redirect']) ? esc_url_raw($_GET['redirect']) : home_url('/');
?>
<div class="f5tv-profile-selector">
    <h3>Quem está assistindo?</h3>
    <div class="f5tv-profile-grid">
        <?php foreach ($profiles as $profile): ?>
            <button class="f5tv-profile-card" data-profile-id="<?php echo esc_attr($profile->id); ?>" data-is-kids="<?php echo esc_attr($profile->is_kids ? '1' : '0'); ?>" data-redirect="<?php echo esc_attr($redirect_url); ?>">
                <div class="f5tv-profile-avatar <?php echo esc_attr($profile->avatar_color); ?>">
                    <?php echo esc_html(mb_substr($profile->name, 0, 1)); ?>
                </div>
                <span><?php echo esc_html($profile->name); ?></span>
            </button>
        <?php endforeach; ?>
        <button class="f5tv-profile-card f5tv-profile-add" id="f5tv-add-profile">
            <div class="f5tv-profile-avatar f5tv-avatar-add">+</div>
            <span>Adicionar Perfil</span>
        </button>
    </div>
</div>
