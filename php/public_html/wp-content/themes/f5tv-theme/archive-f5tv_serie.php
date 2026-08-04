<?php
/**
 * Archive template for F5TV Series CPT - Identical to AppHomePage.tsx series section (React project)
 * Grid layout: grid-cols-1 sm:grid-cols-2 lg:grid-cols-3
 */

get_header();

$series_query = new WP_Query([
    'post_type'      => 'f5tv_serie',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
]);

$series_list = [];
if ($series_query->have_posts()) {
    while ($series_query->have_posts()) {
        $series_query->the_post();
        $terms = get_the_terms(get_the_ID(), 'f5tv_genero');
        $series_list[] = [
            'id'          => get_the_ID(),
            'title'       => get_the_title(),
            'description' => get_the_content(),
            'coverUrl'    => f5tv_get_field('cover_url') ?: get_the_post_thumbnail_url(get_the_ID(), 'medium') ?: 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=600',
            'bannerUrl'   => f5tv_get_field('banner_url') ?: get_the_post_thumbnail_url(get_the_ID(), 'full') ?: 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=1200',
            'genre'       => ($terms && !is_wp_error($terms)) ? $terms[0]->name : 'Série F5 TV',
            'link'        => get_permalink(),
        ];
    }
    wp_reset_postdata();
}
?>

<div id="archive-serie-root" class="min-h-screen bg-f5-blue text-white font-sans selection:bg-f5-red selection:text-white animate-fade-in">

    <!-- Section header - identical to AppHomePage "SÉRIES INVESTIGATIVAS & EXCLUSIVAS" -->
    <section class="max-w-7xl w-full mx-auto px-6 md:px-8 pt-10 pb-6 border-b border-white/5">
        <div class="flex flex-col gap-1">
            <span class="text-[10px] font-mono font-black tracking-[0.2em] text-f5-red uppercase">CATÁLOGO F5 TV</span>
            <h1 class="text-3xl md:text-5xl font-black tracking-tight text-white leading-none flex items-center gap-3">
                <svg class="w-8 h-8 text-f5-red shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                </svg>
                <span>Séries Investigativas &amp; Exclusivas</span>
            </h1>
            <p class="text-zinc-400 text-sm font-semibold mt-1">Produções originais e investigações jornalísticas táticas disponíveis para assinantes.</p>
        </div>
    </section>

    <!-- Series Grid - identical to AppHomePage collection-series-row -->
    <main class="max-w-7xl w-full mx-auto px-6 md:px-8 py-10">
        <?php if (empty($series_list)): ?>
            <div class="flex flex-col items-center justify-center py-24 gap-4 text-center">
                <svg class="w-12 h-12 text-f5-red opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                </svg>
                <h2 class="text-xl font-bold text-zinc-300">Nenhuma série disponível</h2>
                <p class="text-xs text-zinc-500 font-mono max-w-sm">Nenhuma série publicada no momento. Volte em breve para novidades.</p>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="mt-2 bg-f5-red hover:bg-f5-red-700 text-white font-bold px-5 py-2 rounded text-xs uppercase font-mono tracking-wider transition">
                    Voltar ao Início
                </a>
            </div>
        <?php else: ?>
            <div id="collection-series-row" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($series_list as $ser): ?>
                    <div class="group relative bg-f5-blue-950/80 border border-zinc-900 rounded-xl overflow-hidden cursor-pointer hover:border-red-600 transition shadow-lg shrink-0 flex flex-col">
                        <a href="<?php echo esc_url($ser['link']); ?>" class="flex flex-col flex-1">
                            <div class="aspect-[16/9] w-full bg-f5-blue-900 overflow-hidden relative">
                                <img
                                    src="<?php echo esc_url($ser['bannerUrl']); ?>"
                                    alt="<?php echo esc_attr($ser['title']); ?>"
                                    referrerpolicy="no-referrer"
                                    class="w-full h-full object-cover opacity-60 group-hover:opacity-90 group-hover:scale-[1.02] transition duration-300"
                                >
                                <div class="absolute top-2 left-2 bg-f5-red text-[9px] font-bold text-white px-1.5 py-0.5 rounded uppercase font-mono tracking-wider">
                                    SÉRIE F5 TV
                                </div>
                            </div>
                            <div class="p-4 flex flex-col gap-1 justify-between flex-1">
                                <div class="flex flex-col gap-1">
                                    <span class="text-[10px] font-mono tracking-wider font-semibold uppercase text-f5-red">
                                        <?php echo esc_html($ser['genre']); ?>
                                    </span>
                                    <h3 class="font-bold text-sm text-zinc-150 group-hover:text-white">
                                        <?php echo esc_html($ser['title']); ?>
                                    </h3>
                                    <?php if ($ser['description']): ?>
                                        <p class="text-zinc-500 text-xs line-clamp-2 leading-relaxed font-semibold">
                                            <?php echo esc_html(wp_strip_all_tags($ser['description'])); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

</div>

<?php get_footer(); ?>
