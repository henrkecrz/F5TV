<?php
/**
 * Archive taxonomy template for F5TV Theme (genre)
 */

get_header();

$term = get_queried_object();
?>

<div class="min-h-screen bg-f5-blue text-white font-sans">
    <section class="px-4 sm:px-8 py-12 border-b border-white/5">
        <div class="max-w-7xl mx-auto">
            <span class="text-[10px] font-mono font-bold tracking-[0.2em] text-f5-red uppercase">GÊNERO</span>
            <h1 class="text-3xl md:text-5xl font-black tracking-tighter text-white mt-2">
                <?php echo esc_html($term->name); ?>
            </h1>
            <?php if ($term->description): ?>
                <p class="text-white/60 text-base mt-2 max-w-2xl"><?php echo esc_html($term->description); ?></p>
            <?php endif; ?>
        </div>
    </section>

    <main class="max-w-7xl mx-auto px-4 sm:px-8 py-12">
        <?php if (have_posts()): ?>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <?php while (have_posts()): the_post();
                    $cover = get_field('cover_url') ?: get_the_post_thumbnail_url(get_the_ID(), 'medium');
                    $genre = get_field('genre') ?: '';
                    $age_rating = get_field('age_rating') ?: 'Livre';
                    $is_exclusive = get_field('is_exclusive');
                ?>
                    <article class="group relative bg-f5-blue-950 border border-white/5 hover:border-f5-red rounded-lg overflow-hidden cursor-pointer hover:border-f5-red/50 transform transition-all duration-300 hover:scale-[1.03] shadow-2xl">
                        <a href="<?php the_permalink(); ?>" class="block">
                            <div class="aspect-[3/4] relative w-full bg-f5-blue-900">
                                <?php if ($cover): ?>
                                    <img src="<?php echo esc_url($cover); ?>" alt="<?php the_title(); ?>" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity duration-300">
                                <?php endif; ?>
                                <div class="absolute top-2.5 right-2.5 bg-f5-blue/90 text-[10px] font-mono font-bold text-f5-red px-2 py-0.5 rounded border border-white/5">
                                    <?php echo esc_html($age_rating); ?>
                                </div>
                                <?php if ($is_exclusive): ?>
                                    <div class="absolute bottom-2.5 left-2.5 bg-f5-red text-[10px] font-bold text-black px-2 py-0.5 rounded uppercase font-sans tracking-tight">
                                        Exclusivo
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="p-4 bg-f5-blue-950 flex flex-col gap-1.5">
                                <span class="text-[9px] font-mono tracking-wider font-semibold uppercase text-white/40">
                                    <?php echo esc_html($genre); ?>
                                </span>
                                <h3 class="font-bold text-sm text-white/90 line-clamp-1 group-hover:text-white transition-colors duration-250">
                                    <?php the_title(); ?>
                                </h3>
                            </div>
                        </a>
                    </article>
                <?php endwhile; ?>
            </div>

            <div class="mt-12">
                <?php the_posts_pagination(); ?>
            </div>
        <?php else: ?>
            <div class="text-center py-20">
                <p class="text-zinc-500 text-sm">Nenhum conteúdo encontrado neste gênero.</p>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php get_footer(); ?>
