<?php
/**
 * Front Page Template - F5TV Theme
 * Página Inicial idêntica ao projeto React (LandingPage.tsx) com suporte total ao Elementor.
 */

get_header();

// Se a página estiver sendo editada ou tiver sido construída via Elementor, renderizar the_content()
if (did_action('elementor/loaded') && \Elementor\Plugin::$instance->db->is_built_with_elementor(get_the_ID())) {
    echo '<main id="elementor-front-page-content" class="min-h-screen bg-f5-blue text-white font-sans">';
    while (have_posts()): the_post();
        the_content();
    endwhile;
    echo '</main>';
    get_footer();
    return;
}

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
                <span>&#10024; SÉRIE ORIGINAL F5</span>
            </div>

            <h1 class="text-5xl sm:text-7xl font-black tracking-tighter leading-none text-white max-w-3xl">
                CONEXÃO <span class="text-f5-red italic">F5</span>
            </h1>

            <p class="text-white/70 text-base md:text-lg max-w-2xl leading-relaxed font-normal">
                Mergulhe nos bastidores do poder, do jornalismo investigativo tático, de documentários de alta voltagem e de esportes ao vivo. Sem censura, com qualidade calibrada de cinema.
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
                    <h2 class="text-3xl md:text-5xl font-black tracking-tight text-white mt-2">Nossos Maiores Sucessos Disponíveis</h2>
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
                <h2 class="text-4xl sm:text-6xl font-black tracking-tighter text-white leading-none">Por que assinar a F5 TV?</h2>
                <p class="text-white/60 text-base font-normal mt-2 leading-relaxed">Elevamos o padrão de streaming nacional com jornalismo tático, documentários premium e tecnologia de ponta.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="p-8 bg-f5-blue-950 border border-white/5 rounded-xl flex flex-col gap-4 group">
                    <h3 class="text-xl font-bold text-white group-hover:text-f5-red transition">Assista onde quiser</h3>
                    <p class="text-sm text-white/60 leading-relaxed font-normal">Disponível em Celulares, Tablets, Smart TVs, Computadores e consoles sem custo extra.</p>
                </div>
                <div class="p-8 bg-f5-blue-950 border border-white/5 rounded-xl flex flex-col gap-4 group">
                    <h3 class="text-xl font-bold text-white group-hover:text-f5-red transition">Conteúdos Exclusivos F5</h3>
                    <p class="text-sm text-white/60 leading-relaxed font-normal">Séries investigativas brutas, bastidores das grandes cidades do Brasil e jornalismo 24h.</p>
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
