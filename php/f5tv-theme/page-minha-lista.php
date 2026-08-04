<?php
/**
 * Template Name: Minha Lista
 * Description: Página com o catálogo de títulos salvos nos favoritos pelo assinante.
 */

get_header();
?>

<div class="min-h-screen bg-f5-blue text-white font-sans selection:bg-f5-red p-6 md:p-10">
    <div class="max-w-7xl mx-auto flex flex-col gap-6">
        <div class="border-b border-zinc-900 pb-4">
            <span class="text-f5-red font-mono font-black text-xs tracking-widest uppercase">MEUS FAVORITOS</span>
            <h1 class="text-3xl font-black tracking-tight mt-1">Minha Lista de Conteúdos</h1>
        </div>

        <div id="f5tv-mylist-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <div class="col-span-full py-16 text-center text-zinc-500 font-mono text-xs">
                Carregando sua lista personalizada...
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const grid = document.getElementById('f5tv-mylist-grid');
    if (!grid) return;

    try {
        const activeProfileId = sessionStorage.getItem('f5tv_active_profile_id') || '';
        const res = await fetch(`/wp-json/f5tv/v1/my-list?profile_id=${activeProfileId}`);
        const contentIds = await res.json();

        if (!Array.isArray(contentIds) || contentIds.length === 0) {
            grid.innerHTML = '<div class="col-span-full py-16 text-center text-zinc-500 font-mono text-xs">Sua lista de favoritos está vazia no momento. Adicione títulos navegando pelo catálogo.</div>';
            return;
        }

        const catRes = await fetch('/wp-json/f5tv/v1/catalog');
        const catalog = await catRes.json();
        const myItems = catalog.filter(item => contentIds.includes(String(item.id)));

        if (!myItems.length) {
            grid.innerHTML = '<div class="col-span-full py-16 text-center text-zinc-500 font-mono text-xs">Nenhum título encontrado na lista.</div>';
            return;
        }

        grid.innerHTML = myItems.map(item => `
            <a href="/assista?id=${item.id}" class="group flex flex-col bg-f5-blue-950 border border-zinc-900 rounded-xl overflow-hidden hover:scale-103 transition duration-200 shadow-lg">
                <div class="aspect-[3/4] w-full bg-zinc-900 relative">
                    <img src="${item.coverUrl || 'https://images.unsplash.com/photo-1594909122845-11baa439b7bf?q=80&w=600'}" alt="${item.title}" class="w-full h-full object-cover group-hover:opacity-80 transition">
                </div>
                <div class="p-3 flex flex-col gap-1">
                    <strong class="text-xs text-white line-clamp-1 group-hover:text-f5-red transition">${item.title}</strong>
                    <span class="text-[10px] font-mono text-zinc-500 uppercase">${item.category || 'Streaming'}</span>
                </div>
            </a>
        `).join('');

    } catch (e) {
        grid.innerHTML = '<div class="col-span-full py-16 text-center text-zinc-500 font-mono text-xs">Faça login para visualizar sua lista de salvos.</div>';
    }
});
</script>

<?php get_footer(); ?>
