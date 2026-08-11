<?php
/**
 * F5TV Client Area - Checkout / Billing
 * Integração com WooCommerce Subscriptions e gateways de pagamento
 */

if (!defined('ABSPATH')) {
    exit;
}

class F5TV_Checkout
{
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes(): void
    {
        register_rest_route('f5tv/v1', '/checkout/plans', [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [$this, 'get_plans'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route('f5tv/v1', '/checkout/create-subscription', [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => [$this, 'create_subscription'],
            'permission_callback' => [$this, 'check_logged_in'],
        ]);
    }

    public function check_logged_in(): bool
    {
        return is_user_logged_in();
    }

    public function get_plans(): WP_REST_Response
    {
        $plans = apply_filters('f5tv_available_plans', [
            [
                'id'       => 'plano-basico',
                'name'     => 'Básico',
                'price'    => 19.90,
                'currency' => 'BRL',
                'features' => ['Catálogo sob demanda', '1 tela', 'Qualidade HD'],
                'active'   => true,
            ],
            [
                'id'       => 'plano-familia',
                'name'     => 'Família',
                'price'    => 34.90,
                'currency' => 'BRL',
                'features' => ['Catálogo completo', '3 telas', 'Full HD', 'Ao vivo'],
                'active'   => true,
            ],
            [
                'id'       => 'plano-premium',
                'name'     => 'Premium',
                'price'    => 49.90,
                'currency' => 'BRL',
                'features' => ['Catálogo completo', '5 telas', 'Full HD/4K', 'Ao vivo', 'Estreias e especiais'],
                'active'   => true,
            ],
        ]);

        return new WP_REST_Response($plans, 200);
    }

    public function create_subscription(WP_REST_Request $request): WP_REST_Response
    {
        $user_id = get_current_user_id();
        $plan_id = sanitize_text_field($request->get_param('plan_id'));
        $gateway = sanitize_text_field($request->get_param('gateway')) ?: 'stripe';

        if (empty($plan_id)) {
            return new WP_REST_Response(['message' => 'Plano inválido.'], 400);
        }

        $plans = $this->get_plans()->get_data();
        $plan = null;
        foreach ($plans as $p) {
            if ($p['id'] === $plan_id) {
                $plan = $p;
                break;
            }
        }

        if (!$plan || !$plan['active']) {
            return new WP_REST_Response(['message' => 'Plano não disponível.'], 400);
        }

        if (class_exists('WooCommerce') && class_exists('WC_Subscriptions')) {
            return $this->create_wc_subscription($user_id, $plan);
        }

        return new WP_REST_Response([
            'message' => 'Checkout não configurado. Integre WooCommerce Subscriptions ou outro gateway.',
            'planId'  => $plan_id,
        ], 501);
    }

    private function create_wc_subscription(int $user_id, array $plan): WP_REST_Response
    {
        $product = get_page_by_path($plan['id'], OBJECT, 'product');
        if (!$product) {
            return new WP_REST_Response(['message' => 'Produto do plano não encontrado no WooCommerce.'], 400);
        }

        $subscription = wcs_create_subscription([
            'status'      => 'pending',
            'customer_id' => $user_id,
        ]);

        if (is_wp_error($subscription)) {
            return new WP_REST_Response(['message' => 'Erro ao criar assinatura.'], 500);
        }

        $subscription->add_product($product, 1);
        $subscription->calculate_totals();

        $checkout_url = wc_get_checkout_url() . '?subscription=' . $subscription->get_id();

        return new WP_REST_Response([
            'success'      => true,
            'subscriptionId' => strval($subscription->get_id()),
            'checkoutUrl'  => $checkout_url,
        ], 201);
    }

    public function sync_subscription_status($subscription, string $new_status, string $old_status): void
    {
        global $wpdb;
        $user_id = $subscription->get_user_id();
        if (!$user_id) {
            return;
        }

        $items = $subscription->get_items();
        $plan_id = 'plano-basico';
        foreach ($items as $item) {
            $product = $item->get_product();
            if ($product) {
                $sku = $product->get_sku();
                if ($sku) {
                    $plan_id = $sku;
                    break;
                }
            }
        }

        $status = 'inactive';
        if ($new_status === 'active') {
            $status = 'active';
        } elseif ($new_status === 'cancelled') {
            $status = 'canceled';
        } elseif ($new_status === 'on-hold') {
            $status = 'past_due';
        }

        $table_name = $wpdb->prefix . 'f5tv_subscriptions';
        $has_table = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) === $table_name;
        if (!$has_table) {
            update_user_meta($user_id, 'f5tv_plan', $plan_id);
            update_user_meta($user_id, 'f5tv_subscription_status', $status);
            return;
        }

        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_name WHERE user_id = %d LIMIT 1",
            $user_id
        ));

        if ($existing) {
            $wpdb->update(
                $table_name,
                [
                    'plan_id'                 => $plan_id,
                    'status'                  => $status,
                    'gateway'                 => 'woocommerce',
                    'gateway_subscription_id' => strval($subscription->get_id()),
                    'current_period_end'      => $subscription->get_date('next_payment'),
                ],
                ['user_id' => $user_id]
            );
        } else {
            $wpdb->insert(
                $table_name,
                [
                    'user_id'                 => $user_id,
                    'plan_id'                 => $plan_id,
                    'status'                  => $status,
                    'gateway'                 => 'woocommerce',
                    'gateway_subscription_id' => strval($subscription->get_id()),
                    'current_period_start'    => $subscription->get_date('start'),
                    'current_period_end'      => $subscription->get_date('next_payment'),
                ]
            );
        }

        update_user_meta($user_id, 'f5tv_plan', $plan_id);
        update_user_meta($user_id, 'f5tv_subscription_status', $status);
    }
}
