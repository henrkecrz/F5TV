<?php
/**
 * F5TV Client Area - Content Access
 * Controla o acesso a conteúdos baseado em assinatura e plano
 */

if (!defined('ABSPATH')) {
    exit;
}

class F5TV_Content_Access
{
    public function __construct()
    {
        add_action('template_redirect', [$this, 'handle_access_redirect']);
    }

    public function handle_access_redirect(): void
    {
        if (is_admin() || !is_singular('f5tv_conteudo')) {
            return;
        }

        $post_id = get_the_ID();
        if (!$post_id) {
            return;
        }

        $user_id = get_current_user_id();
        if (!$user_id) {
            return;
        }

        $user = get_userdata($user_id);
        if (in_array('administrator', $user->roles) || in_array('editor', $user->roles)) {
            return;
        }

        if (get_field('is_free', $post_id)) {
            return;
        }

        if (!current_user_can('f5tv_access_content', $post_id)) {
            wp_redirect(wp_login_url(get_permalink($post_id)));
            exit;
        }
    }
}
