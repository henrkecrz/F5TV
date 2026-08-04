<?php
/**
 * F5TV Admin Panel - Dashboard Hub
 * Central de Comando F5 TV com o Design System Oficial do Produto
 */

if (!defined('ABSPATH')) {
    exit;
}

class F5TV_Admin_Dashboard
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'register_submenu']);
        add_action('wp_dashboard_setup', [$this, 'register_dashboard_widgets']);
    }

    public function register_submenu(): void
    {
        add_submenu_page(
            'f5tv-dashboard',
            __('F5 Dashboard', 'f5tv-admin-panel'),
            __('F5 Dashboard', 'f5tv-admin-panel'),
            'manage_options',
            'f5tv-dashboard-hub',
            [$this, 'render_page']
        );
    }

    public function register_dashboard_widgets(): void
    {
        wp_add_dashboard_widget(
            'f5tv_dashboard_overview',
            '🎬 F5 TV - Visão Geral do Streaming',
            [$this, 'render_overview_widget']
        );
    }

    public function render_overview_widget(): void
    {
        $this->render_stats_grid();
    }

    public function render_stats_grid(): void
    {
        $users = count_users();
        $subscriber_count = isset($users['avail_roles']['subscriber']) ? $users['avail_roles']['subscriber'] : 1;

        global $wpdb;
        $table_subs = $wpdb->prefix . 'f5tv_subscriptions';
        $active_subs = 0;
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_subs'") === $table_subs) {
            $active_subs = intval($wpdb->get_var("SELECT COUNT(*) FROM $table_subs WHERE status = 'active'"));
        }
        if ($active_subs === 0) $active_subs = $subscriber_count;

        $series_count = wp_count_posts('f5tv_serie')->publish ?? 0;
        $content_count = wp_count_posts('f5tv_conteudo')->publish ?? 0;
        $channels_count = wp_count_posts('f5tv_canal')->publish ?? 0;
        ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem;">
            <div style="background: #060913; border: 1px solid #1f293d; padding: 1.25rem; border-radius: 0.75rem; text-align: center;">
                <div style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: #9ca3af; letter-spacing: 0.05em;">Assinantes Base</div>
                <div style="font-size: 2rem; font-weight: 900; color: #e50914; margin-top: 0.25rem;"><?php echo intval($subscriber_count); ?></div>
            </div>
            <div style="background: #060913; border: 1px solid #1f293d; padding: 1.25rem; border-radius: 0.75rem; text-align: center;">
                <div style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: #9ca3af; letter-spacing: 0.05em;">Assinaturas Ativas</div>
                <div style="font-size: 2rem; font-weight: 900; color: #10b981; margin-top: 0.25rem;"><?php echo intval($active_subs); ?></div>
            </div>
            <div style="background: #060913; border: 1px solid #1f293d; padding: 1.25rem; border-radius: 0.75rem; text-align: center;">
                <div style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: #9ca3af; letter-spacing: 0.05em;">Séries Ativas</div>
                <div style="font-size: 2rem; font-weight: 900; color: #ffffff; margin-top: 0.25rem;"><?php echo intval($series_count); ?></div>
            </div>
            <div style="background: #060913; border: 1px solid #1f293d; padding: 1.25rem; border-radius: 0.75rem; text-align: center;">
                <div style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: #9ca3af; letter-spacing: 0.05em;">Conteúdos Sob Demanda</div>
                <div style="font-size: 2rem; font-weight: 900; color: #ffffff; margin-top: 0.25rem;"><?php echo intval($content_count); ?></div>
            </div>
            <div style="background: #060913; border: 1px solid #1f293d; padding: 1.25rem; border-radius: 0.75rem; text-align: center;">
                <div style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: #9ca3af; letter-spacing: 0.05em;">Canais Ao Vivo</div>
                <div style="font-size: 2rem; font-weight: 900; color: #3b82f6; margin-top: 0.25rem;"><?php echo intval($channels_count); ?></div>
            </div>
        </div>
        <?php
    }

    public function render_page(): void
    {
        ?>
        <div class="wrap" style="max-width: 1200px; margin: 20px 0;">
            <!-- Hero Header da Central F5 TV -->
            <div class="postbox" style="background: linear-gradient(135deg, #0c101d 0%, #151b2e 100%) !important; padding: 2rem !important; margin-bottom: 2rem !important;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <span style="font-family: monospace; font-size: 11px; padding: 4px 10px; background: rgba(229, 9, 20, 0.15); color: #e50914; border: 1px solid rgba(229, 9, 20, 0.3); border-radius: 4px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em;">
                            Plataforma F5 TV Streaming
                        </span>
                        <h1 style="margin-top: 0.75rem; font-size: 2rem; font-weight: 900; color: #fff; letter-spacing: -0.03em;">
                            🚀 Central de Comando F5 TV
                        </h1>
                        <p style="color: #9ca3af; font-size: 0.9rem; margin-top: 0.25rem;">
                            Gerenciamento unificado de produções, métricas de assinantes, player com suporte a Vimeo e servidor de transmissão.
                        </p>
                    </div>
                    <div style="display: flex; gap: 0.75rem;">
                        <a href="<?php echo esc_url(admin_url('post-new.php?post_type=f5tv_conteudo')); ?>" class="button button-primary" style="padding: 10px 20px !important; font-size: 12px !important;">
                            🎬 Novo Vídeo / Filme
                        </a>
                        <a href="<?php echo esc_url(admin_url('post-new.php?post_type=f5tv_serie')); ?>" class="button" style="background: #1f293d !important; padding: 10px 20px !important; font-size: 12px !important;">
                            🍿 Nova Série
                        </a>
                    </div>
                </div>
            </div>

            <!-- Métricas Globais -->
            <div class="postbox" style="margin-bottom: 2rem !important;">
                <h2 style="font-size: 1.1rem; font-weight: 800; color: #fff; margin-bottom: 1rem;">📊 Visão Geral do Sistema</h2>
                <?php $this->render_stats_grid(); ?>
            </div>

            <!-- Atalhos Rápidos & Ações da Plataforma -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
                
                <div class="postbox">
                    <h2 style="font-size: 1rem; font-weight: 800; color: #fff;">🎬 Gestão de Conteúdos & Séries</h2>
                    <p style="color: #9ca3af; font-size: 0.8rem; margin-top: 0.25rem;">Cadastre novos vídeos, importe URLs do Vimeo ou defina episódios de séries.</p>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 1rem;">
                        <a href="<?php echo esc_url(admin_url('edit.php?post_type=f5tv_conteudo')); ?>" class="button" style="background: #060913 !important; border: 1px solid #1f293d !important; width: 100%; text-align: left; justify-content: flex-start;">
                            🍿 Ver Todos os Conteúdos (Filmes/Episódios)
                        </a>
                        <a href="<?php echo esc_url(admin_url('edit.php?post_type=f5tv_serie')); ?>" class="button" style="background: #060913 !important; border: 1px solid #1f293d !important; width: 100%; text-align: left; justify-content: flex-start;">
                            📺 Ver Todas as Séries
                        </a>
                        <a href="<?php echo esc_url(admin_url('edit-tags.php?taxonomy=f5tv_categoria&post_type=f5tv_conteudo')); ?>" class="button" style="background: #060913 !important; border: 1px solid #1f293d !important; width: 100%; text-align: left; justify-content: flex-start;">
                            🏷️ Categorias & Gêneros
                        </a>
                    </div>
                </div>

                <div class="postbox">
                    <h2 style="font-size: 1rem; font-weight: 800; color: #fff;">👥 Assinantes & Assinaturas</h2>
                    <p style="color: #9ca3af; font-size: 0.8rem; margin-top: 0.25rem;">Monitore clientes ativos, históricos de renovação e acessos simultâneos.</p>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 1rem;">
                        <a href="<?php echo esc_url(admin_url('admin.php?page=f5tv-subscribers')); ?>" class="button" style="background: #060913 !important; border: 1px solid #1f293d !important; width: 100%; text-align: left; justify-content: flex-start;">
                            👥 Consultar Base de Assinantes
                        </a>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=f5tv-finance')); ?>" class="button" style="background: #060913 !important; border: 1px solid #1f293d !important; width: 100%; text-align: left; justify-content: flex-start;">
                            💳 Relatório de Receita Recorrente (MRR)
                        </a>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=f5tv-settings')); ?>" class="button" style="background: #060913 !important; border: 1px solid #1f293d !important; width: 100%; text-align: left; justify-content: flex-start;">
                            ⚙️ Configurar CDN, Player & Vimeo
                        </a>
                    </div>
                </div>

            </div>
        </div>
        <?php
    }
}
