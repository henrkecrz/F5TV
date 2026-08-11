<?php
/**
 * Template Name: Ao Vivo
 * Central operacional de canais ao vivo e grade atual.
 */

get_header();

$now = current_time('timestamp');
$today = wp_date('Y-m-d', $now);
$current_minutes = ((int) wp_date('H', $now) * 60) + (int) wp_date('i', $now);

$channel_posts = get_posts([
    'post_type' => 'f5tv_canal',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'meta_query' => [
        ['key' => 'active', 'value' => '1', 'compare' => '='],
        ['key' => 'stream_url', 'value' => '', 'compare' => '!='],
        ['key' => 'status', 'value' => 'offline', 'compare' => '!='],
    ],
    'orderby' => 'title',
    'order' => 'ASC',
]);

$schedule_posts = get_posts([
    'post_type' => 'f5tv_programacao',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'meta_query' => [['key' => 'date', 'value' => $today, 'compare' => '=']],
    'meta_key' => 'start_time',
    'orderby' => 'meta_value',
    'order' => 'ASC',
]);

$schedule = [];
foreach ($schedule_posts as $program) {
    $start = (string) get_post_meta($program->ID, 'start_time', true);
    $end = (string) get_post_meta($program->ID, 'end_time', true);
    $start_minutes = ((int) substr($start, 0, 2) * 60) + (int) substr($start, 3, 2);
    $end_minutes = ((int) substr($end, 0, 2) * 60) + (int) substr($end, 3, 2);
    $schedule[] = [
        'id' => $program->ID,
        'channel_id' => absint(get_post_meta($program->ID, 'channel_id', true)),
        'title' => $program->post_title,
        'host' => (string) get_post_meta($program->ID, 'host', true),
        'start' => substr($start, 0, 5),
        'end' => substr($end, 0, 5),
        'start_minutes' => $start_minutes,
        'end_minutes' => $end_minutes,
        'is_live' => $current_minutes >= $start_minutes && $current_minutes < $end_minutes,
    ];
}

$channels = [];
foreach ($channel_posts as $channel) {
    $terms = get_the_terms($channel->ID, 'f5tv_categoria');
    $channel_schedule = array_values(array_filter($schedule, static function ($item) use ($channel) {
        return $item['channel_id'] === $channel->ID;
    }));
    $live_program = null;
    $next_program = null;
    foreach ($channel_schedule as $item) {
        if ($item['is_live']) $live_program = $item;
        if (!$next_program && $item['start_minutes'] > $current_minutes) $next_program = $item;
    }
    $channels[] = [
        'id' => $channel->ID,
        'name' => $channel->post_title,
        'logo' => get_post_meta($channel->ID, 'logo_text', true) ?: 'F5',
        'category' => ($terms && !is_wp_error($terms)) ? $terms[0]->name : 'Geral',
        'status' => get_post_meta($channel->ID, 'status', true) ?: 'offline',
        'stream' => rest_url('f5tv/v1/live/stream/' . $channel->ID),
        'live' => $live_program,
        'next' => $next_program,
        'programs' => $channel_schedule,
    ];
}

$active_channel = $channels[0] ?? null;
foreach ($channels as $channel) {
    if ($channel['live']) {
        $active_channel = $channel;
        break;
    }
}
?>

