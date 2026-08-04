<?php
/**
 * Template Name: Continuar Assistindo
 * Description: Página com histórico de progresso de exibição de filmes e episódios.
 */

get_header();
?>

<div class="min-h-screen bg-f5-blue text-white font-sans selection:bg-f5-red p-6 md:p-10">
    <div class="max-w-7xl mx-auto flex flex-col gap-6">
        <div class="border-b border-zinc-900 pb-4">
            <span class="text-f5-red font-mono font-black text-xs tracking-widest uppercase">HISTÓRICO EM ANDAMENTO</span>
            <h1 class="text-3xl font-black tracking-tight mt-1">Continuar Assistindo</h1>
        </div>

        <div id="f5tv-continue-grid" class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5 lg:gap-6">
            <div class="col-span-full py-16 text-center text-zinc-500 font-mono text-xs">
                Carregando seu histórico de progresso...
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const grid = document.getElementById('f5tv-continue-grid');
    if (!grid) return;

    try {
        const activeProfileId = sessionStorage.getItem('f5tv_active_profile_id') || '';
        const res = await fetch(`/wp-json/f5tv/v1/watch-history?profile_id=${activeProfileId}`);
        const historyItems = await res.json();

        if (!Array.isArray(historyItems) || historyItems.length === 0) {
            grid.innerHTML = '<div class="col-span-full py-16 text-center text-zinc-500 font-mono text-xs">Nenhum histórico recente encontrado. Assista a um filme ou série para salvar seu progresso.</div>';
            return;
        }

        const catRes = await fetch('/wp-json/f5tv/v1/catalog');
        const catalog = await catRes.json();

        grid.innerHTML = historyItems.map(item => {
            const content = catalog.find(c => String(c.id) === String(item.contentId)) || {};
            const title = content.title || 'Conteúdo em exibição';
            const banner = content.bannerUrl || content.coverUrl || 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=1200';
            const progress = Math.min(100, Math.max(0, item.progress || 0));

            return `
                <a href="/assista?id=${item.contentId}" class="group flex flex-col bg-f5-blue-950 border border-zinc-900 rounded-2xl overflow-hidden hover:scale-102 transition duration-200 shadow-xl">
                    <div class="aspect-video w-full bg-zinc-900 relative">
                        <img src="${banner}" alt="${title}" class="w-full h-full object-contain group-hover:opacity-75 transition">
                        <div class="absolute inset-x-0 bottom-0 bg-black/60 h-1.5">
                            <div class="bg-f5-red h-full" style="width: ${progress}%"></div>
                        </div>
                    </div>
                    <div class="p-4 flex flex-col gap-1">
                        <span class="text-[10px] font-mono font-bold text-f5-red uppercase">${Math.round(progress)}% Concluído</span>
                        <strong class="text-sm text-white line-clamp-1 group-hover:text-f5-red transition">${title}</strong>
                    </div>
                </a>
            `;
        }).join('');

    } catch (e) {
        grid.innerHTML = '<div class="col-span-full py-16 text-center text-zinc-500 font-mono text-xs">Faça login para acompanhar seu histórico de vídeos.</div>';
    }
});
</script>

<?php get_footer(); ?>
