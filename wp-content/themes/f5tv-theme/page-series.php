<?php
/**
 * Template Name: Catálogo de Séries
 * Description: Página de séries investigativas e exclusivas — idêntica ao AppHomePage.tsx (React).
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

$featured = $series_list[0] ?? null;

// Fetch categorized contents for category rows (identical to AppHomePage category sections)
$all_categories = get_terms([
    'taxonomy'   => 'f5tv_categoria',
    'hide_empty' => true,
]);

$category_rows = [];
if ($all_categories && !is_wp_error($all_categories)) {
    foreach ($all_categories as $cat) {
        $cat_posts = get_posts([
            'post_type'      => 'f5tv_conteudo',
            'posts_per_page' => 12,
            'post_status'    => 'publish',
            'tax_query'      => [[
                'taxonomy' => 'f5tv_categoria',
                'field'    => 'term_id',
                'terms'    => $cat->term_id,
            ]],
        ]);
        if (!empty($cat_posts)) {
            $category_rows[] = [
                'cat'   => $cat,
                'posts' => $cat_posts,
            ];
        }
    }
}
?>

<div id="page-series-root" class="min-h-screen bg-f5-blue text-white font-sans selection:bg-f5-red selection:text-white animate-fade-in flex flex-col gap-0">

    <?php if ($featured): ?>
    <!-- 1. Immersive Hero Banner — idêntico ao AppHomePage hero-banner -->
    <section id="hero-banner" class="relative h-[60vh] flex items-end p-6 md:p-12 border-b border-zinc-900 bg-black overflow-hidden select-none">
        <div
            class="absolute inset-0 bg-cover bg-center opacity-40 md:opacity-50"
            style="background-image: url('<?php echo esc_url($featured['bannerUrl']); ?>');"
        ></div>
        <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/40 to-black/30"></div>

        <div class="relative z-10 max-w-3xl flex flex-col items-start gap-4 mx-auto w-full">
            <div class="inline-flex items-center gap-1.5 bg-f5-red text-white font-mono font-black text-[10px] tracking-wider uppercase px-2 py-0.5 rounded-sm">
                EM DESTAQUE F5 HOJE
            </div>

            <h1 class="text-3xl md:text-5xl font-black tracking-tight leading-none text-white">
                <?php echo esc_html($featured['title']); ?>
            </h1>
            <p class="text-zinc-350 text-xs md:text-sm leading-relaxed line-clamp-2 md:line-clamp-3 font-semibold max-w-2xl">
                <?php echo esc_html(wp_strip_all_tags($featured['description'])); ?>
            </p>

            <div class="flex flex-wrap gap-3 mt-2">
                <a
                    href="<?php echo esc_url($featured['link']); ?>"
                    class="bg-f5-red hover:bg-f5-red-700 text-white font-bold px-5 py-2.5 rounded flex items-center gap-1.5 text-xs uppercase cursor-pointer tracking-wider font-mono transition"
                >
                    <svg class="w-4 h-4 fill-white" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    <span>Assistir Agora</span>
                </a>
                <a
                    href="<?php echo esc_url($featured['link']); ?>"
                    class="bg-f5-blue-900/80 border border-zinc-800 hover:bg-zinc-800 hover:border-zinc-700 text-white font-bold px-5 py-2.5 rounded flex items-center gap-1.5 text-xs uppercase cursor-pointer tracking-wider font-mono transition"
                >
                    <svg class="w-4 h-4 text-f5-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Detalhes</span>
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- 2. Series collection grid — idêntico ao "collection-series-row" AppHomePage -->
    <section id="collection-series-row" class="max-w-7xl w-full mx-auto px-6 md:px-8 py-10">
        <h3 class="text-xs font-mono tracking-widest text-zinc-400 font-black uppercase mb-4 flex items-center gap-1.5">
            <svg class="w-4 h-4 text-f5-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
            </svg>
            <span>SÉRIES INVESTIGATIVAS &amp; EXCLUSIVAS</span>
        </h3>

        <?php if (empty($series_list)): ?>
            <div class="flex flex-col items-center justify-center py-20 gap-4 text-center">
                <svg class="w-12 h-12 text-f5-red opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                </svg>
                <p class="text-xs text-zinc-500 font-mono">Nenhuma série cadastrada no momento.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
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
    </section>

    <!-- 3. Categorized content rows — idêntico às category sections do AppHomePage -->
    <?php if (!empty($category_rows)): ?>
        <div class="flex flex-col gap-4 pb-12">
            <?php foreach ($category_rows as $row): ?>
                <section class="max-w-7xl w-full mx-auto px-6 md:px-8 py-2">
                    <h3 class="text-xs font-mono tracking-widest text-zinc-400 font-black uppercase mb-4 flex items-center gap-1.5">
                        <span class="w-1.5 h-3 bg-f5-red rounded-sm inline-block"></span>
                        <span><?php echo esc_html($row['cat']->name); ?></span>
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        <?php foreach ($row['posts'] as $item):
                            $cover      = f5tv_get_field('cover_url', $item->ID) ?: get_the_post_thumbnail_url($item->ID, 'medium') ?: 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=600';
                            $genre      = f5tv_get_field('genre', $item->ID) ?: '';
                            $age_rating = f5tv_get_field('age_rating', $item->ID) ?: 'Livre';
                            $exclusive  = f5tv_get_field('is_exclusive', $item->ID);
                        ?>
                            <div class="group relative bg-f5-blue-950 border border-zinc-900 hover:border-red-600 rounded-lg overflow-hidden cursor-pointer transition transform hover:-translate-y-1 block animate-fade-in">
                                <a href="<?php echo esc_url(get_permalink($item->ID)); ?>" class="block">
                                    <div class="aspect-[3/4] relative bg-f5-blue-900">
                                        <img
                                            src="<?php echo esc_url($cover); ?>"
                                            alt="<?php echo esc_attr($item->post_title); ?>"
                                            referrerpolicy="no-referrer"
                                            class="w-full h-full object-cover group-hover:opacity-100 opacity-80 transition duration-200"
                                        >
                                        <div class="absolute top-2 right-2 bg-black/85 text-[10px] font-mono font-bold text-gray-300 px-1.5 py-0.5 rounded border border-white/5">
                                            <?php echo esc_html($age_rating); ?>
                                        </div>
                                        <?php if ($exclusive): ?>
                                            <div class="absolute bottom-2 left-2 bg-f5-red text-[9px] font-bold text-white px-1.5 py-0.5 rounded uppercase font-mono tracking-wider shadow">
                                                Exclusivo
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="p-3 bg-f5-blue-950 flex flex-col gap-0.5">
                                        <?php if ($genre): ?>
                                            <span class="text-[9px] font-mono uppercase text-zinc-500 tracking-wider">
                                                <?php echo esc_html($genre); ?>
                                            </span>
                                        <?php endif; ?>
                                        <h4 class="font-bold text-xs text-zinc-150 line-clamp-1 group-hover:text-white transition">
                                            <?php echo esc_html($item->post_title); ?>
                                        </h4>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php get_footer(); ?>