<main class="f5-live-page min-h-screen bg-f5-blue text-white font-sans selection:bg-f5-red selection:text-white">
    <div class="max-w-[1500px] mx-auto px-4 sm:px-6 lg:px-8 py-7 lg:py-10">
        <header class="flex flex-col lg:flex-row lg:items-end justify-between gap-5 mb-7">
            <div>
                <div class="flex items-center gap-2 text-[10px] font-mono font-black tracking-[.22em] uppercase text-f5-red">
                    <span class="w-2 h-2 rounded-full bg-f5-red <?php echo $active_channel && $active_channel['live'] ? 'animate-pulse' : ''; ?>"></span>
                    Central F5 TV
                </div>
                <h1 class="text-4xl md:text-6xl font-black tracking-[-.05em] mt-2">Ao Vivo</h1>
                <p class="text-zinc-400 max-w-xl mt-3 text-sm md:text-base">Acompanhe os canais oficiais, veja o que está no ar e programe seu próximo conteúdo.</p>
            </div>
            <div class="flex gap-2 text-[10px] font-mono uppercase font-bold">
                <span class="px-3 py-2 rounded-lg border border-zinc-800 bg-f5-blue-950 text-zinc-400"><?php echo esc_html(wp_date('D, d M', $now)); ?></span>
                <a href="<?php echo esc_url(home_url('/programacao/')); ?>" class="px-3 py-2 rounded-lg bg-f5-red text-white hover:bg-red-700 transition">Ver grade completa</a>
            </div>
        </header>

        <?php if (!$active_channel): ?>
            <section class="rounded-3xl border border-zinc-900 bg-f5-blue-950 p-10 md:p-20 text-center">
                <div class="mx-auto w-16 h-16 rounded-2xl bg-zinc-900 flex items-center justify-center text-zinc-600 text-2xl">TV</div>
                <h2 class="mt-5 text-2xl font-black text-white">Nenhum canal disponível</h2>
                <p class="mt-2 text-sm text-zinc-500 max-w-md mx-auto">A programação ao vivo ainda não foi configurada. Cadastre um canal ativo com uma URL de transmissão no painel administrativo.</p>
                <a href="<?php echo esc_url(home_url('/programacao/')); ?>" class="inline-flex mt-6 px-5 py-3 rounded-xl bg-zinc-800 text-zinc-200 text-xs font-mono uppercase font-bold hover:bg-zinc-700 transition">Consultar programação</a>
            </section>
        <?php else: ?>
            <section class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_340px] gap-5">
                <div class="min-w-0">
                    <div class="relative aspect-video rounded-3xl overflow-hidden border border-zinc-800 bg-black shadow-2xl shadow-black/40">
                        <video id="f5tv-live-player" class="w-full h-full object-contain bg-black" controls autoplay playsinline preload="metadata" data-stream="<?php echo esc_url($active_channel['stream']); ?>"></video>
                        <div class="absolute top-4 left-4 flex items-center gap-2 px-3 py-1.5 rounded-lg bg-f5-red text-white text-[10px] font-mono font-black uppercase shadow-lg"><span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span><span id="f5tv-live-label"><?php echo $active_channel['live'] ? 'No ar agora' : 'Sinal ao vivo'; ?></span></div>
                        <div id="f5tv-player-state" class="hidden absolute inset-0 items-center justify-center bg-black/65 backdrop-blur-sm text-center p-6"><div><div class="text-f5-red text-xs font-mono font-black uppercase">Sinal indisponível</div><p class="text-zinc-400 text-sm mt-2">Este canal está sem transmissão no momento.</p></div></div>
                    </div>
                    <div class="mt-5 rounded-2xl border border-zinc-900 bg-f5-blue-950 p-5 md:p-6">
                        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                            <div class="flex items-center gap-3"><div id="f5tv-channel-logo" class="w-12 h-12 rounded-xl bg-f5-red flex items-center justify-center font-black text-white"><?php echo esc_html($active_channel['logo']); ?></div><div><h2 id="f5tv-channel-name" class="text-xl font-black"><?php echo esc_html($active_channel['name']); ?></h2><p id="f5tv-channel-category" class="text-[10px] font-mono uppercase tracking-wider text-zinc-500 mt-1"><?php echo esc_html($active_channel['category']); ?></p></div></div>
                            <div id="f5tv-now-card" class="md:text-right"><?php if ($active_channel['live']): ?><span class="text-[10px] uppercase font-mono font-black text-f5-red">No ar agora</span><div class="text-white font-bold mt-1"><?php echo esc_html($active_channel['live']['title']); ?></div><div class="text-[11px] text-zinc-500 mt-1"><?php echo esc_html($active_channel['live']['start'] . ' - ' . $active_channel['live']['end']); ?></div><?php else: ?><span class="text-[10px] uppercase font-mono font-black text-zinc-500">Fora da grade</span><div class="text-zinc-400 text-sm mt-1">Aguardando próxima transmissão</div><?php endif; ?></div>
                        </div>
                    </div>
                </div>

                <aside class="rounded-3xl border border-zinc-900 bg-f5-blue-950 p-4 md:p-5">
                    <div class="flex items-center justify-between mb-4"><h2 class="text-xs font-mono uppercase tracking-widest font-black text-zinc-300">Canais oficiais</h2><span class="text-[10px] text-zinc-600 font-mono"><?php echo count($channels); ?> ativos</span></div>
                    <div class="space-y-2" id="f5tv-channel-list">
                        <?php foreach ($channels as $index => $channel): ?><button type="button" class="f5tv-channel-btn w-full text-left p-3 rounded-xl border transition <?php echo $channel['id'] === $active_channel['id'] ? 'bg-f5-blue-900 border-f5-red/60' : 'bg-f5-blue-950 border-zinc-900 hover:border-zinc-700'; ?>" data-id="<?php echo esc_attr($channel['id']); ?>" data-stream="<?php echo esc_url($channel['stream']); ?>" data-name="<?php echo esc_attr($channel['name']); ?>" data-logo="<?php echo esc_attr($channel['logo']); ?>" data-category="<?php echo esc_attr($channel['category']); ?>" data-live="<?php echo esc_attr($channel['live'] ? $channel['live']['title'] : ''); ?>" data-next="<?php echo esc_attr($channel['next'] ? $channel['next']['title'] . ' · ' . $channel['next']['start'] : ''); ?>" data-schedule="<?php echo esc_attr(wp_json_encode($channel['programs'])); ?>"><span class="flex items-center gap-3"><span class="w-9 h-9 rounded-lg bg-zinc-900 flex items-center justify-center text-[10px] font-black text-zinc-300"><?php echo esc_html($channel['logo']); ?></span><span class="min-w-0"><strong class="block text-xs text-zinc-200 truncate"><?php echo esc_html($channel['name']); ?></strong><small class="block text-[10px] text-zinc-600 font-mono mt-1"><?php echo esc_html($channel['live'] ? 'NO AR · ' . $channel['live']['title'] : ($channel['next'] ? 'PRÓXIMO · ' . $channel['next']['start'] : 'SEM GRADE')); ?></small></span><span class="ml-auto w-2 h-2 rounded-full shrink-0 <?php echo $channel['live'] ? 'bg-f5-red animate-pulse' : 'bg-zinc-700'; ?>"></span></span></button><?php endforeach; ?>
                    </div>
                </aside>
            </section>

            <section class="mt-5 grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-5">
                <div class="rounded-2xl border border-zinc-900 bg-f5-blue-950 p-5"><div class="flex items-center justify-between mb-4"><h2 class="text-xs font-mono uppercase tracking-widest font-black text-zinc-300">Grade de hoje</h2><span class="text-[10px] font-mono text-zinc-600">Horário local</span></div><div class="space-y-2" id="f5tv-schedule-list"><?php $visible_programs = $active_channel['programs']; if (!$visible_programs): ?><p class="text-sm text-zinc-500 py-5">Nenhum programa cadastrado para este canal hoje.</p><?php else: foreach ($visible_programs as $program): ?><div class="flex items-center gap-3 p-3 rounded-xl bg-f5-blue-900/40 border border-zinc-900 <?php echo $program['is_live'] ? 'border-f5-red/50' : ''; ?>"><span class="w-14 text-[11px] font-mono font-bold text-zinc-500"><?php echo esc_html($program['start']); ?></span><span class="flex-1"><strong class="block text-sm text-zinc-200"><?php echo esc_html($program['title']); ?></strong><small class="text-[10px] text-zinc-600"><?php echo esc_html($program['host'] ?: 'Produção F5 TV'); ?></small></span><?php if ($program['is_live']): ?><span class="text-[9px] font-mono font-black text-f5-red uppercase">No ar</span><?php endif; ?></div><?php endforeach; endif; ?></div></div>
                <div class="rounded-2xl border border-zinc-900 bg-gradient-to-br from-f5-blue-950 to-[#111827] p-5"><span class="text-[10px] font-mono uppercase tracking-widest text-f5-red font-black">Operação</span><h2 class="text-xl font-black mt-2">Sinal oficial F5 TV</h2><p class="text-sm text-zinc-500 mt-2 leading-relaxed">Os canais exibidos aqui são cadastrados e ativados pela Gestão de Canais. Sem stream configurado, o canal não aparece para o público.</p><a href="<?php echo esc_url(home_url('/programacao/')); ?>" class="inline-flex mt-5 text-xs font-mono uppercase font-bold text-zinc-300 hover:text-white">Abrir programação completa →</a></div>
            </section>
        <?php endif; ?>
    </div>
