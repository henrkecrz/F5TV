<?php
/**
 * F5TV Admin Panel - Coupons
 * Gestão de cupons de desconto e promoções
 */

if (!defined('ABSPATH')) {
    exit;
}

class F5TV_Admin_Coupons
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'register_submenu']);
    }

    public function register_submenu(): void
    {
        add_submenu_page(
            'f5tv-dashboard',
            __('Cupons F5 TV', 'f5tv-admin-panel'),
            __('Cupons', 'f5tv-admin-panel'),
            'manage_options',
            'f5tv-coupons',
            [$this, 'render_page']
        );
    }

    public function render_page(): void
    {
        ?>
        <style>
            .f5-admin-wrap { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; color: #f4f4f5; max-width: 1100px; margin: 20px 0; }
            .f5-card { background: #0c101d; border: 1px solid #1f293d; border-radius: 1rem; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
            .f5-title { font-size: 1.5rem; font-weight: 900; color: #ffffff; letter-spacing: -0.02em; display: flex; align-items: center; gap: 0.75rem; }
            .f5-subtitle { color: #9ca3af; font-size: 0.85rem; margin-top: 0.25rem; }
            .f5-coupon-code { font-family: monospace; font-size: 1.1rem; font-weight: 900; color: #e50914; background: #060913; border: 1px dashed #e50914; padding: 0.5rem 1rem; border-radius: 0.5rem; display: inline-block; }
        </style>

        <div class="f5-admin-wrap wrap">
            <div class="f5-card" style="background: linear-gradient(135deg, #0c101d 0%, #151b2e 100%);">
                <h1 class="f5-title">🎟️ Cupons de Desconto & Promoções F5 TV</h1>
                <p class="f5-subtitle">Crie e gerencie códigos promocionais para novos assinantes e campanhas de marketing.</p>
            </div>

            <div class="f5-card">
                <h2 class="f5-title" style="font-size: 1.1rem;">Cupons Ativos da Plataforma</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem; margin-top: 1rem;">
                    <div style="background: #060913; padding: 1.25rem; border-radius: 0.75rem; border: 1px solid #1f293d;">
                        <span class="f5-coupon-code">BENVINDOF5</span>
                        <div style="margin-top: 0.75rem; color: #fff; font-weight: 700; font-size: 0.9rem;">20% de Desconto na Primeira Mensalidade</div>
                        <span style="font-size: 0.75rem; color: #10b981; display: block; margin-top: 0.25rem;">● Cupons aplicáveis em qualquer plano</span>
                    </div>

                    <div style="background: #060913; padding: 1.25rem; border-radius: 0.75rem; border: 1px solid #1f293d;">
                        <span class="f5-coupon-code">F5PREMIUM30</span>
                        <div style="margin-top: 0.75rem; color: #fff; font-weight: 700; font-size: 0.9rem;">30 dias Grátis no Plano Premium</div>
                        <span style="font-size: 0.75rem; color: #10b981; display: block; margin-top: 0.25rem;">● Cupom exclusivo para assinaturas anuais</span>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
