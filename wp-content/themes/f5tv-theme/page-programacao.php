<?php
/**
 * Template Name: Programação
 * Description: Guia de programação eletrônico (EPG) dos canais da F5 TV.
 */

get_header();

// Channels
$channels_query = new WP_Query([
    'post_type'      => 'f5tv_canal',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
]);
$channels = [];
if ($channels_query->have_posts()) {
    while ($channels_query->have_posts()) {
        $channels_query->the_post();
        $channels[] = [
            'id'       => get_the_ID(),
            'name'     => get_the_title(),
            'logoText' => get_field('logo_text') ?: get_the_title(),
        ];
    }
    wp_reset_postdata();
}

$demo_schedule = false;
if (empty($channels)) {
    $channels[] = [
        'id'       => 'demo-f5tv',
        'name'     => 'F5 TV Demonstração',
        'logoText' => 'F5',
    ];
}

// Schedules
$schedules_query = new WP_Query([
    'post_type'      => 'f5tv_programacao',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
]);
$schedules = [];
if ($schedules_query->have_posts()) {
    while ($schedules_query->have_posts()) {
        $schedules_query->the_post();
        $schedules[] = [
            'id'        => get_the_ID(),
            'channelId' => get_field('channel_id'),
            'title'     => get_the_title(),
            'date'      => get_field('date'),
            'startTime' => get_field('start_time'),
            'endTime'   => get_field('end_time'),
            'status'    => get_field('status') ?: 'scheduled',
            'host'      => get_field('host'),
        ];
    }
    wp_reset_postdata();
}

if (empty($schedules)) {
    $demo_schedule = true;
    $schedules = [
        ['id' => 'demo-1', 'channelId' => 'demo-f5tv', 'title' => 'Bom Dia F5', 'date' => date('Y-m-d'), 'startTime' => '08:00', 'endTime' => '10:00', 'status' => 'scheduled', 'host' => 'Equipe F5 TV'],
        ['id' => 'demo-2', 'channelId' => 'demo-f5tv', 'title' => 'F5 Entrevista', 'date' => date('Y-m-d'), 'startTime' => '10:30', 'endTime' => '12:00', 'status' => 'scheduled', 'host' => 'Redação F5 TV'],
        ['id' => 'demo-3', 'channelId' => 'demo-f5tv', 'title' => 'Jornal F5 Ao Vivo', 'date' => date('Y-m-d'), 'startTime' => '13:00', 'endTime' => '14:00', 'status' => 'scheduled', 'host' => 'Jornalismo F5'],
        ['id' => 'demo-4', 'channelId' => 'demo-f5tv', 'title' => 'Portugal em Foco', 'date' => date('Y-m-d'), 'startTime' => '18:00', 'endTime' => '19:30', 'status' => 'scheduled', 'host' => 'Equipe F5 TV'],
    ];
}
?>

<div class="min-h-screen bg-f5-blue text-white font-sans selection:bg-f5-red p-6 md:p-10">
    <div class="max-w-7xl mx-auto flex flex-col gap-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-zinc-900 pb-4">
            <div>
                <span class="text-f5-red font-mono font-black text-xs tracking-widest uppercase">EPG ELETRÔNICO</span>
                <h1 class="text-3xl font-black tracking-tight mt-1">Guia da Programação</h1>
            </div>
            <a href="<?php echo esc_url(home_url('/ao-vivo/')); ?>" class="bg-f5-red hover:bg-f5-red-700 text-white font-mono font-bold text-xs uppercase px-4 py-2 rounded-xl transition">
                Assistir Ao Vivo Agora
            </a>
        </div>

        <?php if ($demo_schedule): ?>
            <div class="bg-[#10284a] border border-f5-red/30 rounded-xl px-4 py-3 text-xs text-white/80 font-mono uppercase tracking-wider">
                <span class="text-f5-red font-black">Programação demonstrativa:</span> horários e programas abaixo são exemplos de apresentação.
            </div>
        <?php endif; ?>

        <div class="bg-f5-blue-950 border border-zinc-900 rounded-2xl p-6 overflow-x-auto shadow-2xl">
            <table class="w-full text-left border-collapse text-xs font-semibold min-w-[600px]">
                <thead>
                    <tr class="border-b border-zinc-900 text-zinc-500 font-mono text-[10px] uppercase">
                        <th class="py-4 pr-4">Canal</th>
                        <th class="py-4 px-4">Horário</th>
                        <th class="py-4 px-4">Programa</th>
                        <th class="py-4 px-4">Apresentador</th>
                        <th class="py-4 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-900 text-zinc-300">
                    <?php if (empty($schedules)): ?>
                        <tr>
                            <td colspan="5" class="py-8 text-center text-zinc-500 italic">Nenhum programa agendado para exibição.</td>
                        </tr>
                    <?php else: foreach ($schedules as $sched):
                        $ch_name = 'F5 TV';
                        foreach ($channels as $c) {
                            if ($c['id'] == $sched['channelId']) {
                                $ch_name = $c['name'];
                                break;
                            }
                        }
                    ?>
                        <tr class="hover:bg-f5-blue-900/40 transition">
                            <td class="py-4 pr-4 font-mono font-bold text-f5-red"><?php echo esc_html($ch_name); ?></td>
                            <td class="py-4 px-4 font-mono text-zinc-400"><?php echo esc_html($sched['startTime'] . ' - ' . $sched['endTime']); ?></td>
                            <td class="py-4 px-4 text-white font-bold"><?php echo esc_html($sched['title']); ?></td>
                            <td class="py-4 px-4 text-zinc-400"><?php echo esc_html($sched['host'] ?: 'Produção F5 TV'); ?></td>
                            <td class="py-4 px-4 text-center">
                                <?php if ($sched['status'] === 'live'): ?>
                                    <span class="bg-f5-red text-white text-[9px] font-mono font-black px-2 py-0.5 rounded uppercase animate-pulse">NO AR</span>
                                <?php elseif ($sched['status'] === 'ended'): ?>
                                    <span class="bg-zinc-800 text-zinc-500 text-[9px] font-mono px-2 py-0.5 rounded uppercase">Encerrado</span>
                                <?php else: ?>
                                    <span class="bg-f5-blue-900 text-zinc-300 text-[9px] font-mono px-2 py-0.5 rounded uppercase">Agendado</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php get_footer(); ?>
