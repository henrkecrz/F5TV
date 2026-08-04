<?php
/**
 * Template Name: Busca
 * Description: Página de busca interativa com filtros em tempo real pelo catálogo da F5 TV.
 */

get_header();
?>

<div class="min-h-screen bg-f5-blue text-white font-sans selection:bg-f5-red p-6 md:p-10">
    <div class="max-w-7xl mx-auto flex flex-col gap-6">
        <div class="border-b border-zinc-900 pb-4 flex flex-col gap-4">
            <span class="text-f5-red font-mono font-black text-xs tracking-widest uppercase">CATÁLOGO GLOBAL</span>
            <h1 class="text-3xl font-black tracking-tight">Buscar no Catálogo</h1>
            
            <div class="relative w-full max-w-2xl">
                <input type="text" id="f5tv-search-input" placeholder="Digite título, gênero, palavra-chave ou categoria..." class="w-full bg-f5-blue-950 border border-zinc-800 rounded-2xl px-5 py-4 text-sm text-white placeholder-zinc-500 outline-none focus:border-f5-red transition shadow-2xl">
            </div>
        </div>

        <div id="f5tv-search-results" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 mt-2">
            <div class="col-span-full py-16 text-center text-zinc-500 font-mono text-xs">
                Carregando catálogo completo...
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const input = document.getElementById('f5tv-search-input');
    const container = document.getElementById('f5tv-search-results');
    if (!container) return;

    let fullCatalog = [];

    try {
        const res = await fetch('/wp-json/f5tv/v1/catalog');
        fullCatalog = await res.json();
        renderResults(fullCatalog);
    } catch (e) {
        container.innerHTML = '<div class="col-span-full py-16 text-center text-zinc-500 font-mono text-xs">Erro ao carregar o catálogo.</div>';
    }

    function renderResults(items) {
        if (!Array.isArray(items) || items.length === 0) {
            container.innerHTML = '<div class="col-span-full py-16 text-center text-zinc-500 font-mono text-xs">Nenhum título encontrado para sua busca.</div>';
            return;
        }

        container.innerHTML = items.map(item => `
            <a href="/assista?id=${item.id}" class="group flex flex-col bg-f5-blue-950 border border-zinc-900 rounded-xl overflow-hidden hover:scale-103 transition duration-200 shadow-lg">
                <div class="aspect-video w-full bg-zinc-900 relative">
                    <img src="${item.coverUrl || 'https://images.unsplash.com/photo-1594909122845-11baa439b7bf?q=80&w=1280'}" alt="${item.title}" class="w-full h-full object-contain group-hover:opacity-80 transition">
                </div>
                <div class="p-3 flex flex-col gap-1">
                    <strong class="text-xs text-white line-clamp-1 group-hover:text-f5-red transition">${item.title}</strong>
                    <span class="text-[10px] font-mono text-zinc-500 uppercase">${item.category || 'Streaming'}</span>
                </div>
            </a>
        `).join('');
    }

    input?.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase().trim();
        if (!query) {
            renderResults(fullCatalog);
            return;
        }

        const filtered = fullCatalog.filter(item => {
            return (item.title && item.title.toLowerCase().includes(query)) ||
                   (item.category && item.category.toLowerCase().includes(query)) ||
                   (item.genre && item.genre.toLowerCase().includes(query));
        });

        renderResults(filtered);
    });
});
</script>

<?php get_footer(); ?>
