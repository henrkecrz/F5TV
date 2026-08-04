<?php
/**
 * F5TV_Auth class
 * Gerenciamento de login, registro e perfis dos usuários no WordPress
 */

if (!defined('ABSPATH')) {
    exit;
}

class F5TV_Auth
{
    public function __construct()
    {
        // Registrar endpoints da REST API para autenticação e perfis
        add_action('rest_api_init', [$this, 'register_auth_routes']);
    }

    public function register_auth_routes(): void
    {
        // Login
        register_rest_route('f5tv/v1', '/auth/login', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'rest_login'],
            'permission_callback' => '__return_true',
        ]);

        // Registro
        register_rest_route('f5tv/v1', '/auth/register', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'rest_register'],
            'permission_callback' => '__return_true',
        ]);

        // Listagem de Perfis
        register_rest_route('f5tv/v1', '/profiles', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [$this, 'rest_get_profiles'],
            'permission_callback' => [$this, 'check_user_logged_in'],
        ]);

        // Criar Perfil
        register_rest_route('f5tv/v1', '/profiles', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'rest_create_profile'],
            'permission_callback' => [$this, 'check_user_logged_in'],
        ]);

        // Excluir Perfil
        register_rest_route('f5tv/v1', '/profiles/(?P<id>\d+)', [
            'methods'             => WP_REST_Server::DELETABLE,
            'callback'            => [$this, 'rest_delete_profile'],
            'permission_callback' => [$this, 'check_user_logged_in'],
        ]);

        // Logout
        register_rest_route('f5tv/v1', '/auth/logout', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'rest_logout'],
            'permission_callback' => [$this, 'check_user_logged_in'],
        ]);
    }

    public function check_user_logged_in(): bool
    {
        return is_user_logged_in();
    }

    /**
     * Handler: Login
     */
    public function rest_login(WP_REST_Request $request): WP_REST_Response
    {
        $username = sanitize_text_field($request->get_param('email'));
        $password = $request->get_param('password');

        if (empty($username) || empty($password)) {
            return new WP_REST_Response(['message' => 'Email e senha são obrigatórios.'], 400);
        }

        $creds = [
            'user_login'    => $username,
            'user_password' => $password,
            'remember'      => true,
        ];

        $user = wp_signon($creds, false);

        if (is_wp_error($user)) {
            return new WP_REST_Response(['message' => 'Credenciais inválidas.'], 401);
        }

        // Sucesso no login, retornar dados do usuário
        $user_plan = get_user_meta($user->ID, 'f5tv_plan', true);
        $user_status = get_user_meta($user->ID, 'f5tv_subscription_status', true) ?: 'inactive';

        // Atualizar data de último login
        update_user_meta($user->ID, 'last_login', current_time('mysql'));

        return new WP_REST_Response([
            'id'        => strval($user->ID),
            'name'      => $user->display_name,
            'email'     => $user->user_email,
            'role'      => in_array('administrator', $user->roles) ? 'admin' : 'subscriber',
            'planId'    => $user_plan,
            'status'    => $user_status,
            'createdAt' => $user->user_registered,
        ], 200);
    }

    /**
     * Handler: Registro
     */
    public function rest_register(WP_REST_Request $request): WP_REST_Response
    {
        $email = sanitize_email($request->get_param('email'));
        $name = sanitize_text_field($request->get_param('name'));
        $password = $request->get_param('password');

        if (empty($email) || empty($name) || empty($password)) {
            return new WP_REST_Response(['message' => 'Preencha todos os campos.'], 400);
        }

        if (email_exists($email)) {
            return new WP_REST_Response(['message' => 'Este email já está cadastrado.'], 400);
        }

        $user_id = wp_create_user($email, $password, $email);

        if (is_wp_error($user_id)) {
            return new WP_REST_Response(['message' => 'Erro ao criar usuário.'], 500);
        }

        // Configurar nome de exibição e meta padrão
        wp_update_user([
            'ID'           => $user_id,
            'display_name' => $name,
        ]);

        update_user_meta($user_id, 'f5tv_subscription_status', 'inactive');

        // Criar perfil padrão para o usuário recém-registrado
        global $wpdb;
        $table_name = $wpdb->prefix . 'f5tv_profiles';
        $wpdb->insert($table_name, [
            'user_id'      => $user_id,
            'name'         => explode(' ', $name)[0], // Primeiro nome
            'avatar_color' => 'bg-f5-red',
            'is_kids'      => false,
        ]);

        $user = get_userdata($user_id);

        return new WP_REST_Response([
            'id'        => strval($user_id),
            'name'      => $name,
            'email'     => $email,
            'role'      => 'subscriber',
            'planId'    => '',
            'status'    => 'inactive',
            'createdAt' => $user->user_registered,
        ], 201);
    }

    /**
     * Handler: Listar Perfis
     */
    public function rest_get_profiles(): WP_REST_Response
    {
        global $wpdb;
        $user_id = get_current_user_id();
        $table_name = $wpdb->prefix . 'f5tv_profiles';

        $profiles = $wpdb->get_results($wpdb->prepare(
            "SELECT id, name, avatar_color, is_kids FROM $table_name WHERE user_id = %d ORDER BY created_at ASC",
            $user_id
        ));

        $response = [];
        foreach ($profiles as $profile) {
            $response[] = [
                'id'          => strval($profile->id),
                'userId'      => strval($user_id),
                'name'        => $profile->name,
                'avatarColor' => $profile->avatar_color,
                'isKids'      => (bool) $profile->is_kids,
            ];
        }

        return new WP_REST_Response($response, 200);
    }

    /**
     * Handler: Criar Perfil
     */
    public function rest_create_profile(WP_REST_Request $request): WP_REST_Response
    {
        global $wpdb;
        $user_id = get_current_user_id();
        $table_name = $wpdb->prefix . 'f5tv_profiles';

        $name = sanitize_text_field($request->get_param('name'));
        $avatar_color = sanitize_text_field($request->get_param('avatarColor')) ?: 'bg-f5-red';
        $is_kids = (bool) $request->get_param('isKids');

        if (empty($name)) {
            return new WP_REST_Response(['message' => 'Nome do perfil é obrigatório.'], 400);
        }

        // Limitar máximo de 4 perfis por usuário
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(id) FROM $table_name WHERE user_id = %d",
            $user_id
        ));

        if ($count >= 4) {
            return new WP_REST_Response(['message' => 'Limite máximo de 4 perfis atingido.'], 400);
        }

        $result = $wpdb->insert($table_name, [
            'user_id'      => $user_id,
            'name'         => $name,
            'avatar_color' => $avatar_color,
            'is_kids'      => $is_kids,
        ]);

        if ($result === false) {
            return new WP_REST_Response(['message' => 'Erro ao criar perfil no banco.'], 500);
        }

        $profile_id = $wpdb->insert_id;

        return new WP_REST_Response([
            'id'          => strval($profile_id),
            'userId'      => strval($user_id),
            'name'        => $name,
            'avatarColor' => $avatar_color,
            'isKids'      => $is_kids,
        ], 201);
    }

    /**
     * Handler: Deletar Perfil
     */
    public function rest_delete_profile(WP_REST_Request $request): WP_REST_Response
    {
        global $wpdb;
        $user_id = get_current_user_id();
        $profile_id = intval($request->get_param('id'));
        $table_name = $wpdb->prefix . 'f5tv_profiles';

        // Validar propriedade do perfil
        $owner = $wpdb->get_var($wpdb->prepare(
            "SELECT user_id FROM $table_name WHERE id = %d",
            $profile_id
        ));

        if (!$owner || intval($owner) !== $user_id) {
            return new WP_REST_Response(['message' => 'Perfil não encontrado ou não pertence a você.'], 404);
        }

        // Impedir exclusão se for o único perfil restante
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(id) FROM $table_name WHERE user_id = %d",
            $user_id
        ));

        if ($count <= 1) {
            return new WP_REST_Response(['message' => 'Você precisa manter pelo menos um perfil ativo.'], 400);
        }

        $deleted = $wpdb->delete($table_name, [
            'id'      => $profile_id,
            'user_id' => $user_id,
        ]);

        if ($deleted === false) {
            return new WP_REST_Response(['message' => 'Erro ao excluir perfil.'], 500);
        }

        return new WP_REST_Response(['success' => true], 200);
    }

    /**
     * Handler: Logout
     */
    public function rest_logout(): WP_REST_Response
    {
        wp_logout();

        return new WP_REST_Response(['success' => true], 200);
    }
}
