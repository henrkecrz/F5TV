<?php
/**
 * F5TV Admin Panel - Live Schedule
 * Grade de programação ao vivo dos canais F5 TV
 */

if (!defined('ABSPATH')) {
    exit;
}

class F5TV_Admin_Live_Schedule
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'register_submenu']);
    }

    public function register_submenu(): void
    {
        add_submenu_page(
            'f5tv-dashboard',
            __('Programação F5 TV', 'f5tv-admin-panel'),
            __('Programação', 'f5tv-admin-panel'),
            'manage_options',
            'f5tv-live-schedule',
            [$this, 'render_page']
        );
    }

    public function render_page(): void
    {
        $channels = get_posts([
            'post_type'      => 'f5tv_canal',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ]);
        ?>
        <style>
            .f5-admin-wrap { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; color: #f4f4f5; max-width: 1100px; margin: 20px 0; }
            .f5-card { background: #0c101d; border: 1px solid #1f293d; border-radius: 1rem; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
            .f5-title { font-size: 1.5rem; font-weight: 900; color: #ffffff; letter-spacing: -0.02em; display: flex; align-items: center; gap: 0.75rem; }
            .f5-subtitle { color: #9ca3af; font-size: 0.85rem; margin-top: 0.25rem; }
            .f5-btn-action { background: #e50914; color: #ffffff; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; border: none; padding: 0.4rem 0.85rem; border-radius: 0.4rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem; }
            .f5-btn-action:hover { background: #b80710; color: #fff; }
            .f5-table { width: 100%; border-collapse: collapse; margin-top: 1rem; text-align: left; font-size: 0.85rem; }
            .f5-table th { background: #060913; color: #9ca3af; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; padding: 0.85rem 1rem; border-bottom: 1px solid #1f293d; }
            .f5-table td { padding: 0.85rem 1rem; border-bottom: 1px solid #151d2f; color: #e4e4e7; }
        </style>

        <div class="f5-admin-wrap wrap">
            <div class="f5-card" style="background: linear-gradient(135deg, #0c101d 0%, #151b2e 100%); flex-wrap: wrap; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 class="f5-title">📡 Grade de Programação ao Vivo</h1>
                    <p class="f5-subtitle">Gerencie as transmissões em tempo real, horários de exibição e canais da plataforma F5 TV.</p>
                </div>
                <a href="<?php echo esc_url(admin_url('post-new.php?post_type=f5tv_programacao')); ?>" class="f5-btn-action" style="padding: 0.65rem 1.25rem; font-size: 0.8rem;">
                    ➕ Novo Programa Ao Vivo
                </a>
            </div>

            <div class="f5-card">
                <table class="f5-table">
                    <thead>
                        <tr>
                            <th>Canal de Transmissão</th>
                            <th>Status do Sinal</th>
                            <th>Programas Agendados</th>
                            <th>Ações de Gerenciamento</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($channels)): ?>
                            <tr>
                                <td colspan="4" style="text-align: center; color: #6b7280; padding: 2rem;">Nenhum canal cadastrado. Cadastre um canal em "F5 Streaming > Canal".</td>
                            </tr>
                        <?php else: foreach ($channels as $channel):
                            $programs = get_posts([
                                'post_type'      => 'f5tv_programacao',
                                'posts_per_page' => -1,
                                'meta_key'       => 'channel_id',
                                'meta_value'     => $channel->ID,
                                'orderby'        => 'meta_value',
                                'meta_key'       => 'start_time',
                                'order'          => 'ASC',
                            ]);
                            $stream_status = get_field('status', $channel->ID) ?: 'online';
                        ?>
                            <tr>
                                <td style="font-weight: 800; color: #ffffff; font-size: 0.95rem;"><?php echo esc_html($channel->post_title); ?></td>
                                <td>
                                    <span style="font-family: monospace; font-size: 0.65rem; padding: 0.2rem 0.5rem; border-radius: 0.25rem; font-weight: 700; text-transform: uppercase; background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);">
                                        ● <?php echo esc_html(strtoupper($stream_status)); ?>
                                    </span>
                                </td>
                                <td>
                                    <span style="font-weight: 700; color: #e50914;"><?php echo count($programs); ?> programa(s)</span>
                                    <?php if ($programs): ?><div style="margin-top:0.45rem;color:#9ca3af;font-size:0.72rem;line-height:1.6;"><?php foreach (array_slice($programs, 0, 3) as $program): ?><div><strong style="color:#e4e4e7;"><?php echo esc_html(get_post_meta($program->ID, 'start_time', true) ?: '--:--'); ?></strong> <?php echo esc_html($program->post_title); ?><?php $date = get_post_meta($program->ID, 'date', true); if ($date) echo ' · ' . esc_html(date_i18n('d/m', strtotime($date))); ?></div><?php endforeach; ?></div><?php endif; ?>
                                </td>
                                <td style="display: flex; gap: 0.5rem;">
                                    <a href="<?php echo esc_url(get_edit_post_link($channel->ID)); ?>" class="f5-btn-action">
                                        ✏️ Editar Canal
                                    </a>
                                    <a href="<?php echo esc_url(admin_url('post-new.php?post_type=f5tv_programacao')); ?>" class="f5-btn-action" style="background: #1f293d;">
                                        🗓️ Adicionar Horário
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }
}
