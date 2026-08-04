<?php
/**
 * F5TV Client Area - My List
 * Gerenciamento de favoritos / minha lista
 */

if (!defined('ABSPATH')) {
    exit;
}

class F5TV_My_List
{
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes(): void
    {
        register_rest_route('f5tv/v1', '/my-list', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [$this, 'get_list'],
            'permission_callback' => [$this, 'check_logged_in'],
        ]);

        register_rest_route('f5tv/v1', '/my-list', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'add_item'],
            'permission_callback' => [$this, 'check_logged_in'],
        ]);

        register_rest_route('f5tv/v1', '/my-list/(?P<content_id>\d+)', [
            'methods'             => WP_REST_Server::DELETABLE,
            'callback'            => [$this, 'remove_item'],
            'permission_callback' => [$this, 'check_logged_in'],
        ]);
    }

    public function check_logged_in(): bool
    {
        return is_user_logged_in();
    }

    public function get_list(WP_REST_Request $request): WP_REST_Response
    {
        global $wpdb;
        $user_id = get_current_user_id();
        $profile_id = intval($request->get_param('profile_id'));
        $table = $wpdb->prefix . 'f5tv_my_list';

        $query = "SELECT content_id FROM $table WHERE user_id = %d";
        $params = [$user_id];

        if ($profile_id > 0) {
            $query .= " AND profile_id = %d";
            $params[] = $profile_id;
        }

        $query .= " ORDER BY added_at DESC";
        $content_ids = $wpdb->get_col($wpdb->prepare($query, $params));

        $response = [];
        foreach ($content_ids as $post_id) {
            $response[] = [
                'id'       => get_post_field('post_name', $post_id),
                'title'    => get_the_title($post_id),
                'coverUrl' => get_the_post_thumbnail_url($post_id, 'f5tv-content-cover') ?: get_field('cover_url', $post_id),
                'addedAt'  => get_post_field('post_date', $post_id),
            ];
        }

        return new WP_REST_Response($response, 200);
    }

    public function add_item(WP_REST_Request $request): WP_REST_Response
    {
        global $wpdb;
        $user_id = get_current_user_id();
        $profile_id = intval($request->get_param('profile_id'));
        $content_id = intval($request->get_param('content_id'));

        if (!$content_id) {
            return new WP_REST_Response(['message' => 'Conteúdo inválido.'], 400);
        }

        $table = $wpdb->prefix . 'f5tv_my_list';
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table WHERE user_id = %d AND profile_id = %d AND content_id = %d",
            $user_id, $profile_id, $content_id
        ));

        if ($existing) {
            return new WP_REST_Response(['success' => true, 'message' => 'Já está na lista.'], 200);
        }

        $wpdb->insert($table, [
            'user_id'    => $user_id,
            'profile_id' => $profile_id,
            'content_id' => $content_id,
            'added_at'   => current_time('mysql'),
        ]);

        return new WP_REST_Response(['success' => true], 201);
    }

    public function remove_item(WP_REST_Request $request): WP_REST_Response
    {
        global $wpdb;
        $user_id = get_current_user_id();
        $profile_id = intval($request->get_param('profile_id'));
        $content_id = intval($request->get_param('content_id'));

        $table = $wpdb->prefix . 'f5tv_my_list';
        $deleted = $wpdb->delete($table, [
            'user_id'    => $user_id,
            'profile_id' => $profile_id,
            'content_id' => $content_id,
        ]);

        return new WP_REST_Response(['success' => (bool) $deleted], 200);
    }
}
