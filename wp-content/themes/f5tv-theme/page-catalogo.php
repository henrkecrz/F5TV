<?php
/**
 * Public F5 TV catalog page.
 * Uses the seeded f5tv_conteudo entries and local cover assets.
 */
get_header();

$program_query = new WP_Query([
    'post_type'      => 'f5tv_conteudo',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'title',
    'order'          => 'ASC',
]);

$catalog_genres = [];
foreach ($program_query->posts as $catalog_post) {
    $catalog_genre = f5tv_get_field('genre', $catalog_post->ID) ?: 'F5 TV';
    $catalog_genres[$catalog_genre] = true;
}
$catalog_genres = array_keys($catalog_genres);
?>

<div class="min-h-screen bg-f5-blue text-white font-sans selection:bg-f5-red selection:text-white">
    <section class="max-w-7xl w-full mx-auto px-6 md:px-8 pt-12 pb-8 border-b border-white/5">
        <span class="text-[10px] font-mono font-black tracking-[0.2em] text-f5-red uppercase">CATÁLOGO F5 TV</span>
        <h1 class="text-3xl md:text-5xl font-black tracking-tight text-white leading-none mt-2">Programas e conteúdos</h1>
        <p class="text-zinc-400 text-sm md:text-base font-semibold mt-4 max-w-2xl">Conheça os programas que fazem parte da nova televisão portuguesa.</p>
    </section>

    <main class="max-w-7xl w-full mx-auto px-6 md:px-8 py-8 md:py-10">
        <?php if ($program_query->have_posts()): ?>
            <div class="mb-8 md:mb-10">
                <div class="flex items-center justify-between gap-4 mb-3">
                    <span class="text-[10px] font-mono font-bold tracking-[0.18em] text-zinc-500 uppercase">Explorar por categoria</span>
                    <span id="f5tv-catalog-count" class="text-[10px] font-mono text-zinc-500 uppercase tracking-wider"><?php echo esc_html(count($program_query->posts)); ?> títulos</span>
                </div>
                <label class="f5tv-catalog-mobile-select relative block sm:hidden">
                    <span class="sr-only">Filtrar catálogo por categoria</span>
                    <select id="f5tv-catalog-select" class="w-full appearance-none rounded-xl border border-white/10 bg-white/5 px-4 py-3 pr-10 text-sm font-semibold text-white outline-none focus:border-f5-red">
                        <option value="all">Todos os conteúdos</option>
                        <?php foreach ($catalog_genres as $catalog_genre): $catalog_slug = sanitize_title($catalog_genre); ?>
                            <option value="<?php echo esc_attr($catalog_slug); ?>"><?php echo esc_html($catalog_genre); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <svg class="pointer-events-none absolute right-4 top-1/2 h-4 w-4 -translate-y-1/2 text-f5-red" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                </label>
                <div class="f5tv-catalog-filters flex items-center gap-2 overflow-x-auto pb-2 -mx-1 px-1" role="tablist" aria-label="Filtrar catálogo por categoria">
                    <button type="button" class="f5tv-catalog-filter is-active shrink-0 rounded-full px-4 py-2 text-xs font-bold transition" data-filter="all" role="tab" aria-selected="true">Todos</button>
                    <?php foreach ($catalog_genres as $catalog_genre): $catalog_slug = sanitize_title($catalog_genre); ?>
                        <button type="button" class="f5tv-catalog-filter shrink-0 rounded-full px-4 py-2 text-xs font-bold transition" data-filter="<?php echo esc_attr($catalog_slug); ?>" role="tab" aria-selected="false"><?php echo esc_html($catalog_genre); ?></button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div id="f5tv-catalog-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5 lg:gap-6">
                <?php while ($program_query->have_posts()): $program_query->the_post();
                    $post_id = get_the_ID();
                    $cover = f5tv_get_16x9_image($post_id);
                    $genre = f5tv_get_field('genre', $post_id) ?: 'F5 TV';
                    $genre_slug = sanitize_title($genre);
                    $age_rating = f5tv_get_field('age_rating', $post_id) ?: 'Livre';
                    $description = get_the_excerpt();
                ?>
                    <article class="f5tv-catalog-card group bg-f5-blue-950 border border-white/5 hover:border-f5-red/60 rounded-xl overflow-hidden transition-all duration-300 hover:-translate-y-1 shadow-xl" data-genre="<?php echo esc_attr($genre_slug); ?>">
                        <a href="<?php echo esc_url(get_permalink($post_id)); ?>" class="block">
                            <div class="aspect-video relative bg-f5-blue-900">
                                <?php if ($cover): ?>
                                    <img src="<?php echo esc_url($cover); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-contain opacity-90 group-hover:opacity-100 transition duration-300">
                                <?php endif; ?>
                                <span class="absolute top-2 right-2 bg-black/80 text-[10px] font-mono font-bold text-gray-200 px-1.5 py-0.5 rounded border border-white/10"><?php echo esc_html($age_rating); ?></span>
                            </div>
                            <div class="p-3 bg-f5-blue-950">
                                <span class="text-[9px] font-mono uppercase text-f5-red tracking-wider"><?php echo esc_html($genre); ?></span>
                                <h2 class="font-bold text-sm text-white mt-1 line-clamp-2"><?php the_title(); ?></h2>
                                <?php if ($description): ?><p class="text-[11px] text-zinc-500 mt-2 line-clamp-3"><?php echo esc_html($description); ?></p><?php endif; ?>
                            </div>
                        </a>
                    </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
            <div id="f5tv-catalog-empty" class="py-20 text-center" style="display:none">
                <h2 class="text-lg font-bold text-zinc-300">Nenhum conteúdo nesta categoria</h2>
                <p class="text-sm text-zinc-500 mt-2">Escolha outra categoria para continuar a explorar.</p>
            </div>
        <?php else: ?>
            <div class="py-24 text-center">
                <h2 class="text-xl font-bold text-zinc-300">Conteúdos disponíveis em breve</h2>
                <p class="text-sm text-zinc-500 mt-2">Estamos a preparar o catálogo F5 TV.</p>
            </div>
        <?php endif; ?>
    </main>
