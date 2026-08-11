<?php
/**
 * Single series details template for F5TV Theme
 * Idêntico ao ContentDetailsPage.tsx do projeto React (visão de série com temporadas/episódios).
 */

get_header();

while (have_posts()): the_post();
    $cover_url   = f5tv_get_field('cover_url') ?: get_the_post_thumbnail_url(get_the_ID(), 'large') ?: 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=600';
    $banner_url  = f5tv_get_field('banner_url') ?: get_the_post_thumbnail_url(get_the_ID(), 'full') ?: 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=1200';
    $views_count = (int)(f5tv_get_field('views_count') ?: 14200);
    $age_rating  = f5tv_get_field('age_rating') ?: 'Livre';
    $terms       = get_the_terms(get_the_ID(), 'f5tv_genero');
    $genre_name  = ($terms && !is_wp_error($terms)) ? $terms[0]->name : 'Série F5 TV';
    $description = get_the_content();
    $directors   = (array)(f5tv_get_field('directors') ?: []);
    $cast        = (array)(f5tv_get_field('cast') ?: []);
    $tags        = (array)(f5tv_get_field('tags') ?: []);

    // Seasons & Episodes — tenta por meta_key 'series_id' e por post_parent
    $all_seasons = get_posts([
        'post_type'      => 'f5tv_temporada',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'meta_query'     => [[
            'key'     => 'series_id',
            'value'   => get_the_ID(),
            'compare' => '=',
        ]],
        'orderby'  => 'menu_order',
        'order'    => 'ASC',
    ]);
    // Fallback: tenta via post_parent
    if (empty($all_seasons)) {
        $all_seasons = get_posts([
            'post_type'      => 'f5tv_temporada',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'post_parent'    => get_the_ID(),
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ]);
    }
    // Fallback final: busca qualquer temporada com qualquer relação
    if (empty($all_seasons)) {
        $all_seasons = get_posts([
            'post_type'      => 'f5tv_temporada',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ]);
    }
    $serie_video_url = f5tv_get_field('video_url');
    $serie_trailer_url = f5tv_get_field('trailer_url') ?: get_post_meta(get_the_ID(), 'trailer_url', true);

    // Related series from the same category, with a fallback to any series.
    $category_terms = get_the_terms(get_the_ID(), 'f5tv_categoria');
    $category_id = ($category_terms && !is_wp_error($category_terms)) ? $category_terms[0]->term_id : 0;
    $related_ids = array_values(array_filter(array_map('absint', (array) get_post_meta(get_the_ID(), 'related_series_ids', true))));
    $related_series = $related_ids ? get_posts([
        'post_type' => 'f5tv_serie',
        'post__in' => $related_ids,
        'post__not_in' => [get_the_ID()],
        'posts_per_page' => 4,
        'post_status' => 'publish',
        'orderby' => 'post__in',
    ]) : get_posts([
        'post_type'      => 'f5tv_serie',
        'posts_per_page' => 4,
        'post_status'    => 'publish',
        'post__not_in'   => [get_the_ID()],
        'tax_query'      => $category_id ? [[
            'taxonomy' => 'f5tv_categoria',
            'field' => 'term_id',
            'terms' => $category_id,
        ]] : [],
    ]);

    // Reviews
    $reviews = get_comments([
        'post_id' => get_the_ID(),
        'status'  => 'approve',
        'number'  => 20,
    ]);
    $avg = 5.0;
    if (!empty($reviews)) {
        $sum = 0; $cnt = 0;
        foreach ($reviews as $rev) {
            $r = (int) get_comment_meta($rev->comment_ID, 'rating', true);
            if ($r > 0) { $sum += $r; $cnt++; }
        }
        $avg = $cnt > 0 ? round($sum / $cnt, 1) : 5.0;
    }
?>