</main>

<?php if ($active_channel): ?><script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script><script>
(function(){
    const player = document.getElementById('f5tv-live-player');
    const state = document.getElementById('f5tv-player-state');
    let hls = null;
    function loadStream(url){
        if (!player || !url) return;
        if (hls) { hls.destroy(); hls = null; }
        state?.classList.add('hidden');
        player.pause(); player.removeAttribute('src'); player.load();
        if (window.Hls && Hls.isSupported()) { hls = new Hls({enableWorker:true, lowLatencyMode:true}); hls.loadSource(url); hls.attachMedia(player); hls.on(Hls.Events.MANIFEST_PARSED,()=>player.play().catch(()=>{})); hls.on(Hls.Events.ERROR,(_,data)=>{ if(data.fatal) state?.classList.remove('hidden'); }); }
        else { player.src=url; player.play().catch(()=>{}); }
    }
    player?.addEventListener('error',()=>state?.classList.remove('hidden'));
    loadStream(player?.dataset.stream);
    document.querySelectorAll('.f5tv-channel-btn').forEach(btn=>btn.addEventListener('click',()=>{
        document.querySelectorAll('.f5tv-channel-btn').forEach(item=>item.classList.remove('bg-f5-blue-900','border-f5-red/60'));
        btn.classList.add('bg-f5-blue-900','border-f5-red/60'); loadStream(btn.dataset.stream);
        document.getElementById('f5tv-channel-name').textContent=btn.dataset.name;
        document.getElementById('f5tv-channel-logo').textContent=btn.dataset.logo;
        document.getElementById('f5tv-channel-category').textContent=btn.dataset.category;
        const schedule=document.getElementById('f5tv-schedule-list'); const items=JSON.parse(btn.dataset.schedule||'[]'); schedule.innerHTML=items.length ? items.map(item=>'<div class="flex items-center gap-3 p-3 rounded-xl bg-f5-blue-900/40 border border-zinc-900"><span class="w-14 text-[11px] font-mono font-bold text-zinc-500">'+item.start+'</span><span class="flex-1"><strong class="block text-sm text-zinc-200">'+String(item.title).replace(/</g,'&lt;')+'</strong><small class="text-[10px] text-zinc-600">'+String(item.host||'Produção F5 TV').replace(/</g,'&lt;')+'</small></span>'+(item.is_live?'<span class="text-[9px] font-mono font-black text-f5-red uppercase">No ar</span>':'')+'</div>').join('') : '<p class="text-sm text-zinc-500 py-5">Nenhum programa cadastrado para este canal hoje.</p>';
        const now=document.getElementById('f5tv-now-card'); now.innerHTML=btn.dataset.live ? '<span class="text-[10px] uppercase font-mono font-black text-f5-red">No ar agora</span><div class="text-white font-bold mt-1">'+btn.dataset.live.replace(/</g,'&lt;')+'</div>' : '<span class="text-[10px] uppercase font-mono font-black text-zinc-500">Fora da grade</span><div class="text-zinc-400 text-sm mt-1">Aguardando próxima transmissão</div>';
    }));
})();
</script><?php endif; ?>

<?php get_footer(); ?>
