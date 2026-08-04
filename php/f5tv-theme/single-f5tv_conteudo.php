<?php
/**
 * Single content template for F5TV Theme
 */

get_header();

while (have_posts()): the_post();
    $content_type = get_field('content_type');
    $cover_url = get_field('cover_url') ?: get_the_post_thumbnail_url(get_the_ID(), 'large');
    $banner_url = get_field('banner_url') ?: get_the_post_thumbnail_url(get_the_ID(), 'full');
    $video_url = get_field('video_url');
    $trailer_url = get_field('trailer_url');
    $is_free = get_field('is_free');
    $is_exclusive = get_field('is_exclusive');
    $is_featured = get_field('is_featured');
    $year = get_field('year');
    $duration = get_field('duration');
    $age_rating = get_field('age_rating');
    $views_count = get_field('views_count');
    $short_description = get_field('short_description') ?: get_the_excerpt();
    $full_description = get_field('full_description') ?: get_the_content();
    $directors = get_field('directors') ?: [];
    $cast = get_field('cast') ?: [];
    $tags = get_field('tags') ?: [];
    $genres = get_the_terms(get_the_ID(), 'f5tv_genero');
    $category = get_the_terms(get_the_ID(), 'f5tv_categoria');
    $genre_name = ($genres && !is_wp_error($genres)) ? $genres[0]->name : '';
    $category_name = ($category && !is_wp_error($category)) ? $category[0]->name : '';

    // Series info
    $series_posts = get_posts([
        'post_type' => 'f5tv_serie',
        'posts_per_page' => 1,
        'title' => get_the_title(),
        'post_status' => 'publish',
    ]);
    $has_series = !empty($series_posts);
    ?>

    <div class="min-h-screen bg-f5-blue text-white font-sans">
        <?php if ($banner_url): ?>
            <div class="relative h-60 md:h-80 w-full bg-f5-blue-900">
                <img src="<?php echo esc_url($banner_url); ?>" alt="<?php the_title(); ?>" class="w-full h-full object-cover opacity-50">
                <div class="absolute inset-0 bg-gradient-to-t from-[#0a0a0a] to-transparent"></div>
                <div class="absolute bottom-6 left-6 md:left-10 right-6 z-10">
                    <div class="flex flex-col gap-2">
                        <span class="text-xs font-mono font-bold tracking-wider text-f5-red uppercase leading-relaxed">
                            <?php echo esc_html($category_name); ?><?php echo $genre_name ? ' &bull; ' . esc_html($genre_name) : ''; ?>
                        </span>
                        <h1 class="text-2xl md:text-4xl font-black text-white leading-tight"><?php the_title(); ?></h1>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <main class="p-6 md:p-10 grid grid-cols-1 md:grid-cols-3 gap-8 text-zinc-300">
            <div class="md:col-span-2 flex flex-col gap-5">
                <div class="flex flex-wrap items-center gap-3 text-xs font-mono font-bold text-zinc-400">
                    <?php if ($age_rating): ?>
                        <span class="bg-f5-blue-900 text-f5-red px-2 py-0.5 rounded border border-zinc-800 tracking-widest"><?php echo esc_html($age_rating); ?></span>
                    <?php endif; ?>
                    <?php if ($year): ?><span><?php echo esc_html($year); ?></span><?php endif; ?>
                    <?php if ($duration): ?><span><?php echo esc_html($duration); ?></span><?php endif; ?>
                    <?php if ($is_exclusive): ?>
                        <span class="bg-red-950 text-f5-red border border-f5-red-900 px-2 py-0.5 rounded tracking-wide font-sans text-[10px] uppercase">Exclusivo Assinante</span>
                    <?php endif; ?>
                    <?php if ($is_free): ?>
                        <span class="bg-emerald-950 text-emerald-400 border border-emerald-900 px-1.5 py-0.5 rounded text-[10px] font-sans tracking-wide uppercase">Gratuito</span>
                    <?php endif; ?>
                </div>

                <p class="text-sm md:text-base leading-relaxed font-semibold text-zinc-350">
                    <?php echo esc_html($short_description); ?>
                </p>

                <div class="flex flex-wrap gap-3">
                    <?php if ($video_url): ?>
                        <a href="<?php echo esc_url($video_url); ?>" target="_blank" class="bg-f5-red hover:bg-f5-red-700 text-white font-bold py-3 px-6 rounded-lg text-xs tracking-wider uppercase font-mono flex items-center justify-center gap-2 cursor-pointer transition shadow-md shadow-f5-red-700/30">
                            <span>Assistir Agora</span>
                        </a>
                    <?php endif; ?>
                    <?php if ($trailer_url): ?>
                        <a href="<?php echo esc_url($trailer_url); ?>" target="_blank" class="bg-f5-blue-900/90 border border-zinc-800 hover:bg-f5-blue-800 hover:border-zinc-700 text-white font-bold py-3 px-6 rounded-lg text-xs tracking-wider uppercase font-mono flex items-center justify-center gap-2 cursor-pointer transition">
                            <span>Ver Trailer</span>
                        </a>
                    <?php endif; ?>
                </div>

                <?php if ($has_series): ?>
                    <div class="border-t border-zinc-900 pt-6 mt-4">
                        <h3 class="text-zinc-200 font-bold text-base flex items-center gap-2 mb-4">
                            <span class="text-f5-red">&#9679;</span>
                            <span>Episódios</span>
                        </h3>
                        <div class="flex flex-col gap-3">
                            <?php
                            $seasons = get_posts([
                                'post_type' => 'f5tv_temporada',
                                'posts_per_page' => -1,
                                'post_status' => 'publish',
                                'meta_key' => 'series_id',
                                'meta_value' => $series_posts[0]->ID,
                            ]);
                            foreach ($seasons as $season):
                                $episodes = get_posts([
                                    'post_type' => 'f5tv_episodio',
                                    'posts_per_page' => -1,
                                    'post_status' => 'publish',
                                    'meta_key' => 'season_id',
                                    'meta_value' => $season->ID,
                                ]);
                                foreach ($episodes as $ep):
                                    $ep_video = get_field('video_url', $ep->ID);
                                    $ep_thumb = get_field('thumbnail_url', $ep->ID) ?: get_the_post_thumbnail_url($ep->ID, 'medium');
                            ?>
                                <div class="bg-f5-blue-900/60 hover:bg-f5-blue-900 border border-zinc-850/80 hover:border-zinc-750 p-3 rounded-lg flex gap-4 items-center cursor-pointer group transition duration-200">
                                    <div class="w-24 md:w-32 aspect-video bg-f5-blue-950 rounded-md overflow-hidden shrink-0 relative">
                                        <?php if ($ep_thumb): ?>
                                            <img src="<?php echo esc_url($ep_thumb); ?>" alt="<?php echo esc_attr(get_the_title($ep)); ?>" class="w-full h-full object-cover opacity-60 group-hover:opacity-100 transition duration-200">
                                        <?php endif; ?>
                                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                            <span class="text-white text-xl">&#9654;</span>
                                        </div>
                                    </div>
                                    <div class="flex-1 flex flex-col gap-1 text-left">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-mono font-bold text-f5-red">Episódio <?php echo get_field('number', $ep->ID); ?></span>
                                        </div>
                                        <h4 class="font-bold text-sm text-zinc-100 group-hover:text-white line-clamp-1"><?php echo esc_html(get_the_title($ep)); ?></h4>
                                    </div>
                                </div>
                            <?php endforeach; endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="flex flex-col gap-5 text-xs font-mono border-t md:border-t-0 border-zinc-900 pt-6 md:pt-0">
                <?php if ($directors): ?>
                    <div class="flex flex-col gap-1.5">
                        <span class="text-zinc-500 font-bold uppercase">Diretor / Produtor</span>
                        <span class="text-zinc-200 font-bold text-xs"><?php echo esc_html(implode(', ', $directors)); ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($cast): ?>
                    <div class="flex flex-col gap-1.5">
                        <span class="text-zinc-500 font-bold uppercase">Cast / Apresentação</span>
                        <span class="text-zinc-200 font-bold text-xs"><?php echo esc_html(implode(', ', $cast)); ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($views_count): ?>
                    <div class="flex flex-col gap-1.5">
                        <span class="text-zinc-500 font-bold uppercase">Popularidade</span>
                        <span class="text-zinc-300 font-bold text-xs"><?php echo number_format($views_count, 0, ',', '.'); ?> visualizações</span>
                    </div>
                <?php endif; ?>
                <?php if ($tags): ?>
                    <div class="flex flex-col gap-1.5">
                        <span class="text-zinc-500 font-bold uppercase">Marcas de Tag</span>
                        <div class="flex flex-wrap gap-1 mt-1 text-[10px]">
                            <?php foreach ($tags as $tag): ?>
                                <span class="bg-f5-blue-900 text-zinc-400 border border-zinc-800 px-2 py-0.5 rounded uppercase"><?php echo esc_html($tag); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>

        <?php
        // Reviews section
        $reviews = get_comments([
            'post_id' => get_the_ID(),
            'status' => 'approve',
            'number' => 10,
        ]);
        $average_rating = get_comments_number() ? get_comment_meta(get_comments()[0]->comment_ID, 'rating', true) : 0;
        ?>

        <div class="border-t border-white/5 bg-[#070707] p-6 md:p-10 flex flex-col gap-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-4 border-b border-white/5">
                <div>
                    <h3 class="text-lg font-bold text-white uppercase tracking-wider font-mono flex items-center gap-2">
                        <span class="text-f5-red">&#9733;</span>
                        <span>Opinião da Comunidade F5 TV</span>
                    </h3>
                </div>
                <div class="flex items-center gap-6 bg-[#0c0c0d] border border-white/5 px-4 py-2.5 rounded-xl font-mono">
                    <div class="flex flex-col items-center">
                        <span class="text-[10px] text-zinc-500 uppercase font-black">Média Geral</span>
                        <span class="text-xl font-black text-f5-red mt-0.5"><?php echo number_format($average_rating, 1); ?> &#9733;</span>
                    </div>
                    <div class="h-8 w-px bg-zinc-800"></div>
                    <div class="flex flex-col items-center">
                        <span class="text-[10px] text-zinc-500 uppercase font-black">Resenhas</span>
                        <span class="text-lg font-black text-zinc-300 mt-0.5"><?php echo count($reviews); ?></span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                <div class="lg:col-span-2 bg-[#0a0a0b]/40 border border-f5-red/5 p-5 rounded-xl flex flex-col gap-4">
                    <h4 class="text-xs font-mono font-black text-zinc-300 uppercase tracking-widest">Enviar sua avaliação</h4>
                    <?php if (is_user_logged_in()): ?>
                        <form action="<?php echo esc_url(get_comment_link()); ?>" method="post" class="flex flex-col gap-4">
                            <div class="flex flex-col gap-1.5">
                                <span class="text-xs font-semibold text-zinc-400">Quantas estrelas este título merece?</span>
                                <div class="flex gap-1 mt-0.5">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <button type="button" class="cursor-pointer focus:outline-none transition hover:scale-110 text-zinc-700 hover:text-zinc-550">
                                            <span class="text-2xl">&#9733;</span>
                                        </button>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <textarea name="comment" rows="3" placeholder="Diga à comunidade o que achou da produção F5 TV..." class="w-full bg-[#030303] border border-white/5 focus:border-f5-red rounded-lg p-3 text-xs text-zinc-100 placeholder:text-zinc-600 outline-none transition resize-none font-medium leading-relaxed"></textarea>
                            <button type="submit" class="w-full bg-f5-red hover:bg-f5-red-700 text-white font-bold py-2.5 rounded-lg text-xs uppercase tracking-wider font-mono cursor-pointer transition shadow hover:shadow-red-950/40">
                                Publicar Opinião
                            </button>
                        </form>
                    <?php else: ?>
                        <p class="text-xs text-zinc-400"><a href="<?php echo esc_url(wp_login_url()); ?>" class="text-f5-red hover:underline">Entre</a> para deixar sua avaliação.</p>
                    <?php endif; ?>
                </div>

                <div class="lg:col-span-3 flex flex-col gap-4">
                    <h4 class="text-xs font-mono font-black text-zinc-400 uppercase tracking-widest">Resenhas Recentes</h4>
                    <?php if ($reviews): ?>
                        <div class="flex flex-col gap-3 max-h-80 overflow-y-auto pr-2 scrollbar-thin">
                            <?php foreach ($reviews as $review): ?>
                                <div class="bg-[#0b0b0c] border border-white/5 p-4 rounded-xl flex flex-col gap-2 transition hover:border-zinc-850">
                                    <div class="flex justify-between items-center gap-2 text-xs">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-zinc-800 text-xs font-bold text-white flex items-center justify-center">
                                                <?php echo esc_html(strtoupper(substr($review->comment_author, 0, 1))); ?>
                                            </div>
                                            <div class="flex flex-col text-left">
                                                <span class="font-bold text-zinc-200"><?php echo esc_html($review->comment_author); ?></span>
                                                <span class="text-[10px] text-zinc-550 font-mono"><?php echo get_comment_date('d/m/Y', $review); ?></span>
                                            </div>
                                        </div>
                                        <div class="flex gap-0.5">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <span class="text-sm <?php echo ($i <= 4) ? 'text-f5-red' : 'text-zinc-800'; ?>">&#9733;</span>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <p class="text-xs text-zinc-350 leading-relaxed text-left font-medium"><?php echo esc_html($review->comment_content); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="flex-1 border border-zinc-900 border-dashed rounded-xl p-8 text-center text-zinc-600 text-xs flex flex-col items-center justify-center gap-2">
                            <span class="text-4xl">&#9733;</span>
                            <p>Este título ainda não possui resenhas. Seja o primeiro a deixar sua opinião crítica!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<?php endwhile; ?>

<?php get_footer(); ?>
