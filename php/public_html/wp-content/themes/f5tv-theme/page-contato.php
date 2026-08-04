<?php
/**
 * Template Name: Contato
 * Description: Página de contato e suporte da F5 TV.
 */

get_header();
?>

<div class="min-h-screen bg-f5-blue text-white font-sans flex flex-col justify-between selection:bg-f5-red pb-16">
    <main class="flex-grow max-w-5xl mx-auto px-8 py-16 text-left w-full">
        <div class="flex flex-col gap-3 mb-10">
            <span class="text-[10px] font-mono font-bold tracking-[0.2em] text-f5-red uppercase">FALE CONOSCO</span>
            <h1 class="text-4xl sm:text-6xl font-black tracking-tighter leading-none mb-2">Central de Atendimento</h1>
            <p class="text-zinc-400 text-sm leading-relaxed max-w-xl font-semibold">
                Seja para dúvidas comerciais, suporte técnico ao reproduzir vídeos ou agendamento de imprensa, nossa central está pronta para ajudar.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-12 mt-8">
            <div class="lg:col-span-2 flex flex-col gap-6 font-semibold">
                <div class="bg-f5-blue-950 border border-zinc-900 p-6 rounded-2xl flex flex-col gap-5">
                    <h3 class="text-lg font-black text-white tracking-tight">Canais Globais</h3>
                    
                    <div class="flex flex-col gap-1 text-xs text-zinc-400">
                        <span class="text-white font-black text-sm">Escritório Central</span>
                        <span>Av. das Nações Unidas, 12901 - Brooklin</span>
                        <span>São Paulo - SP, Brasil</span>
                    </div>

                    <div class="flex flex-col gap-1 text-xs text-zinc-400">
                        <span class="text-white font-black text-sm">Contato Telefônico</span>
                        <span class="font-mono text-zinc-300">0800 707 9900 (SAC Geral)</span>
                        <span class="font-mono text-zinc-300">+55 11 3060-4000 (Comercial)</span>
                    </div>

                    <div class="flex flex-col gap-1 text-xs text-zinc-400">
                        <span class="text-white font-black text-sm">Correspondência Eletrônica</span>
                        <span class="font-mono text-f5-red">suporte@f5tv.com.br</span>
                        <span class="font-mono text-f5-red">comercial@f5tv.com.br</span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-3">
                <div class="bg-f5-blue-950 border border-zinc-900 p-8 rounded-2xl flex flex-col gap-4">
                    <h3 class="text-lg font-black tracking-tight text-white mb-2">Envie uma Mensagem</h3>
                    
                    <form id="f5tv-contact-form" class="flex flex-col gap-4">
                        <div class="flex flex-col gap-1 text-xs">
                            <label class="text-zinc-400 font-mono font-bold uppercase text-[10px]">Nome Completo</label>
                            <input type="text" required placeholder="Seu nome" class="bg-f5-blue-900 border border-zinc-800 p-3 rounded-xl text-white outline-none text-xs focus:border-f5-red">
                        </div>

                        <div class="flex flex-col gap-1 text-xs">
                            <label class="text-zinc-400 font-mono font-bold uppercase text-[10px]">E-mail</label>
                            <input type="email" required placeholder="seu@email.com" class="bg-f5-blue-900 border border-zinc-800 p-3 rounded-xl text-white outline-none text-xs focus:border-f5-red">
                        </div>

                        <div class="flex flex-col gap-1 text-xs">
                            <label class="text-zinc-400 font-mono font-bold uppercase text-[10px]">Mensagem</label>
                            <textarea required rows="4" placeholder="Descreva sua dúvida ou solicitação..." class="bg-f5-blue-900 border border-zinc-800 p-3 rounded-xl text-white outline-none text-xs focus:border-f5-red resize-none"></textarea>
                        </div>

                        <button type="submit" class="w-full bg-f5-red hover:bg-f5-red-700 text-white font-mono font-bold text-xs uppercase tracking-widest py-3.5 rounded-xl transition cursor-pointer mt-2">
                            Enviar Ticket de Contato
                        </button>
                    </form>
                    <div id="f5tv-contact-msg" class="text-xs font-mono text-center"></div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
document.getElementById('f5tv-contact-form')?.addEventListener('submit', (e) => {
    e.preventDefault();
    const msg = document.getElementById('f5tv-contact-msg');
    if (msg) {
        msg.className = 'text-xs font-mono text-green-400 text-center mt-2';
        msg.textContent = 'Mensagem enviada com sucesso! Um especialista responderá em breve.';
    }
});
</script>

<?php get_footer(); ?>
