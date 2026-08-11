<?php
/**
 * Template Name: Programas
 * Public program catalog. The /catalogo/ URL remains supported for existing links.
 */
get_header();
$programs = get_posts([
    'post_type' => 'f5tv_conteudo',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'orderby' => 'date',
    'order' => 'DESC',
]);
?>
<main class="min-h-screen bg-f5-blue text-white font-sans animate-fade-in">
    <header class="max-w-7xl mx-auto px-6 md:px-8 pt-10 pb-7 border-b border-white/5">
        <span class="text-[10px] font-mono font-black tracking-[0.2em] text-f5-red uppercase">F5 TV ON DEMAND</span>
        <h1 class="text-3xl md:text-5xl font-black tracking-tight mt-2">Programas</h1>
        <p class="text-zinc-400 text-sm font-semibold mt-2">Conteúdos, entrevistas, documentários e produções originais da F5 TV.</p>
        <a href="<?php echo esc_url(home_url('/series/')); ?>" class="inline-flex mt-5 text-xs font-mono font-bold uppercase tracking-widest text-f5-red hover:text-white transition">Ver catálogo de séries</a>
    </header>
    <section class="max-w-7xl mx-auto px-6 md:px-8 py-10">
        <?php if (!$programs): ?><p class="text-zinc-500 font-mono text-sm">Nenhum programa publicado no momento.</p><?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-5">
                <?php foreach ($programs as $program): $cover = f5tv_get_16x9_image($program->ID); $genre = f5tv_get_field('genre', $program->ID); ?>
                    <a href="<?php echo esc_url(get_permalink($program->ID)); ?>" class="group bg-f5-blue-950 border border-zinc-900 hover:border-f5-red rounded-xl overflow-hidden transition hover:-translate-y-1">
                        <div class="aspect-video bg-f5-blue-900"><img src="<?php echo esc_url($cover); ?>" alt="<?php echo esc_attr($program->post_title); ?>" class="w-full h-full object-contain opacity-85 group-hover:opacity-100 transition"></div>
                        <div class="p-3"><span class="text-[9px] font-mono uppercase text-f5-red tracking-wider"><?php echo esc_html($genre ?: 'Programa F5 TV'); ?></span><h2 class="font-bold text-sm text-zinc-150 group-hover:text-white line-clamp-2 mt-1"><?php echo esc_html($program->post_title); ?></h2></div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>
<?php get_footer(); ?>