</div>

<style>
    .f5tv-catalog-filters {
        scrollbar-width: none;
    }

    .f5tv-catalog-filters::-webkit-scrollbar {
        display: none;
    }

    .f5tv-catalog-mobile-select {
        display: none;
    }

    .f5tv-catalog-filter {
        color: rgba(255, 255, 255, 0.58);
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .f5tv-catalog-filter:hover,
    .f5tv-catalog-filter.is-active {
        color: #fff;
        background: #e52329;
        border-color: #e52329;
        box-shadow: 0 8px 24px rgba(229, 35, 41, 0.2);
    }

    @media (max-width: 639px) {
        .f5tv-catalog-mobile-select {
            display: block;
        }

        .f5tv-catalog-filters {
            display: none;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const filters = [...document.querySelectorAll('.f5tv-catalog-filter')];
    const cards = [...document.querySelectorAll('.f5tv-catalog-card')];
    const emptyState = document.getElementById('f5tv-catalog-empty');
    const count = document.getElementById('f5tv-catalog-count');

    function applyFilter(selected) {
        let visible = 0;

        filters.forEach((filter) => {
            const active = filter.dataset.filter === selected;
            filter.classList.toggle('is-active', active);
            filter.setAttribute('aria-selected', active ? 'true' : 'false');
        });

        cards.forEach((card) => {
            const show = selected === 'all' || card.dataset.genre === selected;
            card.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        if (emptyState) emptyState.style.display = visible ? 'none' : '';
        if (count) count.textContent = `${visible} ${visible === 1 ? 'título' : 'títulos'}`;
    }

    filters.forEach((filter) => filter.addEventListener('click', () => applyFilter(filter.dataset.filter)));
    const select = document.getElementById('f5tv-catalog-select');
    if (select) select.addEventListener('change', () => applyFilter(select.value));
});
</script>

<?php get_footer(); ?>
