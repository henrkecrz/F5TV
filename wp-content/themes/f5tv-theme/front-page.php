<?php
/**
 * Front Page Template - F5TV Theme
 * Página Inicial idêntica ao projeto React (LandingPage.tsx) com suporte total ao Elementor.
 */

get_header();

// Renderizar sempre o design premium da Landing Page (ignora Elementor intencionalmente)

// Carregar a Landing Page oficial de alta fidelidade
$contents = get_posts([
    'post_type'      => ['f5tv_conteudo', 'f5tv_serie'],
    'posts_per_page' => 6,
    'post_status'    => 'publish',
]);

if (empty($contents)) {
    $contents = [
        (object)[
            'ID'           => 101,
            'post_title'   => 'Conexão F5: Golpes Cibernéticos',
            'cover'        => 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=600',
            'genre'        => 'Investigativo',
            'age_rating'   => '16',
            'is_exclusive' => true,
        ],
        (object)[
            'ID'           => 102,
            'post_title'   => 'Jornal F5 Ao Vivo',
            'cover'        => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=600',
            'genre'        => 'Jornalismo',
            'age_rating'   => 'Livre',
            'is_exclusive' => false,
        ],
        (object)[
            'ID'           => 103,
            'post_title'   => 'Rondas Noturnas SP',
            'cover'        => 'https://images.unsplash.com/photo-1594909122845-11baa439b7bf?q=80&w=600',
            'genre'        => 'Documentário',
            'age_rating'   => '14',
            'is_exclusive' => true,
        ],
        (object)[
            'ID'           => 104,
            'post_title'   => 'Bastidores do Poder',
            'cover'        => 'https://images.unsplash.com/photo-1578328819058-b69f3a3b0f6b?q=80&w=600',
            'genre'        => 'Política',
            'age_rating'   => '12',
            'is_exclusive' => false,
        ],
        (object)[
            'ID'           => 105,
            'post_title'   => 'Operação Fronteira 24h',
            'cover'        => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?q=80&w=600',
            'genre'        => 'Ação / Realidade',
            'age_rating'   => '16',
            'is_exclusive' => true,
        ],
        (object)[
            'ID'           => 106,
            'post_title'   => 'Linha Direta de Emergência',
            'cover'        => 'https://images.unsplash.com/photo-1517649763962-0c623266010b?q=80&w=600',
            'genre'        => 'Tático',
            'age_rating'   => '18',
            'is_exclusive' => false,
        ],
    ];
}
?>

