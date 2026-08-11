<?php
/** F5 TV channel management panel. */

if (!defined('ABSPATH')) {
    exit;
}

class F5TV_Admin_Channels
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'register_live_pages'], 99);
        add_action('admin_post_f5tv_save_channel', [$this, 'save_channel']);
        add_action('admin_post_f5tv_delete_channel', [$this, 'delete_channel']);
    }

    public function register_live_pages(): void
    {
        $parent = 'edit.php?post_type=f5tv_canal';
        remove_submenu_page($parent, $parent);
        remove_submenu_page($parent, 'post-new.php?post_type=f5tv_canal');
        remove_submenu_page($parent, 'edit.php?post_type=f5tv_programacao');

        add_submenu_page($parent, __('Todos os Canais', 'f5tv-admin-panel'), __('Todos os Canais', 'f5tv-admin-panel'), 'manage_options', 'f5tv-live-channels', [$this, 'render_page']);
        add_submenu_page($parent, __('Adicionar Novo Canal', 'f5tv-admin-panel'), __('Adicionar Novo Canal', 'f5tv-admin-panel'), 'manage_options', 'f5tv-live-channel-new', [$this, 'render_page']);
        add_submenu_page($parent, __('Grade de Programação', 'f5tv-admin-panel'), __('Grade de Programação', 'f5tv-admin-panel'), 'manage_options', 'f5tv-live-schedule', 'f5tv_admin_render_live_schedule');
    }

    public function save_channel(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die('Sem permissao.');
        }
        check_admin_referer('f5tv_save_channel');

        $id = absint($_POST['channel_id'] ?? 0);
        $title = sanitize_text_field($_POST['channel_title'] ?? '');
        if ($title === '') {
            $title = 'Novo Canal F5 TV';
        }
        $id = wp_insert_post([
            'ID' => $id,
            'post_title' => $title,
            'post_type' => 'f5tv_canal',
            'post_status' => 'publish',
        ]);

        if ($id && !is_wp_error($id)) {
            update_post_meta($id, 'logo_text', sanitize_text_field($_POST['logo_text'] ?? ''));
            update_post_meta($id, 'status', sanitize_key($_POST['status'] ?? 'offline'));
            update_post_meta($id, 'active', isset($_POST['active']) ? 1 : 0);
            update_post_meta($id, 'stream_url', esc_url_raw($_POST['stream_url'] ?? ''));
        }
        wp_safe_redirect(admin_url('admin.php?page=f5tv-live-channels&saved=1'));
        exit;
    }

    public function delete_channel(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die('Sem permissao.');
        }
        check_admin_referer('f5tv_delete_channel');
        $id = absint($_POST['channel_id'] ?? 0);
        if ($id && get_post_type($id) === 'f5tv_canal') {
            wp_trash_post($id);
        }
        wp_safe_redirect(admin_url('admin.php?page=f5tv-live-channels&deleted=1'));
        exit;
    }

    public function render_page(): void
    {
        $editing = absint($_GET['edit'] ?? 0);
        $is_new_page = sanitize_key($_GET['page'] ?? '') === 'f5tv-live-channel-new';
        $channel = $editing ? get_post($editing) : null;
        $channels = get_posts([
            'post_type' => 'f5tv_canal',
            'post_status' => ['publish', 'draft'],
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
        ]);
        $value = static function (string $key, $default = '') use ($channel) {
            return $channel ? (get_post_meta($channel->ID, $key, true) ?: $default) : $default;
        };
        ?>
        <style>
            .f5-channel-wrap{max-width:1180px;color:#f4f4f5}.f5-channel-grid{display:grid;grid-template-columns:minmax(280px,360px) 1fr;gap:18px}.f5-channel-card{background:#0c101d;border:1px solid #1f293d;border-radius:16px;padding:22px;box-shadow:0 10px 30px rgba(0,0,0,.35)}.f5-channel-title{color:#fff;font-size:24px;font-weight:900}.f5-channel-label{display:block;color:#9ca3af;font-size:11px;font-weight:800;text-transform:uppercase;margin:14px 0 6px}.f5-channel-input{width:100%;box-sizing:border-box;background:#060913;border:1px solid #27344d;color:#fff;border-radius:8px;padding:10px}.f5-channel-btn{border:0;border-radius:8px;padding:10px 14px;background:#e50914;color:#fff;font-weight:800;cursor:pointer}.f5-channel-table{width:100%;border-collapse:collapse}.f5-channel-table th,.f5-channel-table td{text-align:left;padding:13px 10px;border-bottom:1px solid #1f293d}.f5-channel-table th{color:#9ca3af;font-size:10px;text-transform:uppercase}.f5-channel-table td{color:#e4e4e7}.f5-channel-pill{font:700 10px monospace;text-transform:uppercase;padding:4px 7px;border-radius:5px;background:rgba(16,185,129,.14);color:#34d399}
            @media(max-width:800px){.f5-channel-grid{grid-template-columns:1fr}}
        </style>
        <div class="wrap f5-channel-wrap">
            <div class="f5-channel-card" style="background:linear-gradient(135deg,#0c101d,#151b2e);margin:20px 0">
                <div class="f5-channel-title">Gestao de Canais</div>
                <p style="color:#9ca3af">Cadastre os sinais ao vivo, defina o status e organize a grade por canal.</p>
            </div>
            <?php if (isset($_GET['saved'])): ?><div class="notice notice-success"><p>Canal salvo.</p></div><?php endif; ?>
            <?php if (isset($_GET['deleted'])): ?><div class="notice notice-success"><p>Canal removido.</p></div><?php endif; ?>
            <div class="f5-channel-grid">
                <div class="f5-channel-card">
                    <h2 style="color:#fff;margin-top:0"><?php echo $channel ? 'Editar canal' : 'Novo canal'; ?></h2>
                    <form method="post" action="admin-post.php">
                        <?php wp_nonce_field('f5tv_save_channel'); ?><input type="hidden" name="action" value="f5tv_save_channel"><input type="hidden" name="channel_id" value="<?php echo esc_attr($channel->ID ?? 0); ?>">
                        <label class="f5-channel-label">Nome do canal</label><input class="f5-channel-input" name="channel_title" value="<?php echo esc_attr($channel->post_title ?? ''); ?>" required>
                        <label class="f5-channel-label">Identificador visual</label><input class="f5-channel-input" name="logo_text" value="<?php echo esc_attr($value('logo_text')); ?>" placeholder="NEWS, SPORT, F5">
                        <label class="f5-channel-label">URL do stream</label><input class="f5-channel-input" name="stream_url" value="<?php echo esc_attr($value('stream_url')); ?>" placeholder="https://...m3u8">
                        <label class="f5-channel-label">Status</label><select class="f5-channel-input" name="status"><option value="online" <?php selected($value('status','online'),'online'); ?>>Online</option><option value="offline" <?php selected($value('status','online'),'offline'); ?>>Offline</option></select>
                        <label style="display:flex;gap:8px;align-items:center;margin:15px 0;color:#d1d5db"><input type="checkbox" name="active" value="1" <?php checked($value('active',1),1); ?>> Canal ativo</label>
                        <button class="f5-channel-btn" type="submit">Salvar canal</button>
                        <?php if ($channel): ?><a href="<?php echo esc_url(admin_url('admin.php?page=f5tv-live-channels')); ?>" style="color:#9ca3af;margin-left:10px">Cancelar</a><?php endif; ?>
                    </form>
                </div>
                <?php if (!$is_new_page): ?><div class="f5-channel-card"><h2 style="color:#fff;margin-top:0">Canais cadastrados</h2><table class="f5-channel-table"><thead><tr><th>Canal</th><th>Sinal</th><th>Status</th><th>Acoes</th></tr></thead><tbody>
                <?php if (!$channels): ?><tr><td colspan="4">Nenhum canal cadastrado.</td></tr><?php endif; ?>
                <?php foreach ($channels as $item): ?><tr><td><strong><?php echo esc_html($item->post_title); ?></strong><br><small style="color:#6b7280"><?php echo esc_html(get_post_meta($item->ID,'logo_text',true)); ?></small></td><td><?php echo get_post_meta($item->ID,'stream_url',true) ? 'Configurado' : 'Pendente'; ?></td><td><span class="f5-channel-pill" style="<?php echo get_post_meta($item->ID,'status',true)==='offline'?'background:rgba(239,68,68,.15);color:#fca5a5':''; ?>"><?php echo esc_html(get_post_meta($item->ID,'status',true) ?: 'offline'); ?></span></td><td><a href="<?php echo esc_url(admin_url('admin.php?page=f5tv-live-channel-new&edit='.$item->ID)); ?>">Editar</a> <form method="post" action="admin-post.php" style="display:inline" onsubmit="return confirm('Remover este canal?')"><?php wp_nonce_field('f5tv_delete_channel'); ?><input type="hidden" name="action" value="f5tv_delete_channel"><input type="hidden" name="channel_id" value="<?php echo esc_attr($item->ID); ?>"><button type="submit" style="background:none;border:0;color:#f87171;cursor:pointer">Deletar</button></form></td></tr><?php endforeach; ?>
                </tbody></table></div><?php endif; ?>
            </div>
        </div>
        <?php
    }
}
