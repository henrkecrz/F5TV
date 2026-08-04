<?php
/**
 * F5TV_Subscription class
 * Sincronização de assinaturas com WooCommerce Subscriptions
 */

if (!defined('ABSPATH')) {
    exit;
}

class F5TV_Subscription
{
    public function __construct()
    {
        // Hooks do WooCommerce Subscriptions para sincronização
        add_action('woocommerce_subscription_status_updated', [$this, 'sync_subscription_status'], 10, 3);
    }

    /**
     * Sincroniza o status da assinatura do WC com a tabela customizada
     * 
     * @param WC_Subscription $subscription
     * @param string $new_status
     * @param string $old_status
     */
    public function sync_subscription_status($subscription, string $new_status, string $old_status): void
    {
        global $wpdb;
        $user_id = $subscription->get_user_id();
        if (!$user_id) {
            return;
        }

        // Buscar qual item da assinatura é o plano do streaming
        $items = $subscription->get_items();
        $plan_id = 'plano-basico'; // Fallback padrão
        foreach ($items as $item) {
            $product = $item->get_product();
            if ($product) {
                // Tenta mapear o SKU ou slug do produto ao ID do plano
                $sku = $product->get_sku();
                if ($sku) {
                    $plan_id = $sku;
                    break;
                }
            }
        }

        // Mapear status do WC para status da plataforma F5
        $status = 'inactive';
        if ($new_status === 'active') {
            $status = 'active';
        } elseif ($new_status === 'cancelled') {
            $status = 'canceled';
        } elseif ($new_status === 'on-hold') {
            $status = 'past_due';
        }

        // Sincronizar na tabela customizada wp_f5tv_subscriptions
        $table_name = $wpdb->prefix . 'f5tv_subscriptions';
        
        // Verifica se a tabela existe
        $has_table = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) === $table_name;
        if (!$has_table) {
            // Se a tabela não existe por algum motivo, salva no usermeta de fallback
            update_user_meta($user_id, 'f5tv_plan', $plan_id);
            update_user_meta($user_id, 'f5tv_subscription_status', $status);
            return;
        }

        // Verifica se já existe registro
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

        // Atualiza também os user metas para garantir que os hooks de cache/verificação rápida funcionem
        update_user_meta($user_id, 'f5tv_plan', $plan_id);
        update_user_meta($user_id, 'f5tv_subscription_status', $status);
    }
}