<div id="f5-landing-root" class="min-h-screen bg-f5-blue text-white selection:bg-f5-red selection:text-white font-sans">

    <!-- Hero Banner Section -->
    <section id="hero" class="relative h-[560px] px-8 pt-12 flex flex-col justify-end pb-16 overflow-hidden border-b border-white/5 bg-black">
        <div class="absolute inset-0 bg-gradient-to-r from-[#050505] via-[#050505]/75 to-transparent z-10"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-[#050505] to-transparent z-10"></div>
        <div class="absolute inset-0 z-0 bg-[url('https://images.unsplash.com/photo-1536440136628-849c177e76a1?q=80&w=2025')] bg-cover bg-center opacity-65 grayscale-[0.25]"></div>
        
        <div class="max-w-4xl mx-auto w-full relative z-20 flex flex-col items-start gap-4 text-left">
            <div class="inline-flex items-center gap-2 bg-f5-red px-2.5 py-0.5 text-[10px] font-mono font-bold rounded uppercase tracking-widest text-white shadow">
                <span>&#10024; A 1ª TV STREAMING DE PORTUGAL</span>
            </div>

            <h1 class="text-5xl sm:text-7xl font-black tracking-tighter leading-none text-white max-w-3xl">
                Uma nova forma de <span class="text-f5-red italic">fazer televisão.</span>
            </h1>

            <p class="text-white/70 text-base md:text-lg max-w-2xl leading-relaxed font-normal">
                A F5 TV Streaming nasce para marcar uma nova etapa na televisão em Portugal. Escolha o que quer ver, quando quer ver e em qualquer ecrã.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 w-full h-fit max-w-md mt-4">
                <a href="#planos" class="px-8 py-3.5 rounded bg-white text-black font-bold flex items-center justify-center gap-2 hover:bg-f5-red hover:text-white transition-colors cursor-pointer text-sm uppercase tracking-wider">
                    <span>&#128100; Assinar Agora</span>
                </a>
                <a href="<?php echo esc_url(home_url('/area-do-assinante/')); ?>" class="bg-white/10 backdrop-blur-md text-white border border-white/20 px-8 py-3.5 rounded font-bold flex items-center justify-center gap-2 hover:bg-white/20 transition cursor-pointer text-sm uppercase tracking-wider">
                    <span>&#9654; Explorar Prévia</span>
                </a>
            </div>
            
            <div class="flex items-center gap-6 text-[10px] text-white/40 uppercase tracking-[0.2em] font-medium font-mono mt-8">
                <span>&#10003; CANCELAMENTO 100% ONLINE</span>
                <span>&bull;</span>
                <span>&#10003; FILMES FILTRADOS EM 4K</span>
            </div>
        </div>
    </section>

    <!-- Catálogo de Conteúdo Section -->
    <section id="conteudo-previa" class="py-20 px-8 bg-f5-blue border-b border-white/5">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-12">
                <div>
                    <span class="text-[10px] font-mono font-bold tracking-[0.2em] text-f5-red uppercase">NO CATÁLOGO</span>
                    <h2 class="text-3xl md:text-5xl font-black tracking-tight text-white mt-2">Conteúdo que encontra o seu público</h2>
                </div>
                <a href="<?php echo esc_url(home_url('/series/')); ?>" class="text-sm font-semibold text-f5-red hover:text-white flex items-center gap-2 transition-colors uppercase tracking-wider font-mono text-xs">
                    <span>Ver catálogo completo &rarr;</span>
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <?php if (!empty($contents)): foreach ($contents as $item):
                    $cover = $item->cover ?? f5tv_get_field('cover_url', $item->ID) ?: get_the_post_thumbnail_url($item->ID, 'medium') ?: 'https://images.unsplash.com/photo-1594909122845-11baa439b7bf?q=80&w=600';
                    $genre = $item->genre ?? f5tv_get_field('genre', $item->ID) ?: 'Streaming';
                    $age_rating = $item->age_rating ?? f5tv_get_field('age_rating', $item->ID) ?: 'Livre';
                    $is_exclusive = $item->is_exclusive ?? f5tv_get_field('is_exclusive', $item->ID);
                ?>
                    <a href="<?php echo esc_url(home_url('/assista?id=' . $item->ID)); ?>" class="group relative bg-f5-blue-950 border border-white/5 rounded-lg overflow-hidden cursor-pointer hover:border-f5-red/50 transform transition-all duration-300 hover:scale-[1.03] shadow-2xl block">
                        <div class="aspect-[3/4] relative w-full bg-f5-blue-900">
                            <img src="<?php echo esc_url($cover); ?>" alt="<?php echo esc_attr($item->post_title); ?>" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="absolute top-2.5 right-2.5 bg-f5-blue/90 text-[10px] font-mono font-bold text-f5-red px-2 py-0.5 rounded border border-white/5">
                                <?php echo esc_html($age_rating); ?>
                            </div>
                            <?php if ($is_exclusive): ?>
                                <div class="absolute bottom-2.5 left-2.5 bg-f5-red text-[10px] font-bold text-white px-2 py-0.5 rounded uppercase font-sans tracking-tight">
                                    Exclusivo
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="p-4 bg-f5-blue-950 flex flex-col gap-1.5">
                            <span class="text-[9px] font-mono tracking-wider font-semibold uppercase text-white/40">
                                <?php echo esc_html($genre); ?>
                            </span>
                            <h3 class="font-bold text-sm text-white/90 line-clamp-1 group-hover:text-white transition-colors duration-250">
                                <?php echo esc_html($item->post_title); ?>
                            </h3>
                        </div>
                    </a>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </section>

    <!-- Benefícios Exclusivos Section -->
    <section id="beneficios" class="py-24 px-8 bg-f5-blue border-b border-white/5">
        <div class="max-w-7xl mx-auto">
            <div class="text-left max-w-2xl mb-20 flex flex-col gap-3">
                <span class="text-[10px] font-mono font-bold tracking-[0.2em] text-f5-red uppercase">BENEFÍCIOS EXCLUSIVOS</span>
                <h2 class="text-4xl sm:text-6xl font-black tracking-tighter text-white leading-none">Televisão para um novo tempo</h2>
                <p class="text-white/60 text-base font-normal mt-2 leading-relaxed">Uma experiência mais flexível, contemporânea e conectada com o seu tempo, com conteúdos próprios e parceiros.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="p-8 bg-f5-blue-950 border border-white/5 rounded-xl flex flex-col gap-4 group">
                    <h3 class="text-xl font-bold text-white group-hover:text-f5-red transition">Assista onde quiser</h3>
                    <p class="text-sm text-white/60 leading-relaxed font-normal">Disponível em Celulares, Tablets, Smart TVs, Computadores e consoles sem custo extra.</p>
                </div>
                <div class="p-8 bg-f5-blue-950 border border-white/5 rounded-xl flex flex-col gap-4 group">
                    <h3 class="text-xl font-bold text-white group-hover:text-f5-red transition">Conteúdos próprios e parceiros</h3>
                    <p class="text-sm text-white/60 leading-relaxed font-normal">Formatos diferenciados, histórias, ideias e protagonistas que merecem ser vistos e ouvidos.</p>
                </div>
                <div class="p-8 bg-f5-blue-950 border border-white/5 rounded-xl flex flex-col gap-4 group">
                    <h3 class="text-xl font-bold text-white group-hover:text-f5-red transition">Downloads sob demanda</h3>
                    <p class="text-sm text-white/60 leading-relaxed font-normal">Baixe episódios inteiros em segundos e assista offline no avião, metrô ou estrada.</p>
                </div>
                <div class="p-8 bg-f5-blue-950 border border-white/5 rounded-xl flex flex-col gap-4 group">
                    <h3 class="text-xl font-bold text-white group-hover:text-f5-red transition">Alta Fidelidade 4K / HDR</h3>
                    <p class="text-sm text-white/60 leading-relaxed font-normal">Assista com cores calibradas de cinema e som Dolby Atmos imersivo.</p>
                </div>
                <div class="p-8 bg-f5-blue-950 border border-white/5 rounded-xl flex flex-col gap-4 group">
                    <h3 class="text-xl font-bold text-white group-hover:text-f5-red transition">Multi-perfil Familiar</h3>
                    <p class="text-sm text-white/60 leading-relaxed font-normal">Crie perfis independentes para cada membro da família, incluindo perfil Kids vigiado.</p>
                </div>
                <div class="p-8 bg-f5-blue-950 border border-white/5 rounded-xl flex flex-col gap-4 group">
                    <h3 class="text-xl font-bold text-white group-hover:text-f5-red transition">Sem taxas de cancelamento</h3>
                    <p class="text-sm text-white/60 leading-relaxed font-normal">Assine mensalmente, sem carência. Cancele online em dois cliques a qualquer segundo.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ══ PWA — Instalar App ══ -->
    <section id="instalar-app" class="py-10 px-8 border-b border-white/5" style="background:#060913;">
        <div class="max-w-5xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-6">

            <!-- Left: logo + text -->
            <div class="flex items-center gap-4">
                <!-- Logo textual igual ao header -->
                <span class="text-2xl font-black tracking-tighter text-white uppercase leading-none select-none">
                    F5 <span class="text-f5-red">TV</span>
                </span>
                <div class="w-px h-8 bg-zinc-800"></div>
                <div>
                    <p class="text-white text-sm font-semibold leading-tight">F5TV ao seu alcance</p>
                    <p class="text-zinc-500 text-xs mt-0.5">Adicione à tela inicial · Sem loja · Grátis</p>
                </div>
            </div>

            <!-- Right: store badges -->
            <div class="flex flex-wrap items-center justify-center sm:justify-end gap-3">

                <!-- Google Play badge -->
                <button id="f5-btn-android" title="Instalar no Android"
                        class="group flex items-center gap-3 bg-black border border-zinc-800 hover:border-zinc-600 rounded-xl px-5 py-2.5 transition-all duration-200 hover:scale-105">
                    <!-- Google Play SVG logo -->
                    <svg class="w-6 h-6 flex-shrink-0" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                        <path fill="#EA4335" d="M27.3 9.6L267.1 249 27 488.4C13.8 481.3 5 467.5 5 451.8V60.2C5 44.5 13.8 30.7 27.3 9.6z"/>
                        <path fill="#FBBC05" d="M369.7 196.7L293.8 272 27.3 9.6C36.8 1.1 49.8-2.2 62.4 3.2l307.3 193.5z"/>
                        <path fill="#4285F4" d="M369.7 315.3L62.4 508.8c-12.5 5.4-25.6 2.1-35-6.4L293.8 272l75.9 43.3z"/>
                        <path fill="#34A853" d="M499.4 256c0 13.7-7.6 27-22.4 35l-107.3 60.3L293.8 272l75.9-75.3L477 196.7c14.7 8 22.4 21.3 22.4 35z"/>
                    </svg>
                    <div class="text-left">
                        <div class="text-zinc-400 text-[10px] leading-none uppercase tracking-wide">Get it on</div>
                        <div class="text-white text-sm font-semibold leading-tight">Google Play</div>
                    </div>
                </button>

                <!-- Apple App Store badge -->
                <button id="f5-btn-ios" title="Instalar no iPhone / iPad"
                        class="group flex items-center gap-3 bg-black border border-zinc-800 hover:border-zinc-600 rounded-xl px-5 py-2.5 transition-all duration-200 hover:scale-105">
                    <!-- Apple logo SVG -->
                    <svg class="w-6 h-6 flex-shrink-0 fill-white" viewBox="0 0 814 1000" xmlns="http://www.w3.org/2000/svg">
                        <path d="M788.1 340.9c-5.8 4.5-108.2 62.2-108.2 190.5 0 148.4 130.3 200.9 134.2 202.2-.6 3.2-20.7 71.9-68.7 141.9-42.8 61.6-87.5 123.1-155.5 123.1s-85.5-39.5-164-39.5c-76 0-103.7 40.8-165.9 40.8s-105-57.8-155.5-127.4C46 790.7 0 663.9 0 541.8c0-195.4 127.4-298.5 252.8-298.5 66.1 0 121.2 43.4 162.7 43.4 39.5 0 101.1-46 176.3-46 28.5 0 130.9 2.6 198.3 99.2zm-234-181.5c31.1-36.9 53.1-88.1 53.1-139.3 0-7.1-.6-14.3-1.9-20.1-50.6 1.9-110.8 33.7-147.1 75.8-28.5 32.4-55.1 83.6-55.1 135.5 0 7.8 1.3 15.6 1.9 18.1 3.2.6 8.4 1.3 13.6 1.3 45.4 0 102.5-30.4 135.5-71.3z"/>
                    </svg>
                    <div class="text-left">
                        <div class="text-zinc-400 text-[10px] leading-none uppercase tracking-wide">Download on the</div>
                        <div class="text-white text-sm font-semibold leading-tight">App Store</div>
                    </div>
                </button>

                <!-- Microsoft / Windows badge -->
                <button id="f5-btn-windows" title="Instalar no Windows"
                        class="group flex items-center gap-3 bg-black border border-zinc-800 hover:border-zinc-600 rounded-xl px-5 py-2.5 transition-all duration-200 hover:scale-105">
                    <!-- Windows logo SVG -->
                    <svg class="w-6 h-6 flex-shrink-0" viewBox="0 0 88 88" xmlns="http://www.w3.org/2000/svg">
                        <path fill="#F25022" d="M0 0h42v42H0z"/>
                        <path fill="#7FBA00" d="M46 0h42v42H46z"/>
                        <path fill="#00A4EF" d="M0 46h42v42H0z"/>
                        <path fill="#FFB900" d="M46 46h42v42H46z"/>
                    </svg>
                    <div class="text-left">
                        <div class="text-zinc-400 text-[10px] leading-none uppercase tracking-wide">Get it from</div>
                        <div class="text-white text-sm font-semibold leading-tight">Microsoft</div>
                    </div>
                </button>

            </div>
        </div>

        <!-- Modal iOS instructions (hidden by default) -->
        <div id="f5-ios-modal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.7);backdrop-filter:blur(8px);align-items:flex-end;justify-content:center;">
            <div style="background:#111;border:1px solid #333;border-radius:20px 20px 0 0;padding:28px 24px 36px;max-width:480px;width:100%;text-align:center;">
                <div style="width:40px;height:4px;background:#444;border-radius:2px;margin:0 auto 20px;"></div>
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/icons/apple-touch-icon.png" style="width:64px;border-radius:14px;margin:0 auto 12px;">
                <h3 style="color:#fff;font-size:17px;font-weight:800;margin:0 0 6px;">Adicionar à Tela de Início</h3>
                <p style="color:#888;font-size:13px;line-height:1.6;margin:0 0 20px;">
                    No <b style="color:#fff;">Safari</b>, toque em
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="#007AFF" style="vertical-align:middle;margin:0 2px"><path d="M16 5l-1.42 1.42-1.59-1.59V16h-1.98V4.83L9.42 6.42 8 5l4-4 4 4zm4 5v11c0 1.1-.9 2-2 2H6c-1.11 0-2-.9-2-2V10c0-1.11.89-2 2-2h3v2H6v11h12V10h-3V8h3c1.1 0 2 .89 2 2z"/></svg>
                    e selecione <b style="color:#fff;">"Adicionar ao Ecrã de Início"</b>
                </p>
                <button onclick="document.getElementById('f5-ios-modal').style.display='none'"
                        style="width:100%;padding:12px;background:#dc2626;color:#fff;border:none;border-radius:10px;font-weight:700;font-size:14px;cursor:pointer;">
                    Entendido
                </button>
            </div>
        </div>
    </section>

    <script>
    (function () {
        var prompt = null;

        window.addEventListener('beforeinstallprompt', function(e) {
            e.preventDefault();
            prompt = e;
        });

        function wireInstall(id, fallback) {
            var btn = document.getElementById(id);
            if (!btn) return;
            btn.addEventListener('click', function() {
                if (prompt) {
                    prompt.prompt();
                    prompt.userChoice.then(function() { prompt = null; });
                } else {
                    alert(fallback);
                }
            });
        }

        wireInstall('f5-btn-android',
            'Android (Chrome): Menu ⋮ → "Adicionar à tela inicial"');
        wireInstall('f5-btn-windows',
            'Windows (Chrome): ícone ⊕ na barra → Instalar\nWindows (Edge): Menu … → Aplicativos → Instalar este site');

        var btnIOS = document.getElementById('f5-btn-ios');
        var modal  = document.getElementById('f5-ios-modal');
        if (btnIOS && modal) {
            btnIOS.addEventListener('click', function() { modal.style.display = 'flex'; });
            modal.addEventListener('click', function(e) { if (e.target === modal) modal.style.display = 'none'; });
        }
    })();
    </script>
    <!-- Planos de Assinatura Section -->
    <section id="planos" class="py-24 px-8 bg-f5-blue border-b border-white/5">

        <div class="max-w-7xl mx-auto">
            <div class="text-left max-w-2xl mb-18 flex flex-col gap-3">
                <span class="text-[10px] font-mono font-bold tracking-[0.2em] text-f5-red uppercase">PREÇOS TRANSPARENTES</span>
                <h2 class="text-4xl sm:text-6xl font-black tracking-tighter text-white leading-none">Escolha o seu plano ideal</h2>
                <p class="text-white/60 text-base font-normal mt-2">Valores simples, táticos e sem fidelidade. Cancele digitalmente em 2 cliques.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl">
                <div class="bg-f5-blue-950 border border-white/5 p-8 rounded-2xl flex flex-col justify-between">
                    <div>
                        <h3 class="text-2xl font-black text-white">Plano Básico</h3>
                        <div class="text-3xl font-black text-f5-red mt-2">R$ 19,90<span class="text-xs text-zinc-500 font-normal">/mês</span></div>
                        <ul class="mt-6 flex flex-col gap-3 text-xs text-zinc-300 font-medium">
                            <li>&bull; Acesso ao Sinal Ao Vivo 24/7</li>
                            <li>&bull; Resolução HD 720p em 1 Tela</li>
                            <li>&bull; Suporte Técnico Via Ticket</li>
                        </ul>
                    </div>
                    <a href="<?php echo esc_url(home_url('/checkout?plan=plano-basico')); ?>" class="mt-8 bg-zinc-800 hover:bg-f5-red text-white text-center font-mono font-bold text-xs uppercase py-3 rounded-xl transition">Assinar Básico</a>
                </div>

                <div class="bg-f5-blue-950 border-2 border-f5-red p-8 rounded-2xl flex flex-col justify-between relative shadow-2xl">
                    <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-f5-red text-white text-[9px] font-mono font-black uppercase px-3 py-1 rounded-full shadow">MAIS POPULAR</span>
                    <div>
                        <h3 class="text-2xl font-black text-white">Plano Família</h3>
                        <div class="text-3xl font-black text-f5-red mt-2">R$ 34,90<span class="text-xs text-zinc-500 font-normal">/mês</span></div>
                        <ul class="mt-6 flex flex-col gap-3 text-xs text-zinc-300 font-medium">
                            <li>&bull; 3 Telas Simultâneas em Full HD</li>
                            <li>&bull; Perfis Individuais com Perfil Kids</li>
                            <li>&bull; Downloads Offline Ilimitados</li>
                        </ul>
                    </div>
                    <a href="<?php echo esc_url(home_url('/checkout?plan=plano-familia')); ?>" class="mt-8 bg-f5-red hover:bg-f5-red-700 text-white text-center font-mono font-bold text-xs uppercase py-3 rounded-xl transition shadow-lg shadow-f5-red-700/30">Assinar Família</a>
                </div>

                <div class="bg-f5-blue-950 border border-white/5 p-8 rounded-2xl flex flex-col justify-between">
                    <div>
                        <h3 class="text-2xl font-black text-white">Plano Premium</h3>
                        <div class="text-3xl font-black text-f5-red mt-2">R$ 49,90<span class="text-xs text-zinc-500 font-normal">/mês</span></div>
                        <ul class="mt-6 flex flex-col gap-3 text-xs text-zinc-300 font-medium">
                            <li>&bull; 4 Telas Simultâneas em 4K HDR</li>
                            <li>&bull; Áudio Dolby Atmos 5.1</li>
                            <li>&bull; Acesso Antecipado às Séries Exclusivas</li>
                        </ul>
                    </div>
                    <a href="<?php echo esc_url(home_url('/checkout?plan=plano-premium')); ?>" class="mt-8 bg-zinc-800 hover:bg-f5-red text-white text-center font-mono font-bold text-xs uppercase py-3 rounded-xl transition">Assinar Premium</a>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Accordion Section -->
    <section id="faq" class="py-24 px-8 bg-f5-blue border-b border-white/5">
        <div class="max-w-3xl mx-auto">
            <span class="text-[10px] font-mono font-bold tracking-[0.2em] text-f5-red uppercase">PERGUNTAS FREQUENTES</span>
            <h2 class="text-3xl md:text-5xl font-black tracking-tighter text-white mt-2 mb-12">Dúvidas Frequentes</h2>

            <div class="flex flex-col gap-4">
                <details class="bg-f5-blue-950 border border-white/5 p-6 rounded-2xl group">
                    <summary class="font-bold text-white cursor-pointer list-none flex justify-between items-center text-sm md:text-base">
                        <span>O que é a F5 TV?</span>
                        <span class="text-f5-red group-open:rotate-180 transition">&darr;</span>
                    </summary>
                    <p class="text-xs md:text-sm text-zinc-400 mt-4 leading-relaxed font-semibold">
                        A F5 TV é uma plataforma de streaming premium nacional controlada pela rede F5 de emissoras. Ela combina transmissões de jornalismo investigativo ao vivo com um catálogo rico sob demanda contendo séries exclusivas e programas táticos.
                    </p>
                </details>

                <details class="bg-f5-blue-950 border border-white/5 p-6 rounded-2xl group">
                    <summary class="font-bold text-white cursor-pointer list-none flex justify-between items-center text-sm md:text-base">
                        <span>Como funciona o período de cancelamento?</span>
                        <span class="text-f5-red group-open:rotate-180 transition">&darr;</span>
                    </summary>
                    <p class="text-xs md:text-sm text-zinc-400 mt-4 leading-relaxed font-semibold">
                        Nossa política de assinatura é 100% transparente. Não há contratos de fidelidade. Você pode solicitar o cancelamento da sua assinatura de forma instantânea diretamente na página "Minha Conta".
                    </p>
                </details>

                <details class="bg-f5-blue-950 border border-white/5 p-6 rounded-2xl group">
                    <summary class="font-bold text-white cursor-pointer list-none flex justify-between items-center text-sm md:text-base">
                        <span>Quais aparelhos são compatíveis?</span>
                        <span class="text-f5-red group-open:rotate-180 transition">&darr;</span>
                    </summary>
                    <p class="text-xs md:text-sm text-zinc-400 mt-4 leading-relaxed font-semibold">
                        Você pode sintonizar a F5 TV em computadores via navegador web, smart TVs modernas (Samsung, LG, Android TV), dispositivos de streaming como Chromecast e Apple TV, além de smartphones e tablets.
                    </p>
                </details>
            </div>
        </div>
    </section>

</div>

<?php get_footer(); ?>
