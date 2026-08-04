<?php
/**
 * F5TV Client Area - WP-CLI Seed
 * Popula tabelas customizadas com dados de exemplo
 */

if (!defined('WP_CLI') || !WP_CLI) {
    return;
}

WP_CLI::add_command('f5tv seed', function () {
    global $wpdb;

    $profiles_table = $wpdb->prefix . 'f5tv_profiles';
    $subs_table = $wpdb->prefix . 'f5tv_subscriptions';
    $history_table = $wpdb->prefix . 'f5tv_watch_history';
    $mylist_table = $wpdb->prefix . 'f5tv_my_list';
    $devices_table = $wpdb->prefix . 'f5tv_devices';

    $users = get_users(['role' => 'subscriber', 'number' => 10]);
    if (!$users) {
        WP_CLI::warning('Nenhum subscriber encontrado. Crie usuários primeiro.');
        return;
    }

    foreach ($users as $user) {
        $profile_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $profiles_table WHERE user_id = %d", $user->ID));
        if (!$profile_count) {
            $wpdb->insert($profiles_table, [
                'user_id'      => $user->ID,
                'name'         => explode(' ', $user->display_name)[0],
                'avatar_color' => 'bg-f5-red',
                'is_kids'      => false,
            ]);
            WP_CLI::log("Perfil criado para {$user->display_name}");
        }

        $has_sub = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $subs_table WHERE user_id = %d", $user->ID));
        if (!$has_sub) {
            $plan_id = 'plano-' . ['basico', 'familia', 'premium'][array_rand(['basico', 'familia', 'premium'])];
            $wpdb->insert($subs_table, [
                'user_id'                 => $user->ID,
                'plan_id'                 => $plan_id,
                'status'                  => 'active',
                'gateway'                 => 'woocommerce',
                'current_period_start'    => current_time('mysql'),
                'current_period_end'      => date('Y-m-d H:i:s', strtotime('+1 month')),
            ]);
            WP_CLI::log("Assinatura criada para {$user->display_name}: {$plan_id}");
        }
    }

    $contents = get_posts([
        'post_type'      => 'f5tv_conteudo',
        'posts_per_page' => 20,
        'post_status'    => 'publish',
    ]);

    if ($contents) {
        foreach ($users as $user) {
            $profile_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM $profiles_table WHERE user_id = %d LIMIT 1", $user->ID));
            if (!$profile_id) continue;

            $content = $contents[array_rand($contents)];
            $content_id = $content->ID;

            $existing = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $history_table WHERE user_id = %d AND profile_id = %d AND content_id = %d",
                $user->ID, $profile_id, $content_id
            ));

            if (!$existing) {
                $watched = rand(60, 3600);
                $total = rand(3600, 7200);
                $progress = min(100, round(($watched / $total) * 100, 2));

                $wpdb->insert($history_table, [
                    'user_id'         => $user->ID,
                    'profile_id'      => $profile_id,
                    'content_id'      => $content_id,
                    'watched_seconds' => $watched,
                    'total_seconds'   => $total,
                    'progress'        => $progress,
                    'completed'       => $progress >= 90 ? 1 : 0,
                ]);
            }

            $existing_list = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $mylist_table WHERE user_id = %d AND profile_id = %d AND content_id = %d",
                $user->ID, $profile_id, $content_id
            ));

            if (!$existing_list && rand(0, 1)) {
                $wpdb->insert($mylist_table, [
                    'user_id'    => $user->ID,
                    'profile_id' => $profile_id,
                    'content_id' => $content_id,
                ]);
            }
        }
        WP_CLI::log('Histórico e lista de favoritos populados.');
    }

    WP_CLI::success('Seed concluído.');
});
