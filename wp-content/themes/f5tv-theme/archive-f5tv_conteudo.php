<?php
/**
 * Archive template for F5TV Content CPT - Identical to AppHomePage.tsx (React project)
 * Grid layout: grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6
 */

get_header();

$queried_object = get_queried_object();
$archive_title  = '';
$archive_desc   = '';

if ($queried_object instanceof WP_Term) {
    $archive_title = $queried_object->name;
    $archive_desc  = $queried_object->description;
} elseif (is_post_type_archive()) {
    $archive_title = post_type_archive_title('', false);
}
if (!$archive_title) {
    $archive_title = 'Catálogo';
}
?>

<div id="archive-conteudo-root" class="min-h-screen bg-f5-blue text-white font-sans selection:bg-f5-red selection:text-white flex flex-col gap-0 animate-fade-in">

    <!-- Page header - identical to category section title in AppHomePage -->
    <section class="max-w-7xl w-full mx-auto px-6 md:px-8 pt-10 pb-6 border-b border-white/5 flex items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
            <span class="text-[10px] font-mono font-black tracking-[0.2em] text-f5-red uppercase">CATÁLOGO F5 TV</span>
            <h1 class="text-3xl md:text-5xl font-black tracking-tight text-white leading-none"><?php echo esc_html($archive_title); ?></h1>
            <?php if ($archive_desc): ?>
                <p class="text-zinc-400 text-sm font-semibold mt-1 max-w-2xl"><?php echo esc_html($archive_desc); ?></p>
            <?php endif; ?>
        </div>
        <a href="<?php echo esc_url(home_url('/series/')); ?>" class="hidden sm:flex items-center gap-2 text-xs font-mono font-bold text-zinc-500 hover:text-f5-red transition uppercase tracking-widest shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            <span>Ver Séries</span>
        </a>
    </section>

    <!-- Content Grid - identical to AppHomePage category grid -->
    <main class="max-w-7xl w-full mx-auto px-6 md:px-8 py-10 flex flex-col gap-12">
        <?php if (have_posts()): ?>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <?php while (have_posts()): the_post();
                    $cover      = get_field('cover_url') ?: get_the_post_thumbnail_url(get_the_ID(), 'medium') ?: 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=600';
                    $genre      = get_field('genre') ?: '';
                    $age_rating = get_field('age_rating') ?: 'Livre';
                    $exclusive  = get_field('is_exclusive');
                ?>
                    <article class="group relative bg-f5-blue-950 border border-zinc-900 hover:border-red-600 rounded-lg overflow-hidden cursor-pointer transition transform hover:-translate-y-1 block animate-fade-in">
                        <a href="<?php the_permalink(); ?>" class="block">
                            <div class="aspect-[3/4] relative bg-f5-blue-900">
                                <img
                                    src="<?php echo esc_url($cover); ?>"
                                    alt="<?php the_title_attribute(); ?>"
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
                                    <span class="text-[9px] font-mono uppercase text-zinc-500 tracking-wider"><?php echo esc_html($genre); ?></span>
                                <?php endif; ?>
                                <h3 class="font-bold text-xs text-zinc-150 line-clamp-1 group-hover:text-white transition"><?php the_title(); ?></h3>
                            </div>
                        </a>
                    </article>
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <?php if (get_the_posts_pagination()): ?>
                <div class="flex justify-center mt-8">
                    <?php the_posts_pagination([
                        'mid_size'  => 2,
                        'prev_text' => '&larr; Anterior',
                        'next_text' => 'Próxima &rarr;',
                        'before_page_number' => '<span class="px-3 py-1.5 bg-f5-blue-950 border border-zinc-900 hover:border-f5-red rounded text-xs font-mono font-bold text-zinc-400 hover:text-white transition mx-1 inline-block">',
                        'after_page_number'  => '</span>',
                    ]); ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="flex flex-col items-center justify-center py-24 gap-4 text-center">
                <svg class="w-12 h-12 text-f5-red opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.263a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                </svg>
                <h2 class="text-xl font-bold text-zinc-300">Nenhum conteúdo encontrado</h2>
                <p class="text-xs text-zinc-500 font-mono max-w-sm">Nenhum conteúdo publicado nesta categoria no momento.</p>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="mt-2 bg-f5-red hover:bg-f5-red-700 text-white font-bold px-5 py-2 rounded text-xs uppercase font-mono tracking-wider transition">
                    Voltar ao Início
                </a>
            </div>
        <?php endif; ?>
    </main>

</div>

<?php get_footer(); ?>
