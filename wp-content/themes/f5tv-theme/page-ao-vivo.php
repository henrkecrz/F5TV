<?php
/**
 * Template Name: Ao Vivo
 * Description: Página de transmissão simultânea ao vivo com seleção de canais, EPG e chat.
 */

get_header();

// Buscar canais cadastrados
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
            'id'        => get_the_ID(),
            'name'      => get_the_title(),
            'logoText'  => get_field('logo_text') ?: get_the_title(),
            'streamUrl' => get_field('stream_url') ?: 'https://assets.mixkit.co/videos/preview/mixkit-software-developer-working-on-his-computer-34289-large.mp4',
            'status'    => get_field('status') ?: 'online',
            'active'    => (bool) get_field('active'),
            'category'  => get_the_terms(get_the_ID(), 'f5tv_categoria') ? get_the_terms(get_the_ID(), 'f5tv_categoria')[0]->name : 'Geral',
            'demo'      => false,
        ];
    }
    wp_reset_postdata();
}

$demo_channel = false;
if (empty($channels)) {
    $demo_channel = true;
    $channels[] = [
        'id'        => 'demo-f5tv',
        'name'      => 'F5 TV Demonstração',
        'logoText'  => 'F5',
        'streamUrl' => 'https://assets.mixkit.co/videos/preview/mixkit-software-developer-working-on-his-computer-34289-large.mp4',
        'status'    => 'demo',
        'active'    => true,
        'category'  => 'Demonstração',
        'demo'      => true,
    ];
}

$active_channel = $channels[0] ?? null;

// Buscar programações
$schedules_query = new WP_Query([
    'post_type'      => 'f5tv_programacao',
    'posts_per_page' => 20,
    'post_status'    => 'publish',
]);
$schedules = [];
if ($schedules_query->have_posts()) {
    while ($schedules_query->have_posts()) {
        $schedules_query->the_post();
        $channel_id = get_field('channel_id');
        $schedules[] = [
            'id'        => get_the_ID(),
            'channelId' => $channel_id,
            'title'     => get_the_title(),
            'startTime' => get_field('start_time'),
            'endTime'   => get_field('end_time'),
            'status'    => get_field('status') ?: 'scheduled',
            'host'      => get_field('host'),
        ];
    }
    wp_reset_postdata();
}
?>

