<?php
/**
 * Template Name: Sobre
 * Description: Página sobre a emissora e plataforma de streaming F5 TV.
 */

get_header();
?>

<div class="min-h-screen bg-f5-blue text-white font-sans flex flex-col justify-between selection:bg-f5-red pb-16">
    <main class="flex-grow max-w-4xl mx-auto px-8 py-16 text-left">
        <div class="flex flex-col gap-3 mb-10">
            <span class="text-[10px] font-mono font-bold tracking-[0.2em] text-f5-red uppercase">QUEM SOMOS NÓS</span>
            <h1 class="text-4xl sm:text-6xl font-black tracking-tighter leading-none mb-2">Sobre a F5 TV Brasil</h1>
            <p class="text-zinc-300 text-sm leading-relaxed max-w-2xl font-semibold">
                Uma emissora com DNA tático e digital. Nascemos na interseção entre o jornalismo livre, os esportes de alta velocidade e o entretenimento familiar seguro.
            </p>
        </div>

        <div class="relative aspect-video rounded-3xl overflow-hidden border border-white/5 mb-16 shadow-2xl">
            <div class="absolute inset-0 bg-gradient-to-t from-[#030315] via-transparent to-transparent z-10"></div>
            <img src="https://images.unsplash.com/photo-1461896836934-ffe607ba8211?q=80&w=2070" alt="F5 TV Studio" class="w-full h-full object-cover opacity-70">
            <div class="absolute bottom-6 left-6 z-20 flex gap-2">
                <span class="text-[10px] bg-f5-red text-white font-mono px-2.5 py-1 rounded font-bold uppercase tracking-wider flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-white rounded-full animate-ping"></span>
                    Sinal Ao Vivo 24/7
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 mb-16">
            <div class="flex flex-col gap-3 p-6 bg-f5-blue-950 border border-zinc-900 rounded-2xl">
                <h3 class="text-lg font-black text-white">Transparência Tática</h3>
                <p class="text-zinc-400 text-xs font-semibold leading-relaxed">
                    Jornalismo que reporta fatos sem censura corporativa, focando em economia, segurança e tecnologia.
                </p>
            </div>

            <div class="flex flex-col gap-3 p-6 bg-f5-blue-950 border border-zinc-900 rounded-2xl">
                <h3 class="text-lg font-black text-white">Ambiente Protegido</h3>
                <p class="text-zinc-400 text-xs font-semibold leading-relaxed">
                    Canais e conteúdos infantis com curadoria pedagógica severa para total tranquilidade da família.
                </p>
            </div>

            <div class="flex flex-col gap-3 p-6 bg-f5-blue-950 border border-zinc-900 rounded-2xl">
                <h3 class="text-lg font-black text-white">Esportes & Ação</h3>
                <p class="text-zinc-400 text-xs font-semibold leading-relaxed">
                    Coberturas completas de automobilismo de teste, lutas e campeonatos regionais em 4K.
                </p>
            </div>
        </div>

        <section class="border-t border-zinc-900 pt-12 flex flex-col gap-6 font-semibold text-zinc-300 text-sm leading-relaxed max-w-3xl">
            <h2 class="text-2xl font-black text-white tracking-tight">Nossa Jornada & Concessionária</h2>
            <p>
                Fundada pelo grupo F5 de Comunicação S.A., a F5 TV atua como emissora concessionária de radiodifusão de sons e imagens com outorga e cobertura nacional. Em 2024, expandimos nossas operações com o lançamento da plataforma de streaming F5 Premium, garantindo acesso em tempo real e sob demanda para mais de 10 milhões de lares.
            </p>
            <p>
                Acreditamos que todo cidadão merece veracidade em tempo real. Por isso, a F5 TV investe integralmente em tecnologia de tráfego ultra-low-latency e estúdios modernos baseados em inteligência computacional tática.
            </p>
        </section>
    </main>
</div>

<?php get_footer(); ?>
