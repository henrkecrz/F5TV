<div class="f5tv-client-dashboard flex flex-col gap-6 text-white font-sans w-full">
    <nav class="flex flex-wrap items-center gap-3 border-b border-zinc-900 pb-4 text-xs font-mono font-bold uppercase">
        <a href="<?php echo esc_url(home_url('/area-do-assinante/')); ?>" class="bg-f5-red text-white px-4 py-2 rounded-xl">Início</a>
        <a href="<?php echo esc_url(home_url('/series/')); ?>" class="bg-f5-blue-950 border border-zinc-850 hover:bg-zinc-800 text-zinc-300 px-4 py-2 rounded-xl">Séries</a>
        <a href="<?php echo esc_url(home_url('/ao-vivo/')); ?>" class="bg-f5-blue-950 border border-zinc-850 hover:bg-zinc-800 text-zinc-300 px-4 py-2 rounded-xl">Ao Vivo</a>
        <a href="<?php echo esc_url(home_url('/minha-lista/')); ?>" class="bg-f5-blue-950 border border-zinc-850 hover:bg-zinc-800 text-zinc-300 px-4 py-2 rounded-xl">Minha Lista</a>
        <a href="<?php echo esc_url(home_url('/continuar-assistindo/')); ?>" class="bg-f5-blue-950 border border-zinc-850 hover:bg-zinc-800 text-zinc-300 px-4 py-2 rounded-xl">Continuar</a>
        <a href="<?php echo esc_url(home_url('/busca/')); ?>" class="bg-f5-blue-950 border border-zinc-850 hover:bg-zinc-800 text-zinc-300 px-4 py-2 rounded-xl">Busca</a>
        <a href="<?php echo esc_url(home_url('/dispositivos/')); ?>" class="bg-f5-blue-950 border border-zinc-850 hover:bg-zinc-800 text-zinc-300 px-4 py-2 rounded-xl">Dispositivos</a>
        <a href="<?php echo esc_url(home_url('/minha-conta/')); ?>" class="bg-f5-blue-950 border border-zinc-850 hover:bg-zinc-800 text-zinc-300 px-4 py-2 rounded-xl">Minha Conta</a>
    </nav>

    <main class="f5tv-client-main w-full">
        <section id="f5tv-dashboard-content" class="f5tv-dashboard-content w-full">
            <div class="py-16 text-center text-zinc-500 font-mono text-xs">Carregando catálogo do assinante...</div>
        </section>
    </main>
</div>
