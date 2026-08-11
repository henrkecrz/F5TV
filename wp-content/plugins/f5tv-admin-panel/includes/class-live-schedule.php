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
        add_action('admin_post_f5tv_save_schedule', [$this, 'save_schedule']);
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
        $today = current_time('Y-m-d');
        $today_programs = get_posts([
            'post_type' => 'f5tv_programacao',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => [['key' => 'date', 'value' => $today, 'compare' => '=']],
        ]);
        $online_channels = array_filter($channels, static function ($channel) {
            return get_post_meta($channel->ID, 'status', true) !== 'offline' && get_post_meta($channel->ID, 'active', true);
        });
        if (isset($_GET['new'])) {
            $this->render_form($channels);
            return;
        }
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
                <a href="<?php echo esc_url(admin_url('admin.php?page=f5tv-live-schedule&new=1')); ?>" class="f5-btn-action" style="padding: 0.65rem 1.25rem; font-size: 0.8rem;">
                    ➕ Novo Programa Ao Vivo
                </a>
            </div>

            <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1rem;margin-bottom:1.5rem;">
                <div class="f5-card" style="margin:0;padding:1.1rem;"><div style="color:#9ca3af;font-size:.68rem;text-transform:uppercase;font-family:monospace;font-weight:800;">Canais ativos</div><strong style="display:block;color:#fff;font-size:1.8rem;margin-top:.3rem;"><?php echo count($online_channels); ?><span style="font-size:.8rem;color:#6b7280;font-weight:600;"> / <?php echo count($channels); ?></span></strong></div>
                <div class="f5-card" style="margin:0;padding:1.1rem;"><div style="color:#9ca3af;font-size:.68rem;text-transform:uppercase;font-family:monospace;font-weight:800;">Programas hoje</div><strong style="display:block;color:#fff;font-size:1.8rem;margin-top:.3rem;"><?php echo count($today_programs); ?></strong></div>
                <div class="f5-card" style="margin:0;padding:1.1rem;"><div style="color:#9ca3af;font-size:.68rem;text-transform:uppercase;font-family:monospace;font-weight:800;">Operação</div><strong style="display:block;color:<?php echo $online_channels ? '#34d399' : '#f87171'; ?>;font-size:1rem;margin-top:.65rem;"><?php echo $online_channels ? 'PRONTA PARA EXIBIÇÃO' : 'SEM SINAL CONFIGURADO'; ?></strong></div>
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
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=f5tv-live-schedule&new=1&channel_id=' . $channel->ID)); ?>" class="f5-btn-action" style="background: #1f293d;">
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

    private function render_form(array $channels): void
    {
        wp_enqueue_media();
        $channel_id = absint($_GET['channel_id'] ?? 0);
        $status_options = ['scheduled' => 'Programado', 'live' => 'Ao Vivo', 'premiere' => 'Estreia', 'rerun' => 'Reprise', 'ended' => 'Encerrado'];
        ?>
        <style>
            .f5-schedule-form{max-width:920px;margin:22px 0;color:#f4f4f5;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif}.f5-schedule-form h1{color:#fff;font-size:28px;font-weight:900;letter-spacing:-.04em;margin:0 0 6px}.f5-schedule-form p{color:#9ca3af}.f5-form-card{background:#0c101d;border:1px solid #1f293d;border-radius:16px;padding:24px;box-shadow:0 12px 34px rgba(0,0,0,.35)}.f5-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.f5-form-field{display:flex;flex-direction:column;gap:7px}.f5-form-field.full{grid-column:1/-1}.f5-form-field label{color:#cbd5e1;font-size:12px;font-weight:800}.f5-form-input,.f5-form-select,.f5-form-textarea{width:100%;box-sizing:border-box;background:#060913;border:1px solid #27344d;border-radius:9px;color:#fff;padding:11px 12px}.f5-form-textarea{min-height:100px;resize:vertical}.f5-form-actions{display:flex;align-items:center;gap:10px;margin-top:22px;padding-top:18px;border-top:1px solid #1f293d}.f5-form-primary{border:0;border-radius:9px;padding:11px 18px;background:#e50914;color:#fff;font-weight:900;cursor:pointer}.f5-form-secondary{color:#a1a1aa;text-decoration:none}.f5-form-tip{background:#121b2e;border:1px solid #253654;border-radius:10px;padding:12px 14px;color:#aebbd2;font-size:12px;margin-bottom:18px}@media(max-width:700px){.f5-form-grid{grid-template-columns:1fr}.f5-form-field.full{grid-column:auto}}
        </style>
        <div class="f5-schedule-form wrap">
            <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:18px;margin-bottom:20px"><div><h1>Novo programa ao vivo</h1><p>Monte a programação em poucos passos. O conteúdo ficará disponível na grade e na página ao vivo.</p></div><a class="f5-form-secondary" href="<?php echo esc_url(admin_url('admin.php?page=f5tv-live-schedule')); ?>">← Voltar para a grade</a></div>
            <div class="f5-form-tip">Defina o canal e o horário para que a programação apareça no lugar certo. Você poderá ajustar o status depois sem editar o conteúdo.</div>
            <form class="f5-form-card" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="f5tv_save_schedule"><?php wp_nonce_field('f5tv_save_schedule'); ?>
                <div class="f5-form-grid">
                    <div class="f5-form-field full"><label for="f5-schedule-title">Nome do programa</label><input id="f5-schedule-title" class="f5-form-input" name="title" required placeholder="Ex.: F5 Notícias - Edição da Manhã"></div>
                    <div class="f5-form-field"><label for="f5-schedule-channel">Canal</label><select id="f5-schedule-channel" class="f5-form-select" name="channel_id" required><option value="">Selecione um canal</option><?php foreach ($channels as $channel): ?><option value="<?php echo esc_attr($channel->ID); ?>" <?php selected($channel_id, $channel->ID); ?>><?php echo esc_html($channel->post_title); ?></option><?php endforeach; ?></select></div>
                    <div class="f5-form-field"><label for="f5-schedule-host">Apresentador ou responsável</label><input id="f5-schedule-host" class="f5-form-input" name="host" placeholder="Ex.: Mariana Costa"></div>
                    <div class="f5-form-field"><label for="f5-schedule-date">Data de exibição</label><input id="f5-schedule-date" class="f5-form-input" type="date" name="date" required value="<?php echo esc_attr(current_time('Y-m-d')); ?>"></div>
                    <div class="f5-form-field"><label for="f5-schedule-status">Status</label><select id="f5-schedule-status" class="f5-form-select" name="status"><?php foreach ($status_options as $key => $label): ?><option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></option><?php endforeach; ?></select></div>
                    <div class="f5-form-field"><label for="f5-schedule-start">Hora de início</label><input id="f5-schedule-start" class="f5-form-input" type="time" name="start_time" required></div>
                    <div class="f5-form-field"><label for="f5-schedule-end">Hora de término</label><input id="f5-schedule-end" class="f5-form-input" type="time" name="end_time" required></div>
                    <div class="f5-form-field full"><label for="f5-schedule-image">Imagem de capa (opcional)</label><div style="display:flex;gap:9px;align-items:center"><input id="f5-schedule-image" class="f5-form-input" type="url" name="image_url" placeholder="Selecione ou envie uma imagem" readonly><button type="button" id="f5-select-schedule-image" class="f5-form-primary" style="white-space:nowrap">Selecionar imagem</button></div><div id="f5-schedule-image-preview" style="display:none;margin-top:10px;max-width:280px;border-radius:10px;overflow:hidden;border:1px solid #27344d"><img src="" alt="Prévia da capa" style="display:block;width:100%;height:120px;object-fit:cover"></div></div>
                    <div class="f5-form-field full"><label for="f5-schedule-description">Descrição curta</label><textarea id="f5-schedule-description" class="f5-form-textarea" name="description" placeholder="O que o público encontrará neste programa?"></textarea></div>
                    <label class="f5-form-field" style="flex-direction:row;align-items:center;gap:9px;color:#cbd5e1;font-size:12px"><input type="checkbox" name="is_featured" value="1"> Destacar na programação</label>
                </div>
                <div class="f5-form-actions"><button type="submit" class="f5-form-primary">Salvar programa ao vivo</button><a class="f5-form-secondary" href="<?php echo esc_url(admin_url('admin.php?page=f5tv-live-schedule')); ?>">Cancelar</a></div>
            </form>
        </div>
        <script>
        jQuery(function($) {
            let mediaFrame;
            const input = $('#f5-schedule-image');
            const preview = $('#f5-schedule-image-preview');
            const previewImage = preview.find('img');
            const showPreview = function(url) {
                if (!url) { preview.hide(); return; }
                previewImage.attr('src', url);
                preview.show();
            };
            $('#f5-select-schedule-image').on('click', function(event) {
                event.preventDefault();
                if (mediaFrame) { mediaFrame.open(); return; }
                mediaFrame = wp.media({ title: 'Selecionar capa do programa', button: { text: 'Usar esta imagem' }, multiple: false, library: { type: 'image' } });
                mediaFrame.on('select', function() {
                    const attachment = mediaFrame.state().get('selection').first().toJSON();
                    input.val(attachment.url);
                    showPreview(attachment.url);
                });
                mediaFrame.open();
            });
            input.on('change', function() { showPreview(input.val()); });
        });
        </script>
        <?php
    }

    public function save_schedule(): void
    {
        if (!current_user_can('manage_options')) wp_die('Sem permissão.');
        check_admin_referer('f5tv_save_schedule');
        $title = sanitize_text_field(wp_unslash($_POST['title'] ?? ''));
        $post_id = wp_insert_post(['post_type' => 'f5tv_programacao', 'post_status' => 'publish', 'post_title' => $title, 'post_content' => wp_kses_post(wp_unslash($_POST['description'] ?? ''))], true);
        if (is_wp_error($post_id)) wp_die(esc_html($post_id->get_error_message()));
        $meta = ['channel_id' => absint($_POST['channel_id'] ?? 0), 'host' => sanitize_text_field(wp_unslash($_POST['host'] ?? '')), 'date' => sanitize_text_field($_POST['date'] ?? ''), 'start_time' => sanitize_text_field($_POST['start_time'] ?? ''), 'end_time' => sanitize_text_field($_POST['end_time'] ?? ''), 'status' => sanitize_key($_POST['status'] ?? 'scheduled'), 'image_url' => esc_url_raw(wp_unslash($_POST['image_url'] ?? '')), 'is_featured' => !empty($_POST['is_featured']) ? 1 : 0];
        foreach ($meta as $key => $value) update_post_meta($post_id, $key, $value);
        wp_safe_redirect(admin_url('admin.php?page=f5tv-live-schedule&saved=1'));
        exit;
    }
}
