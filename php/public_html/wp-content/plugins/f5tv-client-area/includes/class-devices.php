<?php
/**
 * F5TV Client Area - Devices
 * Gerenciamento de dispositivos conectados
 */

if (!defined('ABSPATH')) {
    exit;
}

class F5TV_Devices
{
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes(): void
    {
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
    }

    public function check_logged_in(): bool
    {
        return is_user_logged_in();
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
        $ip_address = sanitize_text_field($request->get_param('ip_address')) ?: ($_SERVER['REMOTE_ADDR'] ?? '');
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
}
