<?php
/**
 * Archive template for F5TV Series CPT
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
            'views'       => f5tv_get_field('views_count') ?: 12000,
            'link'        => get_permalink(),
        ];
    }
    wp_reset_postdata();
}
?>

<div class="min-h-screen bg-f5-blue text-white font-sans selection:bg-f5-red p-6 md:p-10">
    <div class="max-w-7xl mx-auto flex flex-col gap-8">
        <div class="border-b border-zinc-900 pb-4">
            <span class="text-f5-red font-mono font-black text-xs tracking-widest uppercase">CATÁLOGO DE SÉRIES</span>
            <h1 class="text-3xl md:text-5xl font-black tracking-tight mt-1">Séries F5 TV</h1>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($series_list)): ?>
                <div class="col-span-full py-16 text-center text-zinc-500 font-mono text-xs">
                    Nenhuma série cadastrada no momento.
                </div>
            <?php else: foreach ($series_list as $ser): ?>
                <a href="<?php echo esc_url($ser['link']); ?>" class="group relative bg-f5-blue-950 border border-zinc-900 rounded-2xl overflow-hidden hover:border-f5-red transition duration-300 shadow-xl flex flex-col">
                    <div class="aspect-[16/9] w-full bg-zinc-900 relative overflow-hidden">
                        <img src="<?php echo esc_url($ser['bannerUrl']); ?>" alt="<?php echo esc_attr($ser['title']); ?>" class="w-full h-full object-cover opacity-70 group-hover:opacity-100 group-hover:scale-103 transition duration-300">
                        <div class="absolute top-3 left-3 bg-f5-red text-[9px] font-mono font-bold text-white px-2 py-0.5 rounded uppercase shadow">
                            SÉRIE F5 TV
                        </div>
                    </div>
                    <div class="p-5 flex flex-col gap-2 justify-between flex-1">
                        <div class="flex flex-col gap-1">
                            <span class="text-[10px] font-mono font-bold text-f5-red uppercase tracking-wider"><?php echo esc_html($ser['genre']); ?></span>
                            <h3 class="font-bold text-base text-white group-hover:text-f5-red transition"><?php echo esc_html($ser['title']); ?></h3>
                            <p class="text-zinc-400 text-xs line-clamp-2 leading-relaxed font-medium"><?php echo esc_html($ser['description']); ?></p>
                        </div>
                    </div>
                </a>
            <?php endforeach; endif; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
