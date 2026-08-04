<?php
/**
 * Single content template for F5TV Theme
 * Idêntico ao ContentDetailsPage.tsx do projeto React.
 */

get_header();

while (have_posts()): the_post();
    $content_type     = get_field('content_type') ?: 'video';
    $cover_url        = get_field('cover_url') ?: get_the_post_thumbnail_url(get_the_ID(), 'large');
    $banner_url       = get_field('banner_url') ?: get_the_post_thumbnail_url(get_the_ID(), 'full');
    $video_url        = get_field('video_url');
    $trailer_url      = get_field('trailer_url');
    $is_free          = get_field('is_free');
    $is_exclusive     = get_field('is_exclusive');
    $year             = get_field('year') ?: date('Y');
    $duration         = get_field('duration') ?: '';
    $age_rating       = get_field('age_rating') ?: 'Livre';
    $views_count      = (int)(get_field('views_count') ?: 0);
    $short_description = get_field('short_description') ?: get_the_excerpt();
    $full_description  = get_field('full_description') ?: get_the_content();
    $directors        = get_field('directors') ?: [];
    $cast             = get_field('cast') ?: [];
    $tags             = get_field('tags') ?: [];

    $genres   = get_the_terms(get_the_ID(), 'f5tv_genero');
    $category = get_the_terms(get_the_ID(), 'f5tv_categoria');
    $genre_name    = ($genres && !is_wp_error($genres))   ? $genres[0]->name   : '';
    $category_name = ($category && !is_wp_error($category)) ? $category[0]->name : 'Especiais';

    // Related content from same category
    $cat_id = ($category && !is_wp_error($category)) ? $category[0]->term_id : 0;
    $related_posts = [];
    if ($cat_id) {
        $related_posts = get_posts([
            'post_type'      => 'f5tv_conteudo',
            'posts_per_page' => 4,
            'post_status'    => 'publish',
            'post__not_in'   => [get_the_ID()],
            'tax_query'      => [[
                'taxonomy' => 'f5tv_categoria',
                'field'    => 'term_id',
                'terms'    => $cat_id,
            ]],
        ]);
    }

    // Series / Seasons / Episodes
    $series_posts = get_posts([
        'post_type'      => 'f5tv_serie',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
        's'              => get_the_title(),
    ]);
    $has_series = !empty($series_posts);
    $all_seasons = [];
    if ($has_series) {
        $all_seasons = get_posts([
            'post_type'      => 'f5tv_temporada',
            'posts_per_page' => -1,
            'meta_query'     => [[
                'key'     => 'series_id',
                'value'   => $series_posts[0]->ID,
                'compare' => '=',
            ]],
            'orderby'        => 'meta_value_num',
            'meta_key'       => 'number',
            'order'          => 'ASC',
        ]);
    }

    // Reviews (WP comments used as reviews)
    $reviews = get_comments([
        'post_id' => get_the_ID(),
        'status'  => 'approve',
        'number'  => 20,
    ]);
    $avg = 0;
    if (!empty($reviews)) {
        $sum = 0; $cnt = 0;
        foreach ($reviews as $rev) {
            $r = (int) get_comment_meta($rev->comment_ID, 'rating', true);
            if ($r > 0) { $sum += $r; $cnt++; }
        }
        $avg = $cnt > 0 ? round($sum / $cnt, 1) : 5.0;
    } else {
        $avg = 5.0;
    }
?>

