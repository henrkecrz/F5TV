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
?>

<div class="min-h-screen bg-f5-blue text-white font-sans selection:bg-f5-red selection:text-white">
    <section class="max-w-7xl w-full mx-auto px-6 md:px-8 pt-12 pb-8 border-b border-white/5">
        <span class="text-[10px] font-mono font-black tracking-[0.2em] text-f5-red uppercase">CATÁLOGO F5 TV</span>
        <h1 class="text-3xl md:text-5xl font-black tracking-tight text-white leading-none mt-2">Programas e conteúdos</h1>
        <p class="text-zinc-400 text-sm md:text-base font-semibold mt-4 max-w-2xl">Conheça os programas que fazem parte da nova televisão portuguesa.</p>
    </section>

    <main class="max-w-7xl w-full mx-auto px-6 md:px-8 py-10">
        <?php if ($program_query->have_posts()): ?>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 md:gap-5">
                <?php while ($program_query->have_posts()): $program_query->the_post();
                    $post_id = get_the_ID();
                    $program_slug = sanitize_title(get_the_title($post_id));
                    $local_cover_path = F5TV_ASSETS_DIR . '/programas/' . $program_slug . '/' . $program_slug . '-16x9.jpg';
                    $local_cover_url = F5TV_ASSETS_URI . '/programas/' . $program_slug . '/' . $program_slug . '-16x9.jpg';
                    $cover = file_exists($local_cover_path) ? $local_cover_url : (f5tv_get_field('cover_url', $post_id) ?: get_the_post_thumbnail_url($post_id, 'medium'));
                    $genre = f5tv_get_field('genre', $post_id) ?: 'F5 TV';
                    $age_rating = f5tv_get_field('age_rating', $post_id) ?: 'Livre';
                    $description = get_the_excerpt();
                ?>
                    <article class="group bg-f5-blue-950 border border-white/5 hover:border-f5-red/60 rounded-lg overflow-hidden transition-all duration-300 hover:-translate-y-1 shadow-xl">
                        <a href="<?php echo esc_url(get_permalink($post_id)); ?>" class="block">
                            <div class="aspect-video relative bg-f5-blue-900">
                                <?php if ($cover): ?>
                                    <img src="<?php echo esc_url($cover); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover opacity-90 group-hover:opacity-100 transition duration-300">
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
        <?php else: ?>
            <div class="py-24 text-center">
                <h2 class="text-xl font-bold text-zinc-300">Conteúdos disponíveis em breve</h2>
                <p class="text-sm text-zinc-500 mt-2">Estamos a preparar o catálogo F5 TV.</p>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php get_footer(); ?>
