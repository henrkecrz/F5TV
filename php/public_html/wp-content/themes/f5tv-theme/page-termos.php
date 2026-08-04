<?php
/**
 * Template Name: Termos de Serviço
 * Description: Página com termos e condições de uso da F5 TV.
 */

get_header();
?>

<div class="min-h-screen bg-f5-blue text-white font-sans flex flex-col justify-between selection:bg-f5-red pb-16">
    <main class="flex-grow max-w-4xl mx-auto px-8 py-16 text-left">
        <div class="flex flex-col gap-3 mb-10">
            <span class="text-[10px] font-mono font-bold tracking-[0.2em] text-f5-red uppercase">DIRETRIZES LEGAIS</span>
            <h1 class="text-4xl sm:text-6xl font-black tracking-tighter leading-none mb-2">Termos de Serviço</h1>
            <p class="text-zinc-500 text-xs font-mono uppercase font-bold">Última Atualização: 2026</p>
        </div>

        <div class="bg-f5-blue-950 p-5 rounded-2xl border border-white/5 flex items-start gap-4 mb-10 text-xs">
            <div class="text-zinc-300 font-semibold leading-relaxed">
                <span class="font-bold text-white block mb-0.5">&#9878; Visão Geral dos Termos</span>
                Ao acessar e se cadastrar na plataforma F5 TV Premium, você concorda em cumprir estes termos regulamentares de licenciamento, direitos autorais e políticas de faturamento recorrentes. Leia com atenção antes de realizar qualquer checkout de planos.
            </div>
        </div>

        <article class="flex flex-col gap-8 text-sm text-zinc-300 leading-relaxed font-semibold max-w-3xl">
            <section class="flex flex-col gap-2">
                <h2 class="text-xl font-black text-white tracking-tight">1. Objeto do Serviço</h2>
                <p>
                    A plataforma F5 TV Premium consiste em um canal unificado de transmissão de televisão simultânea (simulcasting) e biblioteca de vídeos sob demanda (VOD) gerenciado e de propriedade da emissora F5 TV Brasil. O licenciamento de conta é pessoal, intransferível e estritamente para uso não comercial.
                </p>
            </section>

            <section class="flex flex-col gap-2">
                <h2 class="text-xl font-black text-white tracking-tight">2. Política de Reembolso e Fidelidade</h2>
                <p>
                    Em total conformidade com o Artigo 49 do Código de Defesa do Consumidor (CDC) brasileiro, garantimos o direito de arrependimento incondicional com devolução integral de valores em até 7 (sete) dias corridos após a contratação de qualquer plano de assinatura anual ou mensal. Passado o prazo legal de 7 dias ou em caso de uso ostensivo de stream de vídeo o cancelamento restringe-se a não renovação do ciclo atual.
                </p>
            </section>

            <section class="flex flex-col gap-2">
                <h2 class="text-xl font-black text-white tracking-tight">3. Conexões & Limite de Dispositivos</h2>
                <p>
                    Cada plano de assinatura estipula um limite tático de telas operacionais simultâneas para reprodução de vídeo. A F5 TV reserva-se o direito de suspender temporariamente ou emitir alertas de enlace de logins excessivos para contas com logins simultâneos abusivos que excedam o contratual do perfil cadastrado.
                </p>
            </section>

            <section class="flex flex-col gap-2">
                <h2 class="text-xl font-black text-white tracking-tight">4. Propriedade Intelectual & Sinais Protegidos</h2>
                <p>
                    Todas as transmissões, marcas exibidas nos canais, gráficos dos streamings, conteúdos infantis, novelas ou coberturas de jornalismo são de titularidade ou licenciamento oficial da F5 TV Brasil. É terminantemente proibido retransmitir, gravar, fazer captura de imagens para redistribuição comercial ou comercializar pacotes alternativos sob pena de responsabilização civil e penal.
                </p>
            </section>

            <section class="flex flex-col gap-2">
                <h2 class="text-xl font-black text-white tracking-tight">5. Legislação Aplicável e Foro</h2>
                <p>
                    Estes Termos de Serviço são regidos e interpretados segundo as Leis da República Federativa do Brasil. Fica eleito o foro da Comarca de São Paulo/SP para dirimir quaisquer controvérsias judiciais decorrentes deste pacto legal.
                </p>
            </section>
        </article>
    </main>
</div>

<?php get_footer(); ?>