<div id="content-details-page" class="min-h-screen bg-f5-blue text-zinc-300 font-sans animate-fade-in selection:bg-f5-red selection:text-white">

    <!-- Breadcrumb -->
    <div class="max-w-7xl mx-auto px-6 md:px-8 pt-6">
        <a href="<?php echo esc_url(home_url('/series/')); ?>" class="inline-flex items-center gap-1 text-zinc-500 hover:text-white transition text-xs font-mono font-bold uppercase">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            <span>Voltar ao catálogo</span>
        </a>
    </div>

    <!-- Main Hero Header — idêntico ao ContentDetailsPage.tsx hero section -->
    <section class="relative h-[45vh] md:h-[55vh] flex items-end p-6 md:p-12 border-b border-zinc-900 bg-black overflow-hidden select-none mb-8">
        <?php if ($banner_url): ?>
            <div class="absolute inset-0 bg-cover bg-center opacity-30 md:opacity-50" style="background-image: url('<?php echo esc_url($banner_url); ?>');"></div>
        <?php endif; ?>
        <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/40 to-transparent"></div>

        <div class="relative z-10 max-w-7xl mx-auto w-full flex items-end justify-between gap-6">
            <div class="flex flex-col items-start gap-3 max-w-3xl">
                <div class="inline-flex items-center gap-1.5 bg-f5-red text-white font-mono font-black text-[10px] tracking-wider uppercase px-2 py-0.5 rounded-sm">
                    <?php echo esc_html($category_name); ?>
                </div>
                <h1 class="text-3xl md:text-5xl font-black tracking-tight leading-none text-white"><?php the_title(); ?></h1>
                <?php if ($short_description): ?>
                    <p class="text-zinc-400 text-xs md:text-sm font-semibold max-w-2xl"><?php echo esc_html($short_description); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Player de detalhes no estilo YouTube; o Play abre a reprodução em tela cheia. -->
    <section class="max-w-7xl mx-auto px-6 md:px-8 mb-10">
        <div class="relative aspect-video max-w-5xl mx-auto rounded-2xl overflow-hidden border border-white/10 bg-black shadow-2xl group" id="f5tv-details-player" aria-label="Player de vídeo">
            <?php if ($banner_url || $cover_url): ?>
                <img src="<?php echo esc_url($banner_url ?: $cover_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:opacity-75 transition duration-300">
            <?php endif; ?>
            <div class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-black/10"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <a href="<?php echo esc_url(home_url('/assista?id=' . get_the_ID())); ?>" class="w-20 h-20 rounded-full bg-f5-red hover:bg-f5-red-700 flex items-center justify-center shadow-2xl hover:scale-110 transition-transform" aria-label="Reproduzir <?php echo esc_attr(get_the_title()); ?>">
                    <svg class="w-9 h-9 fill-white ml-1" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </a>
            </div>
            <div class="absolute inset-x-0 bottom-0 px-4 md:px-6 pb-4 pt-16 bg-gradient-to-t from-black/95 to-transparent">
                <div class="h-1 rounded-full bg-white/25 mb-3 overflow-hidden"><div class="h-full w-0 bg-f5-red rounded-full"></div></div>
                <div class="flex items-center justify-between text-white">
                    <div class="flex items-center gap-2 md:gap-3">
                        <a href="<?php echo esc_url(home_url('/assista?id=' . get_the_ID())); ?>" class="w-9 h-9 flex items-center justify-center hover:text-f5-red transition" aria-label="Play"><svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></a>
                        <button type="button" class="w-9 h-9 hidden sm:flex items-center justify-center hover:text-f5-red transition" aria-label="Volume"><svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3a3.5 3.5 0 00-2.5-3.35v6.7A3.5 3.5 0 0016.5 12zM14 3.23v2.06a7 7 0 010 13.42v2.06a9 9 0 000-17.54z"/></svg></button>
                        <span class="text-[10px] font-mono text-white/70">0:00 / --:--</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <button type="button" class="w-9 h-9 hidden sm:flex items-center justify-center hover:text-f5-red transition" aria-label="Configurações"><svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M19.43 12.98c.04-.32.07-.65.07-.98s-.02-.66-.07-.98l2.11-1.65-2-3.46-2.49 1a7.4 7.4 0 00-1.69-.98L15 3h-4l-.36 2.53c-.6.24-1.17.56-1.69.98l-2.49-1-2 3.46 2.11 1.65c-.04.32-.08.65-.08.98s.03.66.08.98l-2.11 1.65 2 3.46 2.49-1c.52.42 1.09.74 1.69.98L11 21h4l.36-2.53c.6-.24 1.17-.56 1.69-.98l2.49 1 2-3.46-2.11-1.65zM13 15.5A3.5 3.5 0 1113 8a3.5 3.5 0 010 7.5z"/></svg></button>
                        <a href="<?php echo esc_url(home_url('/assista?id=' . get_the_ID())); ?>" class="w-9 h-9 flex items-center justify-center hover:text-f5-red transition" aria-label="Tela cheia"><svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/></svg></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Split Grid — lg:grid-cols-3 — idêntico ao ContentDetailsPage.tsx -->
    <div class="max-w-7xl mx-auto px-6 md:px-8 grid grid-cols-1 lg:grid-cols-3 gap-12 mb-16">

        <!-- LEFT: col-span-2 -->
        <div class="lg:col-span-2 flex flex-col gap-6">

            <!-- Metadata pills bar -->
            <div class="flex flex-wrap items-center gap-3 text-xs font-mono font-bold text-zinc-500">
                <span class="bg-f5-blue-900 text-f5-red px-2 py-0.5 rounded border border-zinc-800 tracking-widest"><?php echo esc_html($age_rating); ?></span>
                <?php if ($year): ?>
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span><?php echo esc_html($year); ?></span>
                    </span>
                <?php endif; ?>
                <?php if ($duration): ?>
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span><?php echo esc_html($duration); ?></span>
                    </span>
                <?php endif; ?>
                <?php if ($is_exclusive): ?>
                    <span class="bg-red-950 text-red-500 border border-f5-red-900 px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-sans">Exclusivo Assinante</span>
                <?php endif; ?>
                <?php if ($is_free): ?>
                    <span class="bg-emerald-950 text-emerald-400 border border-emerald-900 px-1.5 py-0.5 rounded text-[9px] uppercase tracking-wider font-sans">Acesso Gratuito</span>
                <?php endif; ?>
            </div>

            <!-- Full description box -->
            <?php if ($full_description): ?>
                <div class="p-1 bg-f5-blue-950/40 rounded-2xl border border-zinc-900">
                    <div class="p-6 md:p-8 flex flex-col gap-5">
                        <h3 class="text-sm font-mono tracking-wider font-black text-f5-red uppercase">Sobre este Programa</h3>
                        <p class="text-sm md:text-base leading-relaxed text-zinc-300 font-semibold">
                            <?php echo esc_html(wp_strip_all_tags($full_description)); ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- CTA Action buttons — idêntico ao ContentDetailsPage.tsx -->
            <div class="flex flex-wrap gap-3.5">
                <?php if ($video_url): ?>
                    <a href="<?php echo esc_url(home_url('/assista?id=' . get_the_ID())); ?>"
                       class="bg-f5-red hover:bg-f5-red-700 text-white font-bold py-3.5 px-7 rounded-xl text-xs tracking-wider uppercase font-mono flex items-center justify-center gap-2 cursor-pointer transition shadow-lg shadow-f5-red-700/10">
                        <svg class="w-4 h-4 fill-white" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        <span><?php echo $has_series ? 'Assistir Ep. 1' : 'Assistir Agora'; ?></span>
                    </a>
                <?php endif; ?>

                <?php if ($trailer_url): ?>
                    <a href="<?php echo esc_url($trailer_url); ?>" target="_blank"
                       class="bg-f5-blue-900 border border-zinc-800 hover:bg-f5-blue-800 hover:border-zinc-700 text-white font-bold py-3.5 px-6 rounded-xl text-xs tracking-wider uppercase font-mono flex items-center justify-center gap-2 cursor-pointer transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>
                        <span>Ver Teaser</span>
                    </a>
                <?php endif; ?>

                <?php f5tv_render_minha_lista_button(get_the_ID()); ?>
            </div>

            <!-- SERIES EPISODES PANEL — idêntico ao ContentDetailsPage.tsx episodes panel -->
            <?php if ($has_series && !empty($all_seasons)): ?>
                <div class="border-t border-zinc-900 pt-8 mt-4">
                    <?php foreach ($all_seasons as $season_index => $season):
                        $season_num = f5tv_get_field('number', $season->ID) ?: ($season_index + 1);
                        $episodes = get_posts([
                            'post_type'      => 'f5tv_episodio',
                            'posts_per_page' => -1,
                            'meta_query'     => [[
                                'key'     => 'season_id',
                                'value'   => $season->ID,
                                'compare' => '=',
                            ]],
                            'orderby'        => 'meta_value_num',
                            'meta_key'       => 'number',
                            'order'          => 'ASC',
                        ]);
                    ?>
                        <details class="mb-4 group" <?php echo $season_index === 0 ? 'open' : ''; ?>>
                            <summary class="flex items-center justify-between cursor-pointer p-4 bg-f5-blue-950/30 border border-zinc-900 rounded-xl hover:border-zinc-800 transition list-none">
                                <h3 class="text-zinc-100 font-bold text-sm flex items-center gap-2">
                                    <svg class="w-5 h-5 text-f5-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>
                                    <span><?php echo esc_html($season->post_title ?: 'Temporada ' . $season_num); ?></span>
                                    <span class="text-[10px] font-mono text-zinc-500"><?php echo count($episodes); ?> episódios</span>
                                </h3>
                                <svg class="w-4 h-4 text-zinc-500 group-open:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </summary>

                            <?php if (empty($episodes)): ?>
                                <div class="bg-f5-blue-950/30 p-10 text-center text-zinc-650 rounded-xl border border-dashed border-zinc-900 text-xs mt-3">
                                    Nenhum episódio publicado para esta temporada ainda.
                                </div>
                            <?php else: ?>
                                <div class="flex flex-col gap-3 mt-3 max-h-96 overflow-y-auto pr-1">
                                    <?php foreach ($episodes as $ep):
                                        $ep_num    = f5tv_get_field('number', $ep->ID) ?: '1';
                                        $ep_dur    = f5tv_get_field('duration', $ep->ID) ?: '';
                                        $ep_video  = f5tv_get_field('video_url', $ep->ID);
                                        $ep_thumb  = f5tv_get_field('thumbnail_url', $ep->ID) ?: get_the_post_thumbnail_url($ep->ID, 'medium');
                                        $ep_desc   = get_post_field('post_excerpt', $ep->ID) ?: f5tv_get_field('short_description', $ep->ID) ?: '';
                                        $ep_link   = $ep_video ? home_url('/assista?id=' . $ep->ID) : '#';
                                    ?>
                                        <a href="<?php echo esc_url($ep_link); ?>"
                                           class="bg-f5-blue-950/30 hover:bg-f5-blue-950 border border-zinc-900 hover:border-zinc-800 p-4 rounded-xl flex gap-4 items-center cursor-pointer group transition duration-200">
                                            <div class="w-24 md:w-32 aspect-video bg-f5-blue-900 rounded-lg overflow-hidden shrink-0 relative">
                                                <?php if ($ep_thumb): ?>
                                                    <img src="<?php echo esc_url($ep_thumb); ?>" alt="<?php echo esc_attr($ep->post_title); ?>"
                                                         class="w-full h-full object-cover opacity-60 group-hover:opacity-100 transition duration-200">
                                                <?php endif; ?>
                                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition bg-black/40">
                                                    <svg class="w-6 h-6 fill-white text-white" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                                </div>
                                            </div>
                                            <div class="flex-1 flex flex-col gap-1 text-left min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-mono font-bold text-f5-red">Episódio <?php echo esc_html($ep_num); ?></span>
                                                    <?php if ($ep_dur): ?>
                                                        <span class="text-[10px] text-zinc-600 font-mono">(<?php echo esc_html($ep_dur); ?>)</span>
                                                    <?php endif; ?>
                                                </div>
                                                <h4 class="font-bold text-sm text-zinc-200 group-hover:text-white line-clamp-1"><?php echo esc_html($ep->post_title); ?></h4>
                                                <?php if ($ep_desc): ?>
                                                    <p class="text-[11px] text-zinc-500 line-clamp-2 leading-relaxed font-semibold"><?php echo esc_html($ep_desc); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </details>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>

        <!-- RIGHT: Credits + Related — idêntico ao ContentDetailsPage.tsx right pane -->
        <div class="flex flex-col gap-6 text-zinc-400 font-semibold border-t lg:border-t-0 border-zinc-900 pt-8 lg:pt-0">

            <div class="bg-f5-blue-950/50 border border-zinc-900 rounded-2xl p-6 flex flex-col gap-5 text-xs font-mono">

                <?php if (!empty($directors)): ?>
                    <div class="flex flex-col gap-1.5">
                        <span class="text-zinc-500 font-bold uppercase tracking-wider text-[10px] flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-f5-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.87v6.263a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/></svg>
                            <span>Direção / Produção</span>
                        </span>
                        <span class="text-zinc-250 font-bold text-xs"><?php echo esc_html(implode(', ', (array)$directors)); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($cast)): ?>
                    <div class="flex flex-col gap-1.5">
                        <span class="text-zinc-500 font-bold uppercase tracking-wider text-[10px] flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-f5-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>Elenco / Apresentação</span>
                        </span>
                        <span class="text-zinc-250 font-bold text-xs leading-relaxed"><?php echo esc_html(implode(', ', (array)$cast)); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($views_count > 0): ?>
                    <div class="flex flex-col gap-1.5">
                        <span class="text-zinc-500 font-bold uppercase tracking-wider text-[10px]">Popularidade F5</span>
                        <span class="text-zinc-300 font-bold text-xs"><?php echo number_format($views_count, 0, ',', '.'); ?> visualizações</span>
                    </div>
                <?php endif; ?>

                <div class="flex flex-col gap-1.5">
                    <span class="text-zinc-500 font-bold uppercase tracking-wider text-[10px]">Categoria Geral</span>
                    <span class="text-f5-red font-bold text-xs"><?php echo esc_html($category_name); ?></span>
                </div>

                <?php if (!empty($tags)): ?>
                    <div class="flex flex-col gap-1.5">
                        <span class="text-zinc-500 font-bold uppercase tracking-wider text-[10px]">Tags do Conteúdo</span>
                        <div class="flex flex-wrap gap-1 mt-1 text-[9px] uppercase tracking-wider">
                            <?php foreach ((array)$tags as $tag): ?>
                                <span class="bg-f5-blue-900 text-zinc-500 border border-zinc-850 px-2 py-0.5 rounded"><?php echo esc_html($tag); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Related Recommendations — idêntico ao ContentDetailsPage.tsx "Você Também Pode Curtir" -->
            <?php if (!empty($related_posts)): ?>
                <div class="flex flex-col gap-4">
                    <h3 class="text-xs font-mono font-black uppercase text-zinc-500 tracking-wider">Você Também Pode Curtir</h3>
                    <div class="flex flex-col gap-3">
                        <?php foreach ($related_posts as $related):
                            $rel_cover  = f5tv_get_field('cover_url', $related->ID) ?: get_the_post_thumbnail_url($related->ID, 'thumbnail') ?: '';
                            $rel_genre  = f5tv_get_field('genre', $related->ID) ?: '';
                            $rel_dur    = f5tv_get_field('duration', $related->ID) ?: '';
                        ?>
                            <a href="<?php echo esc_url(get_permalink($related->ID)); ?>"
                               class="flex gap-3 bg-f5-blue-950 p-2 rounded-xl border border-zinc-900 hover:border-zinc-800 transition cursor-pointer group">
                                <div class="w-16 h-20 rounded bg-f5-blue-900 overflow-hidden shrink-0">
                                    <?php if ($rel_cover): ?>
                                        <img src="<?php echo esc_url($rel_cover); ?>" alt="<?php echo esc_attr($related->post_title); ?>"
                                             class="w-full h-full object-cover group-hover:opacity-100 opacity-70 transition">
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1 flex flex-col justify-center text-left">
                                    <?php if ($rel_genre): ?>
                                        <span class="text-[9px] font-mono font-bold text-f5-red uppercase tracking-wider"><?php echo esc_html($rel_genre); ?></span>
                                    <?php endif; ?>
                                    <h4 class="font-bold text-xs text-white/90 group-hover:text-white line-clamp-1 mt-0.5"><?php echo esc_html($related->post_title); ?></h4>
                                    <?php if ($rel_dur): ?>
                                        <span class="text-[10px] text-zinc-500 mt-1"><?php echo esc_html($rel_dur); ?></span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- REVIEWS SECTION — idêntico ao ContentDetailsPage.tsx "Avaliações F5 TV" -->
    <section class="border-t border-zinc-900 w-full bg-[#070707] py-16 px-6 md:px-8">
        <div class="max-w-7xl mx-auto flex flex-col gap-8">

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-6 border-b border-zinc-900">
                <div>
                    <h3 class="text-base font-bold text-white uppercase tracking-wider font-mono flex items-center gap-2">
                        <svg class="w-5 h-5 fill-f5-red text-f5-red" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                        <span>Avaliações F5 TV</span>
                    </h3>
                    <p class="text-zinc-500 text-xs font-semibold mt-1">Sua opinião é vital para a evolução do nosso portfólio.</p>
                </div>
                <div class="flex items-center gap-5 bg-f5-blue-950 border border-zinc-900 px-4 py-2.5 rounded-xl font-mono">
                    <div class="flex flex-col items-center">
                        <span class="text-[9px] text-zinc-500 uppercase font-black">Score</span>
                        <span class="text-lg font-black text-f5-red mt-0.5"><?php echo number_format($avg, 1); ?> ★</span>
                    </div>
                    <div class="h-8 w-px bg-zinc-800"></div>
                    <div class="flex flex-col items-center">
                        <span class="text-[9px] text-zinc-500 uppercase font-black">Resenhas</span>
                        <span class="text-lg font-black text-zinc-300 mt-0.5"><?php echo count($reviews); ?></span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

                <!-- Review form -->
                <div class="lg:col-span-2 bg-f5-blue-950/40 border border-f5-red/5 p-5 rounded-xl flex flex-col gap-4">
                    <h4 class="text-xs font-mono font-black text-zinc-300 uppercase tracking-widest">Enviar sua avaliação</h4>
                    <?php if (is_user_logged_in()): ?>
                        <form method="post" action="<?php echo esc_url(site_url('/wp-comments-post.php')); ?>" class="flex flex-col gap-4" id="f5tv-review-form">
                            <?php wp_nonce_field('add-comment'); ?>
                            <input type="hidden" name="comment_post_ID" value="<?php echo get_the_ID(); ?>">
                            <input type="hidden" name="redirect_to" value="<?php echo esc_url(get_permalink()); ?>">
                            <div class="flex flex-col gap-1.5">
                                <span class="text-xs font-semibold text-zinc-400">Quantas estrelas este título merece?</span>
                                <div class="flex gap-1.5 mt-0.5" id="f5tv-star-rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <button type="button" data-star="<?php echo $i; ?>"
                                                class="cursor-pointer focus:outline-none transition hover:scale-110 text-zinc-700 hover:text-yellow-400 text-2xl star-btn">★</button>
                                    <?php endfor; ?>
                                </div>
                                <input type="hidden" name="f5tv_rating" id="f5tv-rating-input" value="5">
                            </div>
                            <textarea name="comment" rows="3"
                                      placeholder="Diga à comunidade o que achou da produção F5 TV..."
                                      class="w-full bg-[#030303] border border-white/5 focus:border-f5-red rounded-lg p-3 text-xs text-zinc-100 placeholder:text-zinc-600 outline-none transition resize-none font-medium leading-relaxed"
                                      required></textarea>
                            <button type="submit"
                                    class="w-full bg-f5-red hover:bg-f5-red-700 text-white font-bold py-2.5 rounded-lg text-xs uppercase tracking-wider font-mono cursor-pointer transition shadow">
                                Publicar Opinião
                            </button>
                        </form>
                        <script>
                        (function(){
                            const btns = document.querySelectorAll('.star-btn');
                            const inp = document.getElementById('f5tv-rating-input');
                            let selected = 5;
                            function paint(n) {
                                btns.forEach((b, i) => {
                                    b.style.color = i < n ? '#facc15' : '#3f3f46';
                                });
                            }
                            paint(selected);
                            btns.forEach((b, i) => {
                                b.addEventListener('mouseover', () => paint(i+1));
                                b.addEventListener('mouseleave', () => paint(selected));
                                b.addEventListener('click', () => { selected = i+1; inp.value = selected; paint(selected); });
                            });
                        })();
                        </script>
                    <?php else: ?>
                        <p class="text-xs text-zinc-400">
                            <a href="<?php echo esc_url(wp_login_url(get_permalink())); ?>" class="text-f5-red hover:underline">Entre na sua conta</a> para deixar sua avaliação.
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Reviews list -->
                <div class="lg:col-span-3 flex flex-col gap-4">
                    <h4 class="text-xs font-mono font-black text-zinc-400 uppercase tracking-widest">Resenhas Recentes</h4>
                    <?php if (!empty($reviews)): ?>
                        <div class="flex flex-col gap-3 max-h-80 overflow-y-auto pr-2">
                            <?php foreach ($reviews as $review):
                                $rating = (int) get_comment_meta($review->comment_ID, 'rating', true) ?: 4;
                            ?>
                                <div class="bg-f5-blue-950 border border-zinc-900 p-4 rounded-xl flex flex-col gap-2 hover:border-zinc-850 transition">
                                    <div class="flex justify-between items-center gap-2 text-xs">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg bg-zinc-800 text-xs font-bold text-white flex items-center justify-center">
                                                <?php echo esc_html(strtoupper(substr($review->comment_author, 0, 1))); ?>
                                            </div>
                                            <div class="flex flex-col text-left">
                                                <span class="font-bold text-zinc-200"><?php echo esc_html($review->comment_author); ?></span>
                                                <span class="text-[10px] text-zinc-500 font-mono"><?php echo get_comment_date('d/m/Y', $review); ?></span>
                                            </div>
                                        </div>
                                        <div class="flex gap-0.5">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <span class="text-sm <?php echo $i <= $rating ? 'text-yellow-400' : 'text-zinc-800'; ?>">★</span>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <p class="text-xs text-zinc-350 leading-relaxed font-medium"><?php echo esc_html($review->comment_content); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="flex-1 border border-zinc-900 border-dashed rounded-xl p-8 text-center text-zinc-600 text-xs flex flex-col items-center justify-center gap-2">
                            <span class="text-4xl">★</span>
                            <p>Seja o primeiro a deixar sua opinião crítica sobre este título!</p>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </section>

</div>

<?php endwhile; ?>

<?php get_footer(); ?>
