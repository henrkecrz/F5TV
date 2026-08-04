<?php
/**
 * F5TV Client Area - REST API
 * Endpoints adicionais do client area: watch history, my list, devices, checkout, etc.
 */

if (!defined('ABSPATH')) {
    exit;
}

class F5TV_Client_REST_API
{
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes(): void
    {
        // Watch History
        register_rest_route('f5tv/v1', '/watch-history', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [$this, 'get_watch_history'],
            'permission_callback' => [$this, 'check_logged_in'],
        ]);
        register_rest_route('f5tv/v1', '/watch-history', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'save_watch_history'],
            'permission_callback' => [$this, 'check_logged_in'],
        ]);

        // My List
        register_rest_route('f5tv/v1', '/my-list', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [$this, 'get_my_list'],
            'permission_callback' => [$this, 'check_logged_in'],
        ]);
        register_rest_route('f5tv/v1', '/my-list', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'add_to_my_list'],
            'permission_callback' => [$this, 'check_logged_in'],
        ]);
        register_rest_route('f5tv/v1', '/my-list/(?P<content_id>\d+)', [
            'methods'             => WP_REST_Server::DELETABLE,
            'callback'            => [$this, 'remove_from_my_list'],
            'permission_callback' => [$this, 'check_logged_in'],
        ]);

        // Devices
        register_rest_route('f5tv/v1', '/devices', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [$this, 'get_devices'],
            'permission_callback' => [$this, 'check_logged_in'],
        ]);
        register_rest_route('f5tv/v1', '/devices', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'register_device'],
            'permission_callback' => [$this, 'check_logged_in'],
        ]);
        register_rest_route('f5tv/v1', '/devices/(?P<id>\d+)', [
            'methods'             => WP_REST_Server::DELETABLE,
            'callback'            => [$this, 'revoke_device'],
            'permission_callback' => [$this, 'check_logged_in'],
        ]);

        // Checkout / Subscription status
        register_rest_route('f5tv/v1', '/subscription', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [$this, 'get_subscription_status'],
            'permission_callback' => [$this, 'check_logged_in'],
        ]);

        // Content access check
        register_rest_route('f5tv/v1', '/content/access', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [$this, 'check_content_access'],
            'permission_callback' => [$this, 'check_logged_in'],
        ]);
    }

    public function check_logged_in(): bool
    {
        return is_user_logged_in();
    }

    public function get_watch_history(WP_REST_Request $request): WP_REST_Response
    {
        global $wpdb;
        $user_id = get_current_user_id();
        $profile_id = intval($request->get_param('profile_id'));
        $table = $wpdb->prefix . 'f5tv_watch_history';

        $query = "SELECT * FROM $table WHERE user_id = %d";
        $params = [$user_id];

        if ($profile_id > 0) {
            $query .= " AND profile_id = %d";
            $params[] = $profile_id;
        }

        $query .= " ORDER BY updated_at DESC LIMIT 50";

        $items = $wpdb->get_results($wpdb->prepare($query, $params));

        $response = [];
        foreach ($items as $item) {
            $response[] = [
                'contentId'      => strval($item->content_id),
                'episodeId'      => $item->episode_id ? strval($item->episode_id) : null,
                'watchedSeconds' => intval($item->watched_seconds),
                'totalSeconds'   => intval($item->total_seconds),
                'progress'       => floatval($item->progress),
                'completed'      => (bool) $item->completed,
                'updatedAt'      => $item->updated_at,
            ];
        }

        return new WP_REST_Response($response, 200);
    }

    public function save_watch_history(WP_REST_Request $request): WP_REST_Response
    {
        global $wpdb;
        $user_id = get_current_user_id();
        $profile_id = intval($request->get_param('profile_id'));
        $content_id = intval($request->get_param('content_id'));
        $episode_id = $request->get_param('episode_id') ? intval($request->get_param('episode_id')) : null;
        $watched_seconds = intval($request->get_param('watched_seconds'));
        $total_seconds = intval($request->get_param('total_seconds'));
        $progress = min(100, max(0, floatval($request->get_param('progress'))));
        $completed = $progress >= 90 ? 1 : 0;

        if (!$content_id) {
            return new WP_REST_Response(['message' => 'Conteúdo inválido.'], 400);
        }

        $table = $wpdb->prefix . 'f5tv_watch_history';
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE user_id = %d AND profile_id = %d AND content_id = %d AND episode_id " . ($episode_id ? "= %d" : "IS NULL"),
            array_merge([$user_id, $profile_id, $content_id], $episode_id ? [$episode_id] : [])
        ));

        if ($existing) {
            $wpdb->update($table, [
                'watched_seconds' => $watched_seconds,
                'total_seconds'   => $total_seconds,
                'progress'        => $progress,
                'completed'       => $completed,
                'updated_at'      => current_time('mysql'),
            ], [
                'user_id'    => $user_id,
                'profile_id' => $profile_id,
                'content_id' => $content_id,
                'episode_id' => $episode_id,
            ]);
        } else {
            $wpdb->insert($table, [
                'user_id'         => $user_id,
                'profile_id'      => $profile_id,
                'content_id'      => $content_id,
                'episode_id'      => $episode_id,
                'watched_seconds' => $watched_seconds,
                'total_seconds'   => $total_seconds,
                'progress'        => $progress,
                'completed'       => $completed,
                'updated_at'      => current_time('mysql'),
            ]);
        }

        return new WP_REST_Response(['success' => true], 200);
    }

    public function get_my_list(WP_REST_Request $request): WP_REST_Response
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
        $results = $wpdb->get_col($wpdb->prepare($query, $params));

        return new WP_REST_Response(array_map('strval', $results), 200);
    }

    public function add_to_my_list(WP_REST_Request $request): WP_REST_Response
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

    public function remove_from_my_list(WP_REST_Request $request): WP_REST_Response
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

    public function get_devices(WP_REST_Request $request): WP_REST_Response
    {
        global $wpdb;
        $user_id = get_current_user_id();
        $table = $wpdb->prefix . 'f5tv_devices';

        $devices = $wpdb->get_results($wpdb->prepare(
            "SELECT id, device_name, device_type, device_fingerprint, ip_address, location, last_active, is_active, created_at FROM $table WHERE user_id = %d ORDER BY last_active DESC",
            $user_id
        ));

        $response = [];
        foreach ($devices as $device) {
            $response[] = [
                'id'                => strval($device->id),
                'deviceName'        => $device->device_name,
                'deviceType'        => $device->device_type,
                'deviceFingerprint' => $device->device_fingerprint,
                'ipAddress'         => $device->ip_address,
                'location'          => $device->location,
                'lastActive'        => $device->last_active,
                'isActive'          => (bool) $device->is_active,
                'createdAt'         => $device->created_at,
            ];
        }

        return new WP_REST_Response($response, 200);
    }

    public function register_device(WP_REST_Request $request): WP_REST_Response
    {
        global $wpdb;
        $user_id = get_current_user_id();
        $table = $wpdb->prefix . 'f5tv_devices';

        $device_name = sanitize_text_field($request->get_param('device_name')) ?: 'Dispositivo';
        $device_type = sanitize_text_field($request->get_param('device_type')) ?: 'browser';
        $device_fingerprint = sanitize_text_field($request->get_param('device_fingerprint'));
        $user_agent = sanitize_text_field($request->get_param('user_agent')) ?: '';
        $ip_address = sanitize_text_field($request->get_param('ip_address')) ?: $_SERVER['REMOTE_ADDR'] ?? '';
        $location = sanitize_text_field($request->get_param('location')) ?: '';

        if (!$device_fingerprint) {
            return new WP_REST_Response(['message' => 'Fingerprint do dispositivo é obrigatório.'], 400);
        }

        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table WHERE user_id = %d AND device_fingerprint = %s",
            $user_id, $device_fingerprint
        ));

        if ($existing) {
            $wpdb->update($table, [
                'device_name'  => $device_name,
                'device_type'  => $device_type,
                'user_agent'   => $user_agent,
                'ip_address'   => $ip_address,
                'location'     => $location,
                'last_active'  => current_time('mysql'),
                'is_active'    => 1,
            ], ['id' => $existing]);
        } else {
            $wpdb->insert($table, [
                'user_id'          => $user_id,
                'device_name'      => $device_name,
                'device_type'      => $device_type,
                'device_fingerprint' => $device_fingerprint,
                'user_agent'       => $user_agent,
                'ip_address'       => $ip_address,
                'location'         => $location,
                'last_active'      => current_time('mysql'),
                'is_active'        => 1,
            ]);
        }

        return new WP_REST_Response(['success' => true], 200);
    }

    public function revoke_device(WP_REST_Request $request): WP_REST_Response
    {
        global $wpdb;
        $user_id = get_current_user_id();
        $device_id = intval($request->get_param('id'));
        $table = $wpdb->prefix . 'f5tv_devices';

        $device = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d AND user_id = %d",
            $device_id, $user_id
        ));

        if (!$device) {
            return new WP_REST_Response(['message' => 'Dispositivo não encontrado.'], 404);
        }

        $wpdb->update($table, [
            'is_active'  => 0,
            'revoked_at' => current_time('mysql'),
        ], ['id' => $device_id]);

        return new WP_REST_Response(['success' => true], 200);
    }

    public function get_subscription_status(WP_REST_Request $request): WP_REST_Response
    {
        global $wpdb;
        $user_id = get_current_user_id();
        $table = $wpdb->prefix . 'f5tv_subscriptions';

        $sub = $wpdb->get_row($wpdb->prepare(
            "SELECT plan_id, status, current_period_end, cancel_at_period_end FROM $table WHERE user_id = %d LIMIT 1",
            $user_id
        ));

        if (!$sub) {
            return new WP_REST_Response([
                'planId'    => '',
                'status'    => 'inactive',
                'periodEnd' => null,
            ], 200);
        }

        return new WP_REST_Response([
            'planId'         => $sub->plan_id,
            'status'         => $sub->status,
            'periodEnd'      => $sub->current_period_end,
            'cancelAtPeriod' => (bool) $sub->cancel_at_period_end,
        ], 200);
    }

    public function check_content_access(WP_REST_Request $request): WP_REST_Response
    {
        $content_id = intval($request->get_param('content_id'));
        if (!$content_id) {
            return new WP_REST_Response(['message' => 'Conteúdo inválido.'], 400);
        }

        $user_id = get_current_user_id();
        if (!$user_id) {
            return new WP_REST_Response(['allowed' => false, 'reason' => 'not_logged_in'], 200);
        }

        $user = get_userdata($user_id);
        if (in_array('administrator', $user->roles) || in_array('editor', $user->roles)) {
            return new WP_REST_Response(['allowed' => true, 'reason' => 'admin'], 200);
        }

        $is_free = get_field('is_free', $content_id);
        if ($is_free) {
            return new WP_REST_Response(['allowed' => true, 'reason' => 'free'], 200);
        }

        if (current_user_can('f5tv_access_content', $content_id)) {
            return new WP_REST_Response(['allowed' => true, 'reason' => 'subscription'], 200);
        }

        return new WP_REST_Response(['allowed' => false, 'reason' => 'no_subscription'], 200);
    }
}
