<?php
/**
 * F5TV Client Area - Watch History
 * Gerenciamento de histórico de visualização
 */

if (!defined('ABSPATH')) {
    exit;
}

class F5TV_Watch_History
{
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes(): void
    {
        register_rest_route('f5tv/v1', '/continue-watching', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [$this, 'get_continue_watching'],
            'permission_callback' => [$this, 'check_logged_in'],
        ]);
    }

    public function check_logged_in(): bool
    {
        return is_user_logged_in();
    }

    public function get_continue_watching(WP_REST_Request $request): WP_REST_Response
    {
        global $wpdb;
        $user_id = get_current_user_id();
        $profile_id = intval($request->get_param('profile_id'));
        $table = $wpdb->prefix . 'f5tv_watch_history';

        $items = $wpdb->get_results($wpdb->prepare(
            "SELECT content_id, episode_id, watched_seconds, total_seconds, progress, updated_at FROM $table WHERE user_id = %d AND profile_id = %d AND completed = 0 AND progress > 0 ORDER BY updated_at DESC LIMIT 20",
            $user_id, $profile_id
        ));

        $response = [];
        foreach ($items as $item) {
            $post_id = $item->content_id;
            $response[] = [
                'contentId'      => strval($post_id),
                'episodeId'      => $item->episode_id ? strval($item->episode_id) : null,
                'watchedSeconds' => intval($item->watched_seconds),
                'totalSeconds'   => intval($item->total_seconds),
                'progress'       => floatval($item->progress),
                'updatedAt'      => $item->updated_at,
                'title'          => get_the_title($post_id),
                'coverUrl'       => get_the_post_thumbnail_url($post_id, 'f5tv-content-cover') ?: get_field('cover_url', $post_id),
            ];
        }

        return new WP_REST_Response($response, 200);
    }
}