<div class="min-h-screen bg-f5-blue text-white font-sans selection:bg-f5-red p-6 md:p-10">
    <div class="max-w-7xl mx-auto flex flex-col gap-6">
        
        <!-- Header summary -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-zinc-900 pb-4">
            <div>
                <span class="text-f5-red font-mono font-black text-xs tracking-widest uppercase flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-f5-red animate-pulse block"></span>
                    <?php echo $demo_channel ? 'Transmissão Demonstrativa' : 'Transmissão Simultânea Ao Vivo'; ?>
                </span>
                <h1 class="text-3xl font-black tracking-tight mt-1">Central de Transmissões</h1>
            </div>
            <div class="flex items-center gap-3 bg-f5-blue-950 border border-zinc-900 px-4 py-2 rounded-xl text-xs text-zinc-400 font-mono">
                <span class="text-f5-red">&#128101;</span>
                <span id="f5tv-live-viewers"><?php echo $demo_channel ? 'Sinal demonstrativo · audiência simulada' : '1.240 assinantes assistindo agora'; ?></span>
            </div>
        </div>

        <?php if (empty($channels)): ?>
            <div class="bg-f5-blue-950 border border-zinc-900 p-16 text-center text-zinc-600 rounded-3xl flex flex-col items-center gap-3">
                <h3 class="font-bold text-base text-zinc-450">Nenhum canal no ar no momento</h3>
                <p class="text-xs">Aguarde o retorno da transmissão oficial F5 TV.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Main video section -->
                <div class="lg:col-span-3 flex flex-col gap-5">
                    <div class="relative aspect-video bg-black rounded-2xl overflow-hidden shadow-2xl border border-zinc-900">
                        <video id="f5tv-live-player" src="<?php echo esc_url($active_channel['streamUrl']); ?>" autoplay loop muted playsinline controls preload="metadata" class="w-full h-full object-cover"></video>
                        <div class="absolute top-4 left-4 bg-f5-red text-white font-mono font-extrabold text-[10px] tracking-wider uppercase px-2.5 py-1 rounded-md flex items-center gap-1.5 shadow-lg">
                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                            AO VIVO
                        </div>
                        <div id="f5tv-channel-badge" class="absolute top-4 right-4 bg-black/70 backdrop-blur-md border border-white/5 text-zinc-300 font-mono font-bold text-[10px] uppercase px-3 py-1 rounded-lg">
                            Canal: <?php echo esc_html($active_channel['logoText']); ?>
                        </div>
                    </div>

                    <!-- Program details -->
                    <div class="bg-f5-blue-950 border border-zinc-900 p-6 rounded-2xl flex flex-col gap-4">
                        <div class="flex items-center gap-3">
                            <div id="f5tv-channel-icon" class="w-12 h-12 bg-f5-red rounded-xl flex items-center justify-center font-black tracking-tighter text-sm text-white shrink-0 shadow-md">
                                <?php echo esc_html($active_channel['logoText']); ?>
                            </div>
                            <div>
                                <h2 id="f5tv-channel-title" class="text-xl font-black text-zinc-100"><?php echo esc_html($active_channel['name']); ?></h2>
                                <p id="f5tv-channel-category" class="text-xs text-zinc-500 font-mono uppercase mt-0.5 font-bold">Categoria: <?php echo esc_html($active_channel['category']); ?></p>
                            </div>
                        </div>

                        <div class="bg-f5-blue-900/60 p-4 border border-zinc-850 rounded-xl flex flex-col gap-2">
                            <span class="text-[10px] font-mono font-extrabold tracking-widest text-f5-red uppercase"><?php echo $demo_channel ? 'PRÉVIA DEMONSTRATIVA:' : 'NO AR AGORA:'; ?></span>
                            <div class="flex flex-col">
                                <h3 class="text-lg font-black text-white"><?php echo esc_html($active_channel['name']); ?> - <?php echo $demo_channel ? 'Conteúdo demonstrativo' : 'Transmissão Oficial'; ?></h3>
                                <p class="text-xs text-zinc-400 mt-1 leading-relaxed"><?php echo $demo_channel ? 'Imagem e sinal usados apenas para demonstrar a experiência do player.' : 'Acompanhe a transmissão em alta definição do sinal F5 TV.'; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar channels list & chat -->
                <div class="flex flex-col gap-5">
                    <div class="bg-f5-blue-950 border border-zinc-900 p-4 rounded-xl flex flex-col gap-3">
                        <span class="text-xs font-mono font-bold text-zinc-500 uppercase">Selecione o Canal</span>
                        <div class="flex flex-col gap-2">
                            <?php foreach ($channels as $idx => $ch): ?>
                                <button type="button" class="f5tv-channel-btn w-full flex items-center justify-between p-3 rounded-xl transition cursor-pointer text-left border <?php echo $idx === 0 ? 'bg-f5-blue-900 border-red-650' : 'bg-f5-blue-950 border-zinc-900'; ?>" data-stream="<?php echo esc_url($ch['streamUrl']); ?>" data-name="<?php echo esc_attr($ch['name']); ?>" data-logo="<?php echo esc_attr($ch['logoText']); ?>" data-category="<?php echo esc_attr($ch['category']); ?>">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-f5-blue-900 flex items-center justify-center font-black text-[10px] border border-zinc-850">
                                            <?php echo esc_html($ch['logoText']); ?>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-bold text-xs text-zinc-200"><?php echo esc_html($ch['name']); ?></span>
                                            <span class="text-[9px] font-mono text-zinc-500"><?php echo esc_html($ch['category']); ?></span>
                                        </div>
                                    </div>
                                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Chat room -->
                    <div class="bg-f5-blue-950 border border-zinc-900 rounded-2xl flex flex-col h-[380px]">
                        <div class="p-3 border-b border-zinc-900 bg-f5-blue-950/20 flex items-center justify-between text-xs">
                            <span class="font-bold flex items-center gap-1.5">&#128172; Chat do Assinante</span>
                            <span class="font-mono text-[10px] text-green-500">Online</span>
                        </div>
                        <div id="f5tv-chat-box" class="flex-1 overflow-y-auto p-4 flex flex-col gap-3 text-xs scrollbar-thin">
                            <div class="flex flex-col gap-0.5">
                                <span class="font-mono font-black text-zinc-350">@Carlos_Silva</span>
                                <p class="text-zinc-400 font-semibold">Excelente transmissão! Qualidade 100%.</p>
                            </div>
                            <div class="flex flex-col gap-0.5">
                                <span class="font-mono font-black text-zinc-350">@AnaMariano</span>
                                <p class="text-zinc-400 font-semibold">F5 TV sempre surpreendendo!</p>
                            </div>
                        </div>
                        <form id="f5tv-chat-form" class="p-2.5 border-t border-zinc-900 bg-f5-blue-950 flex gap-2">
                            <input type="text" id="f5tv-chat-input" placeholder="Envie uma mensagem..." class="flex-grow bg-f5-blue-900 border border-zinc-850 outline-none text-xs rounded-lg p-2.5 text-zinc-100 focus:border-f5-red">
                            <button type="submit" class="bg-f5-red hover:bg-f5-red-700 text-white px-3 py-2.5 rounded-lg transition cursor-pointer font-bold text-xs">Enviar</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.querySelectorAll('.f5tv-channel-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.f5tv-channel-btn').forEach(b => {
            b.classList.remove('bg-f5-blue-900', 'border-red-650');
            b.classList.add('bg-f5-blue-950', 'border-zinc-900');
        });
        btn.classList.remove('bg-f5-blue-950', 'border-zinc-900');
        btn.classList.add('bg-f5-blue-900', 'border-red-650');

        const stream = btn.getAttribute('data-stream');
        const name = btn.getAttribute('data-name');
        const logo = btn.getAttribute('data-logo');
        const cat = btn.getAttribute('data-category');

        const player = document.getElementById('f5tv-live-player');
        if (player && stream) {
            player.src = stream;
            player.play();
        }

        document.getElementById('f5tv-channel-title').textContent = name;
        document.getElementById('f5tv-channel-icon').textContent = logo;
        document.getElementById('f5tv-channel-category').textContent = 'Categoria: ' + cat;
        document.getElementById('f5tv-channel-badge').textContent = 'Canal: ' + logo;
    });
});

document.getElementById('f5tv-chat-form')?.addEventListener('submit', (e) => {
    e.preventDefault();
    const input = document.getElementById('f5tv-chat-input');
    if (!input || !input.value.trim()) return;
    const box = document.getElementById('f5tv-chat-box');
    if (box) {
        const msgDiv = document.createElement('div');
        msgDiv.className = 'flex flex-col gap-0.5';
        msgDiv.innerHTML = `<span class="font-mono font-black text-f5-red">@Você (Assinante)</span><p class="text-zinc-400 font-semibold">${input.value.replace(/</g, "&lt;").replace(/>/g, "&gt;")}</p>`;
        box.appendChild(msgDiv);
        box.scrollTop = box.scrollHeight;
    }
    input.value = '';
});
</script>

<?php get_footer(); ?>
