<?php
/**
 * F5TV Admin Panel - Subscribers Management
 * Interface Administrativa Premium de Gestão de Assinantes
 */

if (!defined('ABSPATH')) {
    exit;
}

class F5TV_Admin_Subscribers
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'register_submenu']);
    }

    public function register_submenu(): void
    {
        add_submenu_page(
            'f5tv-dashboard',
            __('Assinantes F5 TV', 'f5tv-admin-panel'),
            __('Assinantes', 'f5tv-admin-panel'),
            'manage_options',
            'f5tv-subscribers',
            [$this, 'render_page']
        );
    }

    public function render_page(): void
    {
        $search = sanitize_text_field($_GET['s'] ?? '');
        $status_filter = sanitize_text_field($_GET['status'] ?? '');

        $query_args = [
            'number' => 50,
        ];
        if ($search) {
            $query_args['search'] = '*' . $search . '*';
        }

        $query = new WP_User_Query($query_args);
        $users = $query->get_results();
        ?>
        <style>
            .f5-admin-wrap { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; color: #f4f4f5; max-width: 1100px; margin: 20px 0; }
            .f5-card { background: #0c101d; border: 1px solid #1f293d; border-radius: 1rem; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
            .f5-title { font-size: 1.5rem; font-weight: 900; color: #ffffff; letter-spacing: -0.02em; display: flex; align-items: center; gap: 0.75rem; }
            .f5-subtitle { color: #9ca3af; font-size: 0.85rem; margin-top: 0.25rem; }
            .f5-input { background: #060913; border: 1px solid #27344d; color: #ffffff; padding: 0.6rem 0.85rem; border-radius: 0.5rem; font-size: 0.85rem; }
            .f5-btn-filter { background: #e50914; color: #ffffff; font-weight: 800; font-size: 0.8rem; text-transform: uppercase; border: none; padding: 0.6rem 1.25rem; border-radius: 0.5rem; cursor: pointer; transition: all 0.2s; }
            .f5-btn-filter:hover { background: #b80710; }
            .f5-table { width: 100%; border-collapse: collapse; margin-top: 1rem; text-align: left; font-size: 0.85rem; }
            .f5-table th { background: #060913; color: #9ca3af; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; padding: 0.85rem 1rem; border-bottom: 1px solid #1f293d; }
            .f5-table td { padding: 0.85rem 1rem; border-bottom: 1px solid #151d2f; color: #e4e4e7; }
            .f5-table tr:hover td { background: rgba(229, 9, 20, 0.05); }
            .f5-badge { font-family: monospace; font-size: 0.65rem; padding: 0.2rem 0.5rem; border-radius: 0.25rem; font-weight: 700; text-transform: uppercase; }
            .f5-badge-active { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
            .f5-badge-inactive { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); }
        </style>

        <div class="f5-admin-wrap wrap">
            <div class="f5-card" style="background: linear-gradient(135deg, #0c101d 0%, #151b2e 100%);">
                <h1 class="f5-title">👥 Gestão de Assinantes F5 TV</h1>
                <p class="f5lead f5-subtitle">Acompanhe a base de clientes cadastrados, planos de assinatura e status dos acessos.</p>

                <form method="get" style="margin-top: 1.25rem; display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
                    <input type="hidden" name="page" value="f5tv-subscribers">
                    <input type="text" name="s" value="<?php echo esc_attr($search); ?>" placeholder="Buscar por nome ou e-mail..." class="f5-input" style="min-width: 250px;">
                    <select name="status" class="f5-input" style="min-width: 140px;">
                        <option value="">Todos os Status</option>
                        <option value="active" <?php selected($status_filter, 'active'); ?>>Ativo</option>
                        <option value="inactive" <?php selected($status_filter, 'inactive'); ?>>Inativo</option>
                        <option value="past_due" <?php selected($status_filter, 'past_due'); ?>>Inadimplente</option>
                    </select>
                    <button type="submit" class="f5-btn-filter">🔍 Filtrar Base</button>
                </form>
            </div>

            <div class="f5-card">
                <table class="f5-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome do Assinante</th>
                            <th>E-mail</th>
                            <th>Plano Atual</th>
                            <th>Status da Conta</th>
                            <th>Data de Cadastro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: #6b7280; padding: 2rem;">Nenhum assinante encontrado com os filtros aplicados.</td>
                            </tr>
                        <?php else: foreach ($users as $user):
                            $plan = get_user_meta($user->ID, 'f5tv_plan', true) ?: 'Plano Básico';
                            $user_status = get_user_meta($user->ID, 'f5tv_subscription_status', true) ?: 'active';
                            if ($status_filter && $user_status !== $status_filter) continue;
                            $badge_class = ($user_status === 'active') ? 'f5-badge-active' : 'f5-badge-inactive';
                        ?>
                            <tr>
                                <td style="font-family: monospace; font-weight: 700; color: #9ca3af;">#<?php echo esc_html($user->ID); ?></td>
                                <td style="font-weight: 700; color: #ffffff;"><?php echo esc_html($user->display_name); ?></td>
                                <td><?php echo esc_html($user->user_email); ?></td>
                                <td><span style="color: #e50914; font-weight: 700; font-size: 0.8rem;"><?php echo esc_html($plan); ?></span></td>
                                <td><span class="f5-badge <?php echo $badge_class; ?>"><?php echo esc_html(strtoupper($user_status)); ?></span></td>
                                <td style="color: #9ca3af; font-size: 0.8rem;"><?php echo esc_html(date('d/m/Y H:i', strtotime($user->user_registered))); ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }
}
