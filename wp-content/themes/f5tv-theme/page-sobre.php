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
            <span class="text-[10px] font-mono font-bold tracking-[0.2em] text-f5-red uppercase">F5 TV STREAMING</span>
            <h1 class="text-4xl sm:text-6xl font-black tracking-tighter leading-none mb-2">A 1ª TV Streaming de Portugal</h1>
            <p class="text-zinc-300 text-sm leading-relaxed max-w-2xl font-semibold">
                Uma nova forma de fazer televisão.
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
                <h3 class="text-lg font-black text-white">Televisão sem fronteiras</h3>
                <p class="text-zinc-400 text-xs font-semibold leading-relaxed">
                    Informação, entretenimento, cultura, entrevistas, opinião, negócios e lifestyle num só lugar.
                </p>
            </div>

            <div class="flex flex-col gap-3 p-6 bg-f5-blue-950 border border-zinc-900 rounded-2xl">
                <h3 class="text-lg font-black text-white">Uma visão contemporânea</h3>
                <p class="text-zinc-400 text-xs font-semibold leading-relaxed">
                    Conteúdos próprios e parceiros, formatos diferenciados e uma experiência adaptada aos novos ecrãs.
                </p>
            </div>

            <div class="flex flex-col gap-3 p-6 bg-f5-blue-950 border border-zinc-900 rounded-2xl">
                <h3 class="text-lg font-black text-white">Portugal e o mundo</h3>
                <p class="text-zinc-400 text-xs font-semibold leading-relaxed">
                    Histórias, ideias e protagonistas que merecem ser vistos e ouvidos.
                </p>
            </div>
        </div>

        <section class="border-t border-zinc-900 pt-12 flex flex-col gap-6 font-semibold text-zinc-300 text-sm leading-relaxed max-w-3xl">
            <h2 class="text-2xl font-black text-white tracking-tight">Uma nova forma de fazer televisão</h2>
            <p>
                A F5 TV Streaming chega para inaugurar uma nova forma de fazer televisão em Portugal. Como 1ª TV Streaming de Portugal, assumimos um lugar pioneiro num mercado que está a mudar — e que já não cabe em horários fixos, grelhas rígidas ou formatos previsíveis. Hoje, cada pessoa escolhe o que quer ver, no momento certo e no ecrã que prefere. É nessa liberdade que a F5 TV encontra o seu lugar.
            </p>
            <p>
                Reunimos informação, entretenimento, cultura, entrevistas, opinião, negócios, lifestyle e conteúdos especiais numa experiência pensada para despertar curiosidade e criar ligação. Aqui, cada programa tem uma história, cada voz tem espaço e cada espectador pode encontrar algo que lhe diz respeito.
            </p>
            <p>
                Acreditamos numa televisão que não se limita a transmitir: uma televisão que informa, inspira, surpreende e participa na conversa do seu tempo. Por isso, criamos conteúdos próprios e em parceria, exploramos novos formatos e acompanhamos o que acontece em Portugal e no mundo — sempre com uma linguagem próxima, atual e feita para os novos ecrãs.
            </p>
            <p>
                A F5 TV é televisão sem fronteiras de horário, espaço ou formato. É conteúdo que encontra o seu público, ideias que geram conversa e histórias que continuam depois do ecrã. Uma televisão para quem gosta de descobrir, participar e ver mais.
            </p>
            <p class="text-lg font-black text-white">F5 TV Streaming. O futuro da televisão começa aqui — e começa consigo.</p>
        </section>
    </main>
</div>

<?php get_footer(); ?>
