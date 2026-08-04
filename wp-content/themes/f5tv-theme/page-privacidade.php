<?php
/**
 * Template Name: Política de Privacidade
 * Description: Página com política de privacidade e LGPD da F5 TV.
 */

get_header();
?>

<div class="min-h-screen bg-f5-blue text-white font-sans flex flex-col justify-between selection:bg-f5-red pb-16">
    <main class="flex-grow max-w-4xl mx-auto px-8 py-16 text-left">
        <div class="flex flex-col gap-3 mb-10">
            <span class="text-[10px] font-mono font-bold tracking-[0.2em] text-f5-red uppercase">SEGURANÇA DA INFORMAÇÃO</span>
            <h1 class="text-4xl sm:text-6xl font-black tracking-tighter leading-none mb-2">Política de Privacidade</h1>
            <p class="text-zinc-500 text-xs font-mono uppercase font-bold">Última Atualização: 2026</p>
        </div>

        <div class="bg-f5-blue-950 p-5 rounded-2xl border border-white/5 flex items-start gap-4 mb-10 text-xs">
            <div class="text-zinc-300 font-semibold leading-relaxed">
                <span class="font-bold text-white block mb-0.5">&#128274; Criptografia SSL/TLS</span>
                Seus dados cadastrais, histórico de exibição de canais e credenciais bancárias durante o checkout são encriptados digitalmente. Não compartilhamos informações privadas com terceiros sem autorização prévia por escrito.
            </div>
        </div>

        <article class="flex flex-col gap-8 text-sm text-zinc-300 leading-relaxed font-semibold max-w-3xl">
            <section class="flex flex-col gap-2">
                <h2 class="text-xl font-black text-white tracking-tight">1. Dados Coletados</h2>
                <p>
                    Ao utilizar a plataforma F5 TV, coletamos informações essenciais para a melhoria do serviço:
                </p>
                <ul class="list-disc list-inside text-zinc-400 pl-2 flex flex-col gap-1 mt-1 text-xs">
                    <li>E-mail e Nome do assinante para gerenciamento de perfis e faturamento.</li>
                    <li>Dados de reprodução e taxa de bits para otimizar os fluxos de streaming.</li>
                    <li>Modelos de dispositivos cadastrados para gerenciamento de logins simultâneos ativos.</li>
                </ul>
            </section>

            <section class="flex flex-col gap-2">
                <h2 class="text-xl font-black text-white tracking-tight">2. Uso de Informações de Pagamento</h2>
                <p>
                    Os dados fornecidos durante a contratação são processados sob diretrizes de segurança PCI-DSS. A F5 TV não mantém dados brutos de cartão em seus bancos de dados compartilhados, utilizando tokens fornecidos pelas adquirentes oficiais.
                </p>
            </section>

            <section class="flex flex-col gap-2">
                <h2 class="text-xl font-black text-white tracking-tight">3. LGPD Compliance (Lei Geral de Proteção de Dados)</h2>
                <p>
                    Você, como titular de dados sob amparo da Lei nº 13.709/2018 (LGPD), tem o direito de solicitar a qualquer momento o acesso, a correção de inconformidades, a exclusão ou a portabilidade dos seus dados cadastrados.
                </p>
            </section>

            <section class="flex flex-col gap-2">
                <h2 class="text-xl font-black text-white tracking-tight">4. Cookies de Sessão e Preferências</h2>
                <p>
                    Utilizamos cookies estritamente necessários para manter a integridade da sessão do assinante e preferência de áudio do player. Cookies de analytics são anônimos e focados em fornecer dados agregados de audiência.
                </p>
            </section>
        </article>
    </main>
</div>

<?php get_footer(); ?>
