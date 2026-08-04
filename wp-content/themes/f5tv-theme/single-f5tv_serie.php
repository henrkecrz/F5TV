<?php
/**
 * Single series details template for F5TV Theme
 */

get_header();

while (have_posts()): the_post();
    $cover_url = f5tv_get_field('cover_url') ?: get_the_post_thumbnail_url(get_the_ID(), 'large') ?: 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=600';
    $banner_url = f5tv_get_field('banner_url') ?: get_the_post_thumbnail_url(get_the_ID(), 'full') ?: 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=1200';
    $views_count = f5tv_get_field('views_count') ?: 14200;
    $terms = get_the_terms(get_the_ID(), 'f5tv_genero');
    $genre_name = ($terms && !is_wp_error($terms)) ? $terms[0]->name : 'Série F5 TV';
?>

<div class="min-h-screen bg-f5-blue text-white font-sans selection:bg-f5-red">
    
    <!-- Hero Banner with backdrop -->
    <div class="relative h-[480px] w-full bg-black overflow-hidden border-b border-zinc-900">
        <div class="absolute inset-0 bg-cover bg-center opacity-50 grayscale-[0.2]" style="background-image: url('<?php echo esc_url($banner_url); ?>');"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-[#030315] via-[#030315]/60 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-[#030315] via-[#030315]/80 to-transparent"></div>

        <div class="max-w-7xl mx-auto h-full px-6 md:px-10 flex items-end pb-10 relative z-10 w-full">
            <div class="flex flex-col md:flex-row items-start md:items-end gap-6 w-full">
                <div class="w-36 md:w-48 aspect-[3/4] bg-f5-blue-950 rounded-2xl overflow-hidden border border-zinc-800 shadow-2xl shrink-0 hidden sm:block">
                    <img src="<?php echo esc_url($cover_url); ?>" alt="<?php the_title(); ?>" class="w-full h-full object-cover">
                </div>

                <div class="flex flex-col gap-3 max-w-3xl">
                    <div class="flex items-center gap-2">
                        <span class="bg-f5-red text-white text-[10px] font-mono font-black uppercase px-2.5 py-0.5 rounded tracking-wider shadow">
                            SÉRIE ORIGINAL F5
                        </span>
                        <span class="text-xs font-mono font-bold text-f5-red uppercase tracking-wider">&bull; <?php echo esc_html($genre_name); ?></span>
                    </div>

                    <h1 class="text-3xl sm:text-5xl font-black text-white leading-none"><?php the_title(); ?></h1>
                    
                    <div class="text-zinc-300 text-xs sm:text-sm leading-relaxed font-medium line-clamp-3">
                        <?php the_content(); ?>
                    </div>

                    <div class="flex items-center gap-4 text-xs font-mono text-zinc-400 mt-2">
                        <span>&#128065; <?php echo number_format($views_count, 0, ',', '.'); ?> visualizações</span>
                        <span>&bull;</span>
                        <span class="text-green-400">Classificação: Livre</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Seasons & Episodes section -->
    <main class="max-w-7xl mx-auto px-6 md:px-10 py-12 flex flex-col gap-8">
        <h2 class="text-2xl font-black text-white uppercase tracking-tight flex items-center gap-2">
            <span class="text-f5-red">&#9679;</span>
            <span>Temporadas e Episódios</span>
        </h2>

        <?php
        $seasons = get_posts([
            'post_type'      => 'f5tv_temporada',
            'posts_per_page' => -1,
            'meta_query'     => [
                [
                    'key'     => 'series_id',
                    'value'   => get_the_ID(),
                    'compare' => '=',
                ],
            ],
            'orderby'        => 'title',
            'order'          => 'ASC',
        ]);

        if (empty($seasons)):
        ?>
            <div class="bg-f5-blue-950 border border-zinc-900 rounded-2xl p-12 text-center text-zinc-500 font-mono text-xs">
                Nenhuma temporada cadastrada para esta série no momento.
            </div>
        <?php else: foreach ($seasons as $season): ?>
            <div class="bg-f5-blue-950 border border-zinc-900 rounded-2xl p-6 md:p-8 flex flex-col gap-6 shadow-xl">
                <div class="flex items-center justify-between border-b border-zinc-900 pb-4">
                    <h3 class="text-xl font-bold text-white"><?php echo esc_html($season->post_title); ?></h3>
                    <span class="text-xs font-mono text-zinc-500 uppercase">Temporada <?php echo get_field('number', $season->ID) ?: '1'; ?></span>
                </div>

                <?php
                $episodes = get_posts([
                    'post_type'      => 'f5tv_episodio',
                    'posts_per_page' => -1,
                    'meta_key'       => 'season_id',
                    'meta_value'     => $season->ID,
                    'orderby'        => 'meta_value_num',
                    'meta_key'       => 'number',
                    'order'          => 'ASC',
                ]);
                ?>

                <?php if (empty($episodes)): ?>
                    <p class="text-xs text-zinc-500 font-mono italic">Sem episódios disponíveis.</p>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach ($episodes as $ep):
                            $ep_num = f5tv_get_field('number', $ep->ID) ?: '1';
                            $ep_duration = f5tv_get_field('duration', $ep->ID) ?: '45m';
                            $ep_video = f5tv_get_field('video_url', $ep->ID) ?: 'https://assets.mixkit.co/videos/preview/mixkit-software-developer-working-on-his-computer-34289-large.mp4';
                            $ep_thumb = f5tv_get_field('thumbnail_url', $ep->ID) ?: get_the_post_thumbnail_url($ep->ID, 'medium') ?: $banner_url;
                        ?>
                            <a href="<?php echo esc_url(home_url('/assista?id=' . $ep->ID)); ?>" class="group bg-f5-blue-900/60 border border-zinc-850 hover:border-f5-red rounded-xl p-3 flex gap-4 items-center transition duration-200 shadow-md">
                                <div class="w-28 md:w-32 aspect-video bg-zinc-950 rounded-lg overflow-hidden shrink-0 relative">
                                    <img src="<?php echo esc_url($ep_thumb); ?>" alt="<?php echo esc_attr($ep->post_title); ?>" class="w-full h-full object-cover opacity-70 group-hover:opacity-100 transition">
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition bg-black/40">
                                        <span class="text-white text-lg">&#9654;</span>
                                    </div>
                                </div>
                                <div class="flex-1 flex flex-col gap-1 text-left min-w-0">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-mono font-bold text-f5-red">Ep. <?php echo esc_html($ep_num); ?></span>
                                        <span class="text-[9px] font-mono text-zinc-500"><?php echo esc_html($ep_duration); ?></span>
                                    </div>
                                    <h4 class="font-bold text-xs text-white group-hover:text-f5-red line-clamp-1 transition"><?php echo esc_html($ep->post_title); ?></h4>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; endif; ?>
    </main>
</div>

<?php endwhile; ?>

<?php get_footer(); ?>
