<?php
/**
 * F5TV Admin Panel - Settings
 * Interface Administrativa Premium de Configurações da Plataforma F5 TV
 */

if (!defined('ABSPATH')) {
    exit;
}

class F5TV_Admin_Settings
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'register_submenu']);
        add_action('admin_post_f5tv_save_settings', [$this, 'save_settings']);
    }

    public function register_submenu(): void
    {
        add_submenu_page(
            'f5tv-dashboard',
            __('Configurações F5 TV', 'f5tv-admin-panel'),
            __('Configurações', 'f5tv-admin-panel'),
            'manage_options',
            'f5tv-settings',
            [$this, 'render_page']
        );
    }

    public function save_settings(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die('Sem permissão.');
        }

        check_admin_referer('f5tv_save_settings');

        $fields = [
            'f5tv_cdn_url',
            'f5tv_player_watermark_enabled',
            'f5tv_player_watermark_text',
            'f5tv_vimeo_enabled',
            'f5tv_youtube_enabled',
            'f5tv_email_from_name',
            'f5tv_email_from_address',
            'f5tv_webhook_secret',
            'f5tv_max_devices_per_account',
        ];

        foreach ($fields as $field) {
            $value = sanitize_text_field($_POST[$field] ?? '');
            if ($value === '1' || $value === 'true') $value = 1;
            if ($value === '0' || $value === 'false') $value = 0;
            update_option($field, $value);
        }

        wp_redirect(add_query_arg('page', 'f5tv-settings&saved=1', admin_url('admin.php')));
        exit;
    }

    public function render_page(): void
    {
        $saved = isset($_GET['saved']) && $_GET['saved'] === '1';
        
        $cdn_url           = get_option('f5tv_cdn_url', '');
        $watermark_enabled = get_option('f5tv_player_watermark_enabled', 1);
        $watermark_text    = get_option('f5tv_player_watermark_text', 'F5 TV STREAMING');
        $vimeo_enabled     = get_option('f5tv_vimeo_enabled', 1);
        $youtube_enabled   = get_option('f5tv_youtube_enabled', 1);
        $email_name        = get_option('f5tv_email_from_name', 'F5 TV Plataforma');
        $email_address     = get_option('f5tv_email_from_address', get_option('admin_email'));
        $webhook_secret    = get_option('f5tv_webhook_secret', wp_generate_password(24, false));
        $max_devices       = get_option('f5tv_max_devices_per_account', 3);
        ?>
        <style>
            .f5-admin-wrap { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; color: #f4f4f5; max-width: 1100px; margin: 20px 0; }
            .f5-card { background: #0c101d; border: 1px solid #1f293d; border-radius: 1rem; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
            .f5-title { font-size: 1.5rem; font-weight: 900; color: #ffffff; letter-spacing: -0.02em; display: flex; align-items: center; gap: 0.75rem; }
            .f5-subtitle { color: #9ca3af; font-size: 0.85rem; margin-top: 0.25rem; }
            .f5-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.25rem; margin-top: 1.25rem; }
            .f5-field-group { display: flex; flex-col; flex-direction: column; gap: 0.35rem; }
            .f5-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #d1d5db; }
            .f5-input { background: #060913; border: 1px solid #27344d; color: #ffffff; padding: 0.65rem 0.85rem; border-radius: 0.5rem; font-size: 0.9rem; width: 100%; box-sizing: border-box; transition: all 0.2s; }
            .f5-input:focus { border-color: #e50914; outline: none; box-shadow: 0 0 0 3px rgba(229, 9, 20, 0.15); }
            .f5-toggle { display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; background: #060913; border: 1px solid #27344d; border-radius: 0.5rem; }
            .f5-btn-save { background: #e50914; color: #ffffff; font-weight: 800; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; border: none; padding: 0.85rem 2rem; border-radius: 0.5rem; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.5rem; }
            .f5-btn-save:hover { background: #b80710; transform: translateY(-1px); box-shadow: 0 10px 20px rgba(229,9,20,0.3); }
            .f5-badge { font-family: monospace; font-size: 0.65rem; padding: 0.2rem 0.5rem; border-radius: 0.25rem; background: rgba(229,9,20,0.15); color: #e50914; border: 1px solid rgba(229,9,20,0.3); font-weight: 700; text-transform: uppercase; }
            .f5-success-notice { background: #064e3b; border: 1px solid #059669; color: #34d399; padding: 0.85rem 1.25rem; border-radius: 0.5rem; font-weight: 700; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; }
        </style>

        <div class="f5-admin-wrap wrap">
            
            <!-- Header Hero -->
            <div class="f5-card" style="background: linear-gradient(135deg, #0c101d 0%, #151b2e 100%);">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span class="f5-badge">Painel de Controle F5 TV</span>
                            <span class="f5-badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; border-color: rgba(16, 185, 129, 0.3);">v1.0.0 Online</span>
                        </div>
                        <h1 class="f5-title" style="margin-top: 0.5rem;">⚙️ Configurações do Streaming</h1>
                        <p class="f5-subtitle">Gerencie os servidores CDN, reprodutor de vídeo, notificações por e-mail e integração via Webhook.</p>
                    </div>
                </div>
            </div>

            <?php if ($saved): ?>
                <div class="f5-success-notice">
                    <span>✅</span> Configurações salvas com sucesso! As alterações foram aplicadas ao streaming.
                </div>
            <?php endif; ?>

            <form method="post" action="admin-post.php">
                <?php wp_nonce_field('f5tv_save_settings'); ?>
                <input type="hidden" name="action" value="f5tv_save_settings">

                <!-- Bloco 1: CDN & Servidores de Mídia -->
                <div class="f5-card">
                    <h2 class="f5-title" style="font-size: 1.1rem;">📡 CDN & Distribuição de Vídeo</h2>
                    <p class="f5-subtitle">Configure a URL base do seu provedor de CDN (Bunny.net, Cloudflare Stream ou AWS CloudFront).</p>
                    
                    <div class="f5-grid">
                        <div class="f5-field-group" style="grid-column: span 2;">
                            <label class="f5-label" for="f5tv_cdn_url">URL Base do CDN / Stream Host</label>
                            <input type="text" id="f5tv_cdn_url" name="f5tv_cdn_url" value="<?php echo esc_attr($cdn_url); ?>" class="f5-input" placeholder="ex: https://video.f5tv.com.br/hls/">
                            <span style="font-size: 0.75rem; color: #6b7280; margin-top: 0.2rem;">Substitui a URL padrão de armazenamento para entrega rápida com baixa latência.</span>
                        </div>
                    </div>
                </div>

                <!-- Bloco 2: Player & Marca D'água -->
                <div class="f5-card">
                    <h2 class="f5-title" style="font-size: 1.1rem;">🎬 Player & Motores de Vídeo</h2>
                    <p class="f5-subtitle">Habilite provedores externos (Vimeo / YouTube) e controle o carimbo de segurança (watermark).</p>

                    <div class="f5-grid">
                        <div class="f5-toggle">
                            <div>
                                <strong style="font-size: 0.85rem; color: #fff; display: block;">Habilitar Suporte ao Vimeo</strong>
                                <span style="font-size: 0.75rem; color: #9ca3af;">Permite cadastrar links do vimeo.com ou player.vimeo.com</span>
                            </div>
                            <input type="checkbox" id="f5tv_vimeo_enabled" name="f5tv_vimeo_enabled" value="1" <?php checked($vimeo_enabled, 1); ?> style="width: 1.2rem; height: 1.2rem; accent-color: #e50914; cursor: pointer;">
                        </div>

                        <div class="f5-toggle">
                            <div>
                                <strong style="font-size: 0.85rem; color: #fff; display: block;">Habilitar Suporte ao YouTube</strong>
                                <span style="font-size: 0.75rem; color: #9ca3af;">Permite cadastrar trailers ou vídeos do youtube.com</span>
                            </div>
                            <input type="checkbox" id="f5tv_youtube_enabled" name="f5tv_youtube_enabled" value="1" <?php checked($youtube_enabled, 1); ?> style="width: 1.2rem; height: 1.2rem; accent-color: #e50914; cursor: pointer;">
                        </div>

                        <div class="f5-toggle">
                            <div>
                                <strong style="font-size: 0.85rem; color: #fff; display: block;">Marca D'água (Watermark) no Player</strong>
                                <span style="font-size: 0.75rem; color: #9ca3af;">Exibe o logotipo/texto no canto superior do vídeo</span>
                            </div>
                            <input type="checkbox" id="f5tv_player_watermark_enabled" name="f5tv_player_watermark_enabled" value="1" <?php checked($watermark_enabled, 1); ?> style="width: 1.2rem; height: 1.2rem; accent-color: #e50914; cursor: pointer;">
                        </div>

                        <div class="f5-field-group">
                            <label class="f5-label" for="f5tv_player_watermark_text">Texto da Marca D'água</label>
                            <input type="text" id="f5tv_player_watermark_text" name="f5tv_player_watermark_text" value="<?php echo esc_attr($watermark_text); ?>" class="f5-input" placeholder="ex: F5 TV STREAMING">
                        </div>

                        <div class="f5-field-group">
                            <label class="f5-label" for="f5tv_max_devices_per_account">Máximo de Dispositivos por Assinatura</label>
                            <input type="number" id="f5tv_max_devices_per_account" name="f5tv_max_devices_per_account" value="<?php echo esc_attr($max_devices); ?>" min="1" max="10" class="f5-input">
                        </div>
                    </div>
                </div>

                <!-- Bloco 3: E-mail & Notificações -->
                <div class="f5-card">
                    <h2 class="f5-title" style="font-size: 1.1rem;">✉️ Comunicação & E-mails de Sistema</h2>
                    <p class="f5-subtitle">Defina o nome e o endereço de remetente para e-mails de boas-vindas, cobrança e redefinição de senha.</p>

                    <div class="f5-grid">
                        <div class="f5-field-group">
                            <label class="f5-label" for="f5tv_email_from_name">Nome do Remetente</label>
                            <input type="text" id="f5tv_email_from_name" name="f5tv_email_from_name" value="<?php echo esc_attr($email_name); ?>" class="f5-input">
                        </div>

                        <div class="f5-field-group">
                            <label class="f5-label" for="f5tv_email_from_address">Endereço de E-mail de Envio</label>
                            <input type="email" id="f5tv_email_from_address" name="f5tv_email_from_address" value="<?php echo esc_attr($email_address); ?>" class="f5-input">
                        </div>
                    </div>
                </div>

                <!-- Bloco 4: Webhooks & API -->
                <div class="f5-card">
                    <h2 class="f5-title" style="font-size: 1.1rem;">🔑 Integrações & Segredos de Webhook</h2>
                    <p class="f5-subtitle">Chave de segurança usada para validar webhooks de plataformas de pagamento (Asaas, Mercado Pago, Stripe).</p>

                    <div class="f5-grid">
                        <div class="f5-field-group" style="grid-column: span 2;">
                            <label class="f5-label" for="f5tv_webhook_secret">Chave Secreta do Webhook (Secret Key)</label>
                            <input type="text" id="f5tv_webhook_secret" name="f5tv_webhook_secret" value="<?php echo esc_attr($webhook_secret); ?>" class="f5-input font-mono">
                            <span style="font-size: 0.75rem; color: #6b7280; margin-top: 0.2rem;">Forneça esta chave no painel do seu gateway de pagamento para validar notificações push de renovação.</span>
                        </div>
                    </div>
                </div>

                <!-- Botão de Salvar -->
                <div style="display: flex; justify-content: flex-end; margin-top: 1rem;">
                    <button type="submit" class="f5-btn-save">
                        <span>💾 Salvar Configurações</span>
                    </button>
                </div>
            </form>
        </div>
        <?php
    }
}
