<?php
/**
 * Template Name: Planos e Assinaturas
 * Description: Página com opções de planos, tabela comparativa e FAQ da F5 TV.
 */

get_header();

if (did_action('elementor/loaded') && \Elementor\Plugin::$instance->db->is_built_with_elementor(get_the_ID())) {
    echo '<main id="elementor-plans-page-content" class="min-h-screen bg-f5-blue text-white font-sans">';
    while (have_posts()): the_post();
        the_content();
    endwhile;
    echo '</main>';
    get_footer();
    return;
}
?>

<div id="plans-page-root" class="min-h-screen bg-f5-blue text-white flex flex-col justify-between font-sans relative pb-16">
    <main class="flex-1 max-w-7xl w-full mx-auto px-6 py-16 flex flex-col items-center gap-12">
        <div class="text-center max-w-2xl flex flex-col gap-3">
            <span class="text-f5-red font-mono font-black text-xs uppercase tracking-widest">ASSINATURAS EM BREVE</span>
            <h1 class="text-3xl md:text-5xl font-black tracking-tight leading-none text-zinc-100">
                Conteúdos por assinatura disponíveis em breve!
            </h1>
            <p class="text-zinc-400 text-sm md:text-base font-medium">
                Estamos a preparar novas experiências de conteúdo para a F5 TV.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 w-full max-w-5xl mt-4 justify-items-center">
            <!-- Plano Básico -->
            <div id="plan-card-basico" class="flex flex-col rounded-2xl border border-zinc-900 bg-f5-blue-950/80 p-8 shadow-2xl relative transition hover:scale-102 duration-300 w-full max-w-sm">
                <div class="flex flex-col gap-2 pb-6 border-b border-zinc-900 text-center">
                    <span class="text-xs font-mono text-zinc-400 uppercase font-black tracking-widest">Básico</span>
                    <div class="flex items-baseline gap-1 mt-1 justify-center">
                        <span class="text-sm font-semibold text-f5-red">Conteúdos por assinatura disponíveis em breve!</span>
                    </div>
                </div>

                <div class="flex-1 py-8 flex flex-col gap-4">
                    <span class="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider text-center">Recursos Inclusos:</span>
                    <ul class="flex flex-col gap-3.5 items-center">
                        <li class="flex items-start gap-2.5 text-xs text-zinc-300 text-center">
                            <span class="text-f5-red font-bold">&#10003;</span>
                            <span class="font-semibold leading-relaxed">Catálogo sob demanda em HD</span>
                        </li>
                        <li class="flex items-start gap-2.5 text-xs text-zinc-300 text-center">
                            <span class="text-f5-red font-bold">&#10003;</span>
                            <span class="font-semibold leading-relaxed">1 tela simultânea</span>
                        </li>
                        <li class="flex items-start gap-2.5 text-xs text-zinc-300 text-center">
                            <span class="text-f5-red font-bold">&#10003;</span>
                            <span class="font-semibold leading-relaxed">Acesso em Smart TV, celular e PC</span>
                        </li>
                    </ul>
                </div>

                <span class="w-full text-center py-3 px-4 rounded-xl font-bold font-mono text-xs uppercase tracking-wider bg-[#194b7a]/60 text-white/80">Disponível em breve</span>
            </div>

            <!-- Plano Família -->
            <div id="plan-card-familia" class="flex flex-col rounded-2xl border border-zinc-900 bg-f5-blue-950/80 p-8 shadow-2xl relative transition hover:scale-102 duration-300 w-full max-w-sm">
                <div class="flex flex-col gap-2 pb-6 border-b border-zinc-900 text-center">
                    <span class="text-xs font-mono text-zinc-400 uppercase font-black tracking-widest">Família</span>
                    <div class="flex items-baseline gap-1 mt-1 justify-center">
                        <span class="text-sm font-semibold text-f5-red">Conteúdos por assinatura disponíveis em breve!</span>
                    </div>
                </div>

                <div class="flex-1 py-8 flex flex-col gap-4">
                    <span class="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider text-center">Recursos Inclusos:</span>
                    <ul class="flex flex-col gap-3.5 items-center">
                        <li class="flex items-start gap-2.5 text-xs text-zinc-300 text-center">
                            <span class="text-f5-red font-bold">&#10003;</span>
                            <span class="font-semibold leading-relaxed">Catálogo completo em Full HD</span>
                        </li>
                        <li class="flex items-start gap-2.5 text-xs text-zinc-300 text-center">
                            <span class="text-f5-red font-bold">&#10003;</span>
                            <span class="font-semibold leading-relaxed">3 telas simultâneas</span>
                        </li>
                        <li class="flex items-start gap-2.5 text-xs text-zinc-300 text-center">
                            <span class="text-f5-red font-bold">&#10003;</span>
                            <span class="font-semibold leading-relaxed">Canais Ao Vivo inclusos</span>
                        </li>
                    </ul>
                </div>

                <span class="w-full text-center py-3 px-4 rounded-xl font-bold font-mono text-xs uppercase tracking-wider bg-[#194b7a]/60 text-white/80">Disponível em breve</span>
            </div>

            <!-- Plano Premium (Recomendado) -->
            <div id="plan-card-premium" class="flex flex-col rounded-2xl border border-f5-red/60 bg-gradient-to-b from-f5-red-950/20 to-f5-blue-950/80 p-8 shadow-2xl relative transition hover:scale-102 duration-300 w-full max-w-sm">
                <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-f5-red text-white text-[9px] font-mono font-black uppercase tracking-widest px-3.5 py-1 rounded-full shadow shadow-f5-red-700/45 flex items-center gap-1">
                    <span>&#9733; Mais Recomendado</span>
                </span>

                <div class="flex flex-col gap-2 pb-6 border-b border-zinc-900 text-center">
                    <span class="text-xs font-mono text-f5-red uppercase font-black tracking-widest">Premium 4K</span>
                    <div class="flex items-baseline gap-1 mt-1 justify-center">
                        <span class="text-sm font-semibold text-f5-red">Conteúdos por assinatura disponíveis em breve!</span>
                    </div>
                </div>

                <div class="flex-1 py-8 flex flex-col gap-4">
                    <span class="text-[10px] font-mono font-bold text-zinc-400 uppercase tracking-wider text-center">Recursos Inclusos:</span>
                    <ul class="flex flex-col gap-3.5 items-center">
                        <li class="flex items-start gap-2.5 text-xs text-zinc-300 text-center">
                            <span class="text-f5-red font-bold">&#10003;</span>
                            <span class="font-semibold leading-relaxed">Catálogo completo em 4K Ultra HD</span>
                        </li>
                        <li class="flex items-start gap-2.5 text-xs text-zinc-300 text-center">
                            <span class="text-f5-red font-bold">&#10003;</span>
                            <span class="font-semibold leading-relaxed">5 telas simultâneas</span>
                        </li>
                        <li class="flex items-start gap-2.5 text-xs text-zinc-300 text-center">
                            <span class="text-f5-red font-bold">&#10003;</span>
                            <span class="font-semibold leading-relaxed">Ao vivo, estreias e especiais exclusivos</span>
                        </li>
                        <li class="flex items-start gap-2.5 text-xs text-zinc-300 text-center">
                            <span class="text-f5-red font-bold">&#10003;</span>
                            <span class="font-semibold leading-relaxed">Áudio imersivo Dolby Atmos</span>
                        </li>
                    </ul>
                </div>

                <span class="w-full text-center py-3 px-4 rounded-xl font-bold font-mono text-xs uppercase tracking-wider bg-[#194b7a]/60 text-white/80">Disponível em breve</span>
            </div>
        </div>

        <!-- COMPARATIVE TABLE -->
        <div class="w-full max-w-4xl mt-12 bg-f5-blue-950 border border-zinc-900 rounded-2xl overflow-hidden shadow-2xl p-6 md:p-8">
            <h2 class="text-md font-mono font-black uppercase tracking-widest text-f5-red mb-6 flex items-center gap-2">
                <span>&#128737; Comparativo Detalhado de Benefícios</span>
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs font-semibold">
                    <thead>
                        <tr class="border-b border-zinc-900 text-zinc-500 font-mono text-[10px] uppercase">
                            <th class="py-4 pr-4">Recurso do Serviço</th>
                            <th class="py-4 px-4 text-center">Básico</th>
                            <th class="py-4 px-4 text-center">Família</th>
                            <th class="py-4 px-4 text-center">Premium 4K</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-900 text-zinc-300">
                        <tr class="hover:bg-f5-blue-900/20">
                            <td class="py-4 pr-4 text-zinc-400">Qualidade Máxima de Vídeo</td>
                            <td class="py-4 px-4 text-center">HD (720p)</td>
                            <td class="py-4 px-4 text-center">Full HD (1080p)</td>
                            <td class="py-4 px-4 text-center text-f5-red font-bold">UHD @ 4K Ultra</td>
                        </tr>
                        <tr class="hover:bg-f5-blue-900/20">
                            <td class="py-4 pr-4 text-zinc-400">Telas Simultâneas Ativas</td>
                            <td class="py-4 px-4 text-center">1 Tela</td>
                            <td class="py-4 px-4 text-center">3 Telas</td>
                            <td class="py-4 px-4 text-center text-white font-bold">5 Telas</td>
                        </tr>
                        <tr class="hover:bg-f5-blue-900/20">
                            <td class="py-4 pr-4 text-zinc-400">Conteúdo Exclusivo F5 Original</td>
                            <td class="py-4 px-4 text-center">Sim</td>
                            <td class="py-4 px-4 text-center">Sim</td>
                            <td class="py-4 px-4 text-center text-white">Sim</td>
                        </tr>
                        <tr class="hover:bg-f5-blue-900/20">
                            <td class="py-4 pr-4 text-zinc-400">Transmissões Ao Vivo</td>
                            <td class="py-4 px-4 text-center text-zinc-600 font-mono">Não</td>
                            <td class="py-4 px-4 text-center text-white">Sim</td>
                            <td class="py-4 px-4 text-center text-white">Sim</td>
                        </tr>
                        <tr class="hover:bg-f5-blue-900/20">
                            <td class="py-4 pr-4 text-zinc-400">Áudio Imersivo Dolby Atmos</td>
                            <td class="py-4 px-4 text-center text-zinc-600 font-mono">Não</td>
                            <td class="py-4 px-4 text-center text-zinc-600 font-mono">Não</td>
                            <td class="py-4 px-4 text-center text-f5-red font-bold">Sim</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php get_footer(); ?>