<div id="serie-details-page" class="min-h-screen bg-f5-blue text-zinc-300 font-sans animate-fade-in selection:bg-f5-red selection:text-white">

    <!-- Breadcrumb -->
    <div class="max-w-7xl mx-auto px-6 md:px-8 pt-6">
        <a href="<?php echo esc_url(home_url('/catalogo/')); ?>" class="inline-flex items-center gap-1 text-zinc-500 hover:text-white transition text-xs font-mono font-bold uppercase">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            <span>Voltar ao catálogo</span>
        </a>
    </div>

    <!-- Hero Banner — idêntico ao ContentDetailsPage.tsx hero section -->
    <section class="relative h-[45vh] md:h-[55vh] flex items-end p-6 md:p-12 border-b border-zinc-900 bg-black overflow-hidden select-none mb-8">
        <div class="absolute inset-0 bg-cover bg-center opacity-30 md:opacity-50"
             style="background-image: url('<?php echo esc_url($banner_url); ?>');"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/40 to-transparent"></div>

        <div class="relative z-10 max-w-7xl mx-auto w-full flex items-end gap-6">
            <!-- Cover poster (desktop) -->
            <div class="hidden md:block w-36 lg:w-48 aspect-[3/4] bg-f5-blue-950 rounded-2xl overflow-hidden border border-zinc-800 shadow-2xl shrink-0">
                <img src="<?php echo esc_url($cover_url); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover">
            </div>

            <div class="flex flex-col items-start gap-3 max-w-3xl">
                <div class="flex items-center gap-2">
                    <div class="inline-flex items-center gap-1.5 bg-f5-red text-white font-mono font-black text-[10px] tracking-wider uppercase px-2 py-0.5 rounded-sm">
                        SÉRIE ORIGINAL F5
                    </div>
                    <span class="text-xs font-mono font-bold text-f5-red uppercase tracking-wider">• <?php echo esc_html($genre_name); ?></span>
                </div>
                <h1 class="text-3xl md:text-5xl font-black tracking-tight leading-none text-white"><?php the_title(); ?></h1>
                <?php if ($description): ?>
                    <p class="text-zinc-400 text-xs md:text-sm font-semibold max-w-2xl line-clamp-3">
                        <?php echo esc_html(wp_strip_all_tags($description)); ?>
                    </p>
                <?php endif; ?>
                <div class="flex items-center gap-4 text-xs font-mono text-zinc-500 mt-1">
                    <span>👁 <?php echo number_format($views_count, 0, ',', '.'); ?> visualizações</span>
                    <span>•</span>
                    <span class="text-emerald-400">Classificação: <?php echo esc_html($age_rating); ?></span>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Split Grid — lg:grid-cols-3 — idêntico ao ContentDetailsPage.tsx -->
    <div class="max-w-7xl mx-auto px-6 md:px-8 grid grid-cols-1 lg:grid-cols-3 gap-12 mb-16">

        <!-- LEFT col-span-2: Metadata + CTA + Episodes -->
        <div class="lg:col-span-2 flex flex-col gap-6">

            <!-- Metadata pills -->
            <div class="flex flex-wrap items-center gap-3 text-xs font-mono font-bold text-zinc-500">
                <span class="bg-f5-blue-900 text-f5-red px-2 py-0.5 rounded border border-zinc-800 tracking-widest"><?php echo esc_html($age_rating); ?></span>
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-f5-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>
                    <span><?php echo count($all_seasons); ?> Temporada<?php echo count($all_seasons) !== 1 ? 's' : ''; ?></span>
                </span>
                <span class="bg-red-950 text-red-500 border border-f5-red-900 px-2 py-0.5 rounded text-[9px] uppercase tracking-wider font-sans">Exclusivo Assinante</span>
            </div>

            <!-- Description box -->
            <?php if ($description): ?>
                <div class="p-1 bg-f5-blue-950/40 rounded-2xl border border-zinc-900">
                    <div class="p-6 md:p-8 flex flex-col gap-5">
                        <h3 class="text-sm font-mono tracking-wider font-black text-f5-red uppercase">Sobre esta Série</h3>
                        <p class="text-sm md:text-base leading-relaxed text-zinc-300 font-semibold">
                            <?php echo esc_html(wp_strip_all_tags($description)); ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- CTA Buttons — idêntico ao ContentDetailsPage.tsx -->
            <div class="flex flex-wrap gap-3.5">
                <?php
                // Busca primeiro episódio via meta OU post_parent
                $first_ep_id = null;
                $first_ep_query_args = [
                    'post_type'      => 'f5tv_episodio',
                    'posts_per_page' => 1,
                    'post_status'    => 'publish',
                    'orderby'        => 'menu_order',
                    'order'          => 'ASC',
                ];
                if (!empty($all_seasons)) {
                    $first_ep_query_args['meta_query'] = [[
                        'key'     => 'season_id',
                        'value'   => $all_seasons[0]->ID,
                        'compare' => '=',
                    ]];
                    $first_eps = get_posts($first_ep_query_args);
                    // Fallback via post_parent da temporada
                    if (empty($first_eps)) {
                        $first_eps = get_posts([
                            'post_type'      => 'f5tv_episodio',
                            'posts_per_page' => 1,
                            'post_status'    => 'publish',
                            'post_parent'    => $all_seasons[0]->ID,
                            'orderby'        => 'menu_order',
                            'order'          => 'ASC',
                        ]);
                    }
                    if (!empty($first_eps)) $first_ep_id = $first_eps[0]->ID;
                }
                // Determinar link do botão principal
                if ($first_ep_id) {
                    $play_url = home_url('/assista?id=' . $first_ep_id);
                    $play_label = 'Assistir Ep. 1';
                } elseif ($serie_video_url) {
                    $play_url = home_url('/assista?id=' . get_the_ID());
                    $play_label = 'Assistir Agora';
                } else {
                    $play_url = null;
                    $play_label = 'Em Breve';
                }
                ?>
                <?php if ($play_url): ?>
                <a href="<?php echo esc_url($play_url); ?>"
                   class="bg-f5-red hover:bg-f5-red-700 text-white font-bold py-3.5 px-7 rounded-xl text-xs tracking-wider uppercase font-mono flex items-center justify-center gap-2 cursor-pointer transition shadow-lg shadow-f5-red-700/10">
                    <svg class="w-4 h-4 fill-white" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    <span><?php echo esc_html($play_label); ?></span>
                </a>
                <?php else: ?>
                <span class="bg-zinc-800 text-zinc-500 font-bold py-3.5 px-7 rounded-xl text-xs tracking-wider uppercase font-mono flex items-center gap-2 cursor-not-allowed border border-zinc-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Em Breve</span>
                </span>
                <?php endif; ?>
                <?php if ($serie_trailer_url): ?>
                <a href="<?php echo esc_url(home_url('/assista?id=' . get_the_ID() . '&trailer=true')); ?>"
                   class="bg-f5-blue-900 border border-zinc-800 hover:bg-f5-blue-800 hover:border-zinc-700 text-white font-bold py-3.5 px-6 rounded-xl text-xs tracking-wider uppercase font-mono flex items-center justify-center gap-2 cursor-pointer transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001-1 1z"/></svg>
                    <span>Assistir Trailer</span>
                </a>
                <?php endif; ?>
                <?php f5tv_render_minha_lista_button(get_the_ID()); ?>
            </div>

            <!-- SEASONS & EPISODES ACCORDION — idêntico ao ContentDetailsPage.tsx episodes panel -->
            <?php if (empty($all_seasons)): ?>
                <div class="bg-f5-blue-950/30 p-10 text-center rounded-xl border border-dashed border-zinc-900 flex flex-col items-center gap-3">
                    <svg class="w-8 h-8 text-f5-red opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>
                    <p class="text-zinc-500 text-xs font-mono">Nenhuma temporada cadastrada ainda.</p>
                    <p class="text-zinc-600 text-[10px] font-mono">Acesse o painel admin → Séries → Adicionar Temporada para criar conteúdo.</p>
                </div>
            <?php else: ?>
                <div class="border-t border-zinc-900 pt-8 mt-4 flex flex-col gap-4">
                    <h3 class="text-zinc-100 font-bold text-base flex items-center gap-2">
                        <svg class="w-5 h-5 text-f5-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>
                        <span>Temporadas &amp; Episódios</span>
                    </h3>

                    <?php foreach ($all_seasons as $s_idx => $season):
                        $s_num = f5tv_get_field('number', $season->ID) ?: ($s_idx + 1);
                        $episodes = get_posts([
                            'post_type'      => 'f5tv_episodio',
                            'posts_per_page' => -1,
                            'meta_query'     => [[
                                'key'     => 'season_id',
                                'value'   => $season->ID,
                                'compare' => '=',
                            ]],
                            'orderby'  => 'meta_value_num',
                            'meta_key' => 'number',
                            'order'    => 'ASC',
                        ]);
                    ?>
                        <details class="group" <?php echo $s_idx === 0 ? 'open' : ''; ?>>
                            <summary class="flex items-center justify-between cursor-pointer p-4 bg-f5-blue-950/30 border border-zinc-900 rounded-xl hover:border-zinc-800 transition list-none">
                                <div class="flex items-center gap-3">
                                    <span class="text-zinc-100 font-bold text-sm"><?php echo esc_html($season->post_title ?: 'Temporada ' . $s_num); ?></span>
                                    <span class="text-[10px] font-mono text-zinc-500"><?php echo count($episodes); ?> ep.</span>
                                </div>
                                <svg class="w-4 h-4 text-zinc-500 group-open:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </summary>

                            <?php if (empty($episodes)): ?>
                                <p class="text-xs text-zinc-500 font-mono italic px-4 py-3">Sem episódios disponíveis.</p>
                            <?php else: ?>
                                <div class="flex flex-col gap-3 mt-3 max-h-96 overflow-y-auto pr-1">
                                    <?php foreach ($episodes as $ep):
                                        $ep_num   = f5tv_get_field('number', $ep->ID) ?: '1';
                                        $ep_dur   = f5tv_get_field('duration', $ep->ID) ?: '';
                                        $ep_video = f5tv_get_field('video_url', $ep->ID);
                                        $ep_thumb = f5tv_get_field('thumbnail_url', $ep->ID) ?: get_the_post_thumbnail_url($ep->ID, 'medium') ?: $banner_url;
                                        $ep_desc  = get_post_field('post_excerpt', $ep->ID) ?: f5tv_get_field('short_description', $ep->ID) ?: '';
                                        $ep_link  = $ep_video ? home_url('/assista?id=' . $ep->ID) : '#';
                                    ?>
                                        <a href="<?php echo esc_url($ep_link); ?>"
                                           class="bg-f5-blue-950/30 hover:bg-f5-blue-950 border border-zinc-900 hover:border-zinc-800 p-4 rounded-xl flex gap-4 items-center cursor-pointer group transition duration-200">
                                            <div class="w-24 md:w-32 aspect-video bg-f5-blue-900 rounded-lg overflow-hidden shrink-0 relative">
                                                <?php if ($ep_thumb): ?>
                                                    <img src="<?php echo esc_url($ep_thumb); ?>"
                                                         alt="<?php echo esc_attr($ep->post_title); ?>"
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
                        <span class="text-zinc-250 font-bold text-xs"><?php echo esc_html(implode(', ', $directors)); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($cast)): ?>
                    <div class="flex flex-col gap-1.5">
                        <span class="text-zinc-500 font-bold uppercase tracking-wider text-[10px] flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-f5-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <span>Elenco / Apresentação</span>
                        </span>
                        <span class="text-zinc-250 font-bold text-xs leading-relaxed"><?php echo esc_html(implode(', ', $cast)); ?></span>
                    </div>
                <?php endif; ?>

                <div class="flex flex-col gap-1.5">
                    <span class="text-zinc-500 font-bold uppercase tracking-wider text-[10px]">Popularidade F5</span>
                    <span class="text-zinc-300 font-bold text-xs"><?php echo number_format($views_count, 0, ',', '.'); ?> visualizações</span>
                </div>

                <div class="flex flex-col gap-1.5">
                    <span class="text-zinc-500 font-bold uppercase tracking-wider text-[10px]">Gênero</span>
                    <span class="text-f5-red font-bold text-xs"><?php echo esc_html($genre_name); ?></span>
                </div>

                <?php if (!empty($tags)): ?>
                    <div class="flex flex-col gap-1.5">
                        <span class="text-zinc-500 font-bold uppercase tracking-wider text-[10px]">Tags</span>
                        <div class="flex flex-wrap gap-1 mt-1 text-[9px] uppercase tracking-wider">
                            <?php foreach ($tags as $tag): ?>
                                <span class="bg-f5-blue-900 text-zinc-500 border border-zinc-850 px-2 py-0.5 rounded"><?php echo esc_html($tag); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Related series — idêntico ao "Você Também Pode Curtir" -->
            <?php if (!empty($related_series)): ?>
                <div class="flex flex-col gap-4">
                    <h3 class="text-xs font-mono font-black uppercase text-zinc-500 tracking-wider">Você Também Pode Curtir</h3>
                    <div class="flex flex-col gap-3">
                        <?php foreach ($related_series as $rel):
                            $rel_banner = f5tv_get_field('banner_url', $rel->ID) ?: get_the_post_thumbnail_url($rel->ID, 'medium') ?: '';
                            $rel_terms  = get_the_terms($rel->ID, 'f5tv_genero');
                            $rel_genre  = ($rel_terms && !is_wp_error($rel_terms)) ? $rel_terms[0]->name : '';
                        ?>
                            <a href="<?php echo esc_url(get_permalink($rel->ID)); ?>"
                               class="flex gap-3 bg-f5-blue-950 p-2 rounded-xl border border-zinc-900 hover:border-zinc-800 transition cursor-pointer group">
                               <div class="w-12 h-16 rounded bg-f5-blue-900 overflow-hidden shrink-0">
                                    <?php if ($rel_banner): ?>
                                        <img src="<?php echo esc_url($rel_banner); ?>" alt="<?php echo esc_attr($rel->post_title); ?>"
                                             class="w-full h-full object-cover group-hover:opacity-100 opacity-70 transition">
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1 flex flex-col justify-center text-left">
                                    <?php if ($rel_genre): ?>
                                        <span class="text-[9px] font-mono font-bold text-f5-red uppercase tracking-wider"><?php echo esc_html($rel_genre); ?></span>
                                    <?php endif; ?>
                                    <h4 class="font-bold text-xs text-white/90 group-hover:text-white line-clamp-1 mt-0.5"><?php echo esc_html($rel->post_title); ?></h4>
                                    <span class="text-[10px] text-zinc-500 mt-1 font-mono">SÉRIE F5 TV</span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- REVIEWS SECTION — idêntico ao ContentDetailsPage.tsx -->
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
                        <form method="post" action="<?php echo esc_url(site_url('/wp-comments-post.php')); ?>" class="flex flex-col gap-4">
                            <?php wp_nonce_field('add-comment'); ?>
                            <input type="hidden" name="comment_post_ID" value="<?php echo get_the_ID(); ?>">
                            <input type="hidden" name="redirect_to" value="<?php echo esc_url(get_permalink()); ?>">
                            <div class="flex flex-col gap-1.5">
                                <span class="text-xs font-semibold text-zinc-400">Quantas estrelas esta série merece?</span>
                                <div class="flex gap-1.5 mt-0.5" id="f5tv-star-rating-serie">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <button type="button" data-star="<?php echo $i; ?>"
                                                class="cursor-pointer focus:outline-none transition hover:scale-110 text-zinc-700 hover:text-yellow-400 text-2xl star-btn-serie">★</button>
                                    <?php endfor; ?>
                                </div>
                                <input type="hidden" name="f5tv_rating" id="f5tv-rating-serie" value="5">
                            </div>
                            <textarea name="comment" rows="3"
                                      placeholder="Diga à comunidade o que achou desta série F5 TV..."
                                      class="w-full bg-[#030303] border border-white/5 focus:border-f5-red rounded-lg p-3 text-xs text-zinc-100 placeholder:text-zinc-600 outline-none transition resize-none font-medium leading-relaxed"
                                      required></textarea>
                            <button type="submit"
                                    class="w-full bg-f5-red hover:bg-f5-red-700 text-white font-bold py-2.5 rounded-lg text-xs uppercase tracking-wider font-mono cursor-pointer transition">
                                Publicar Opinião
                            </button>
                        </form>
                        <script>
                        (function(){
                            var btns = document.querySelectorAll('.star-btn-serie');
                            var inp = document.getElementById('f5tv-rating-serie');
                            var selected = 5;
                            function paint(n) { btns.forEach(function(b,i){ b.style.color = i < n ? '#facc15' : '#3f3f46'; }); }
                            paint(selected);
                            btns.forEach(function(b, i) {
                                b.addEventListener('mouseover', function(){ paint(i+1); });
                                b.addEventListener('mouseleave', function(){ paint(selected); });
                                b.addEventListener('click', function(){ selected = i+1; inp.value = selected; paint(selected); });
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
                            <p>Seja o primeiro a avaliar esta série F5 TV!</p>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </section>

</div>

<?php endwhile; ?>

<?php get_footer(); ?>
