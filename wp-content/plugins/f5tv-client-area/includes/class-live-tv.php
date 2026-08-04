<?php
/**
 * F5TV Client Area - Live TV
 * Gerenciamento de canais e programação ao vivo
 */

if (!defined('ABSPATH')) {
    exit;
}

class F5TV_Live_TV
{
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes(): void
    {
        register_rest_route('f5tv/v1', '/live/channels', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [$this, 'get_channels'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route('f5tv/v1', '/live/schedule', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [$this, 'get_schedule'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function get_channels(WP_REST_Request $request): WP_REST_Response
    {
        $query = new WP_Query([
            'post_type'      => 'f5tv_canal',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_key'       => 'active',
            'meta_value'     => '1',
        ]);

        $response = [];
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $terms = get_the_terms($post_id, 'f5tv_categoria');
            $category = ($terms && !is_wp_error($terms)) ? $terms[0]->name : 'Geral';

            $response[] = [
                'id'        => get_post_field('post_name', $post_id),
                'name'      => get_the_title(),
                'logoText'  => get_field('logo_text', $post_id) ?: 'F5',
                'streamUrl' => get_field('stream_url', $post_id) ?: '',
                'active'    => (bool) get_field('active', $post_id),
                'status'    => get_field('status', $post_id) ?: 'online',
                'category'  => $category,
            ];
        }
        wp_reset_postdata();

        return new WP_REST_Response($response, 200);
    }

    public function get_schedule(WP_REST_Request $request): WP_REST_Response
    {
        $date = sanitize_text_field($request->get_param('date')) ?: date('Y-m-d');
        $channel_id = sanitize_text_field($request->get_param('channel_id'));

        $args = [
            'post_type'      => 'f5tv_programacao',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_query'     => [
                [
                    'key'     => 'date',
                    'value'   => $date,
                    'compare' => '=',
                ]
            ],
            'orderby'        => 'meta_value',
            'order'          => 'ASC',
            'meta_key'       => 'start_time',
        ];

        $query = new WP_Query($args);
        $response = [];

        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $channel_post_id = get_field('channel_id', $post_id);
            $channel_slug = $channel_post_id ? get_post_field('post_name', $channel_post_id) : '';

            if ($channel_id && $channel_slug !== $channel_id) {
                continue;
            }

            $response[] = [
                'id'          => get_post_field('post_name', $post_id),
                'channelId'   => $channel_slug,
                'title'       => get_the_title(),
                'description' => get_field('description', $post_id) ?: '',
                'host'        => get_field('host', $post_id) ?: '',
                'date'        => get_field('date', $post_id),
                'startTime'   => substr(get_field('start_time', $post_id), 0, 5),
                'endTime'     => substr(get_field('end_time', $post_id), 0, 5),
                'status'      => get_field('status', $post_id) ?: 'scheduled',
                'imageUrl'    => get_field('image_url', $post_id) ?: '',
                'isFeatured'  => (bool) get_field('is_featured', $post_id),
            ];
        }
        wp_reset_postdata();

        return new WP_REST_Response($response, 200);
    }
}
