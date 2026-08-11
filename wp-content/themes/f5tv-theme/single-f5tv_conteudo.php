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
    $trailer_url      = f5tv_get_field('trailer_url') ?: get_post_meta(get_the_ID(), 'trailer_url', true);
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
    $related_ids = array_values(array_filter(array_map('absint', (array) get_post_meta(get_the_ID(), 'related_content_ids', true))));
    $related_posts = $related_ids ? get_posts([
        'post_type' => 'f5tv_conteudo',
        'post__in' => $related_ids,
        'post__not_in' => [get_the_ID()],
        'posts_per_page' => 6,
        'post_status' => 'publish',
        'orderby' => 'post__in',
    ]) : get_posts([
        'post_type'      => 'f5tv_conteudo',
        'posts_per_page' => 6,
        'post_status'    => 'publish',
        'post__not_in'   => [get_the_ID()],
        'tax_query'      => $cat_id ? [[
            'taxonomy' => 'f5tv_categoria',
            'field'    => 'term_id',
            'terms'    => $cat_id,
        ]] : [],
    ]);

    // Series / Seasons / Episodes
    $linked_series_id = absint(get_post_meta(get_the_ID(), 'series_id', true));
    $series_posts = $linked_series_id ? [get_post($linked_series_id)] : get_posts([
        'post_type'      => 'f5tv_serie',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
        's'              => get_the_title(),
    ]);
    $series_posts = array_values(array_filter($series_posts));
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
    $program_episodes = get_posts([
        'post_type' => 'f5tv_episodio',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => [['key' => 'content_id', 'value' => get_the_ID(), 'compare' => '=']],
        'meta_key' => 'number',
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
    ]);
    if (empty($program_episodes)) {
        $program_episodes = get_posts([
            'post_type' => 'f5tv_episodio',
            'post_status' => ['publish', 'private'],
            'posts_per_page' => -1,
            'post_parent' => get_the_ID(),
            'orderby' => 'menu_order',
            'order' => 'ASC',
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

    $watch_url         = home_url('/assista?id=' . get_the_ID());
    $watch_cta_url     = is_user_logged_in()
        ? $watch_url
        : add_query_arg('redirect_to', $watch_url, home_url('/login/'));
    $trailer_watch_url = $trailer_url ? home_url('/assista?id=' . get_the_ID() . '&trailer=true') : '';
    $trailer_admin_url = current_user_can('manage_options')
        ? admin_url('admin.php?page=f5tv-content-studio&action=edit&id=' . get_the_ID() . '#f5_trailer_url')
        : '';
    $content_type_labels = [
        'programa'    => 'Programa',
        'tv_show'     => 'Programa',
        'documentary' => 'Documentário',
        'documentario'=> 'Documentário',
        'movie'       => 'Programa',
        'video'       => 'Programa',
    ];
    $content_type_label = $content_type_labels[$content_type] ?? 'Programa';
    $hero_description = wp_trim_words(
        wp_strip_all_tags($short_description ?: $full_description),
        34,
        '...'
    );
    $episode_total = count($program_episodes);
    if ($episode_total === 0 && $has_series && !empty($all_seasons)) {
        foreach ($all_seasons as $hero_season) {
            $episode_total += (int) count(get_posts([
                'post_type'      => 'f5tv_episodio',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'fields'         => 'ids',
                'meta_query'     => [[
                    'key'     => 'season_id',
                    'value'   => $hero_season->ID,
                    'compare' => '=',
                ]],
            ]));
        }
    }
?>

<div id="content-details-page" class="min-h-screen bg-f5-blue text-zinc-300 font-sans animate-fade-in selection:bg-f5-red selection:text-white">

    <!-- Apresentação cinematográfica com informações e ações acima da dobra. -->
    <section class="f5tv-cinematic-hero relative bg-black overflow-hidden select-none mb-10">
        <?php if ($banner_url): ?>
            <div class="f5tv-cinematic-hero__image absolute inset-0 bg-cover" style="background-image: url('<?php echo esc_url($banner_url); ?>');"></div>
        <?php endif; ?>
        <div class="f5tv-cinematic-hero__shade absolute inset-0"></div>

        <div class="f5tv-cinematic-hero__content relative z-10 max-w-7xl mx-auto w-full px-6 md:px-8">
            <a href="<?php echo esc_url(home_url('/catalogo/')); ?>" class="f5tv-cinematic-hero__back inline-flex items-center gap-2">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15.5 5l-7 7 7 7"/></svg>
                <span>Voltar aos programas</span>
            </a>

            <div class="f5tv-cinematic-hero__copy">
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    <span class="f5tv-cinematic-hero__eyebrow">F5 Original</span>
                    <span class="f5tv-cinematic-hero__category"><?php echo esc_html($content_type_label); ?></span>
                </div>

                <h1><?php the_title(); ?></h1>

                <div class="f5tv-cinematic-hero__metadata" aria-label="Informações do programa">
                    <?php if ($year): ?><span><?php echo esc_html($year); ?></span><?php endif; ?>
                    <?php if ($duration): ?><span><?php echo esc_html($duration); ?></span><?php endif; ?>
                    <span class="is-rating"><?php echo esc_html($age_rating); ?></span>
                    <?php if ($genre_name): ?><span><?php echo esc_html($genre_name); ?></span><?php endif; ?>
                    <?php if ($episode_total > 0): ?><span><?php echo esc_html($episode_total); ?> episódios</span><?php endif; ?>
                    <span class="is-access">Grátis com login</span>
                </div>

                <?php if ($hero_description): ?>
                    <p class="f5tv-cinematic-hero__description"><?php echo esc_html($hero_description); ?></p>
                <?php endif; ?>

                <div class="f5tv-cinematic-hero__actions">
                    <?php if ($video_url): ?>
                        <a href="<?php echo esc_url($watch_cta_url); ?>" class="f5tv-cinematic-hero__button is-primary">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
                            <span><?php echo is_user_logged_in() ? 'Assistir agora' : 'Entrar grátis para assistir'; ?></span>
                        </a>
                    <?php endif; ?>

                    <?php if ($trailer_url): ?>
                        <a href="<?php echo esc_url($trailer_watch_url); ?>" class="f5tv-cinematic-hero__button is-secondary">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
                            <span>Assistir trailer</span>
                        </a>
                    <?php elseif ($trailer_admin_url): ?>
                        <a href="<?php echo esc_url($trailer_admin_url); ?>" class="f5tv-cinematic-hero__button is-secondary">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M11 4h2v7h7v2h-7v7h-2v-7H4v-2h7V4z"/></svg>
                            <span>Cadastrar trailer</span>
                        </a>
                    <?php else: ?>
                        <span class="f5tv-cinematic-hero__button is-secondary is-disabled" aria-disabled="true">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
                            <span>Trailer em breve</span>
                        </span>
                    <?php endif; ?>

                    <a href="#sobre-programa" class="f5tv-cinematic-hero__info" aria-label="Ver mais informações">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 10v7M12 7h.01"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Prévia editorial: ações reais substituem controles decorativos. -->
    <section class="f5tv-detail-player-section max-w-7xl mx-auto px-6 md:px-8 mb-10">
        <div class="f5tv-detail-player relative aspect-video max-w-5xl mx-auto overflow-hidden bg-black group" id="f5tv-details-player" aria-label="Prévia de <?php echo esc_attr(get_the_title()); ?>">
            <?php if ($banner_url || $cover_url): ?>
                <img src="<?php echo esc_url($banner_url ?: $cover_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="f5tv-detail-player__image absolute inset-0 w-full h-full object-cover">
            <?php endif; ?>
            <div class="f5tv-detail-player__shade absolute inset-0"></div>

            <div class="f5tv-detail-player__top absolute inset-x-0 top-0 flex items-center justify-between gap-3">
                <span class="f5tv-detail-player__brand"><strong>F5</strong> TV</span>
                <div class="flex items-center gap-2">
                    <span class="f5tv-detail-player__type"><?php echo esc_html($content_type_label); ?></span>
                    <span class="f5tv-detail-player__trailer-status<?php echo $trailer_url ? '' : ' is-pending'; ?>">
                        <i></i> <?php echo $trailer_url ? 'Trailer disponível' : 'Trailer em breve'; ?>
                    </span>
                </div>
            </div>

            <?php if ($video_url): ?>
                <a href="<?php echo esc_url($watch_cta_url); ?>" class="f5tv-detail-player__main-action absolute inset-0 flex items-center justify-center" aria-label="Assistir <?php echo esc_attr(get_the_title()); ?>">
                    <span class="f5tv-detail-player__play">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
                    </span>
                    <span class="f5tv-detail-player__play-label"><?php echo is_user_logged_in() ? 'Assistir agora' : 'Entrar grátis para assistir'; ?></span>
                </a>
            <?php endif; ?>

            <div class="f5tv-detail-player__footer absolute inset-x-0 bottom-0 flex items-end justify-between gap-5">
                <div class="f5tv-detail-player__copy min-w-0">
                    <span><?php echo esc_html($content_type_label); ?> F5 TV</span>
                    <h2><?php the_title(); ?></h2>
                    <p>
                        <?php if ($duration): ?><b><?php echo esc_html($duration); ?></b><?php endif; ?>
                        <b><?php echo esc_html($age_rating); ?></b>
                        <b>Acesso gratuito</b>
                    </p>
                </div>
                <div class="f5tv-detail-player__actions flex items-center gap-2 shrink-0">
                    <?php if ($trailer_url): ?>
                        <a href="<?php echo esc_url($trailer_watch_url); ?>" class="f5tv-detail-player__button f5tv-detail-player__button--trailer" aria-label="Assistir trailer de <?php echo esc_attr(get_the_title()); ?>">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
                            <span>Assistir trailer</span>
                        </a>
                    <?php elseif ($trailer_admin_url): ?>
                        <a href="<?php echo esc_url($trailer_admin_url); ?>" class="f5tv-detail-player__button f5tv-detail-player__button--trailer is-pending" title="Cadastrar o trailer deste conteúdo">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M11 4h2v7h7v2h-7v7h-2v-7H4v-2h7V4z"/></svg>
                            <span>Cadastrar trailer</span>
                        </a>
                    <?php else: ?>
                        <span class="f5tv-detail-player__button f5tv-detail-player__button--trailer is-pending" aria-disabled="true" title="Trailer ainda não cadastrado">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
                            <span>Trailer em breve</span>
                        </span>
                    <?php endif; ?>
                    <?php if ($video_url): ?>
                        <a href="<?php echo esc_url($watch_cta_url); ?>" class="f5tv-detail-player__button f5tv-detail-player__button--watch">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
                            <span>Assistir</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="f5tv-detail-player__hint max-w-5xl mx-auto flex items-center justify-between gap-4">
            <span class="flex items-center gap-2">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 1a5 5 0 00-5 5v3H6a2 2 0 00-2 2v10h16V11a2 2 0 00-2-2h-1V6a5 5 0 00-5-5zm-3 8V6a3 3 0 116 0v3H9z"/></svg>
                Catálogo aberto. É necessário entrar somente para assistir ao conteúdo completo.
            </span>
            <?php if ($trailer_url): ?>
                <a href="<?php echo esc_url($trailer_watch_url); ?>">Trailer livre, sem login</a>
            <?php elseif ($trailer_admin_url): ?>
                <a href="<?php echo esc_url($trailer_admin_url); ?>">Cadastrar trailer</a>
            <?php endif; ?>
        </div>
    </section>

    <style>
    .f5tv-cinematic-hero { min-height: clamp(570px, 76vh, 780px); display: flex; align-items: stretch; border-bottom: 1px solid rgba(255,255,255,.07); }
    .f5tv-cinematic-hero__image { background-position: center 28%; transform: scale(1.015); }
    .f5tv-cinematic-hero__shade { background: linear-gradient(90deg, rgba(2,8,21,.99) 0%, rgba(2,8,21,.9) 32%, rgba(2,8,21,.26) 68%, rgba(2,8,21,.12) 100%), linear-gradient(0deg, #061831 0%, rgba(6,24,49,.5) 20%, transparent 58%), linear-gradient(180deg, rgba(2,8,21,.52), transparent 25%); }
    .f5tv-cinematic-hero__content { display: flex; flex-direction: column; justify-content: center; padding-top: 66px; padding-bottom: 72px; }
    .f5tv-cinematic-hero__back { position: absolute; top: 27px; color: rgba(255,255,255,.62); font: 800 10px/1 monospace; letter-spacing: .09em; text-transform: uppercase; transition: color .2s ease; }
    .f5tv-cinematic-hero__back:hover { color: #fff; }
    .f5tv-cinematic-hero__back svg { width: 17px; fill: none; stroke: currentColor; stroke-width: 2; }
    .f5tv-cinematic-hero__copy { width: min(680px, 58vw); }
    .f5tv-cinematic-hero__eyebrow, .f5tv-cinematic-hero__category { display: inline-flex; min-height: 27px; align-items: center; padding: 0 9px; border-radius: 4px; font: 900 9px/1 monospace; letter-spacing: .12em; text-transform: uppercase; }
    .f5tv-cinematic-hero__eyebrow { background: #e50914; color: #fff; }
    .f5tv-cinematic-hero__category { border: 1px solid rgba(255,255,255,.2); background: rgba(4,12,26,.5); color: rgba(255,255,255,.75); backdrop-filter: blur(8px); }
    .f5tv-cinematic-hero h1 { max-width: 760px; margin: 0; color: #fff; font-size: clamp(42px, 6vw, 86px); font-weight: 950; letter-spacing: -.055em; line-height: .91; text-wrap: balance; text-shadow: 0 6px 36px rgba(0,0,0,.52); }
    .f5tv-cinematic-hero__metadata { display: flex; flex-wrap: wrap; align-items: center; gap: 8px 14px; margin-top: 24px; color: rgba(255,255,255,.82); font: 800 11px/1 monospace; }
    .f5tv-cinematic-hero__metadata span { position: relative; }
    .f5tv-cinematic-hero__metadata span + span::before { content: ""; display: inline-block; width: 3px; height: 3px; margin: 0 12px 2px 0; border-radius: 50%; background: rgba(255,255,255,.38); }
    .f5tv-cinematic-hero__metadata .is-rating { padding: 3px 6px; border: 1px solid rgba(255,255,255,.42); border-radius: 3px; }
    .f5tv-cinematic-hero__metadata .is-rating::before, .f5tv-cinematic-hero__metadata .is-access::before { display: none; }
    .f5tv-cinematic-hero__metadata .is-access { color: #48db8b; }
    .f5tv-cinematic-hero__description { max-width: 620px; margin: 22px 0 0; color: rgba(244,247,252,.84); font-size: clamp(14px, 1.25vw, 18px); font-weight: 600; line-height: 1.62; text-shadow: 0 2px 14px rgba(0,0,0,.85); }
    .f5tv-cinematic-hero__actions { display: flex; flex-wrap: wrap; align-items: center; gap: 11px; margin-top: 27px; }
    .f5tv-cinematic-hero__button { display: inline-flex; min-height: 50px; align-items: center; justify-content: center; gap: 10px; padding: 0 21px; border: 1px solid transparent; border-radius: 8px; font: 900 11px/1 monospace; letter-spacing: .04em; text-transform: uppercase; transition: transform .2s ease, background .2s ease, border-color .2s ease; }
    .f5tv-cinematic-hero__button:hover { transform: translateY(-2px); }
    .f5tv-cinematic-hero__button svg { width: 20px; fill: currentColor; }
    .f5tv-cinematic-hero__button.is-primary { background: #fff; color: #071326; box-shadow: 0 12px 35px rgba(0,0,0,.26); }
    .f5tv-cinematic-hero__button.is-primary:hover { background: #e50914; color: #fff; }
    .f5tv-cinematic-hero__button.is-secondary { border-color: rgba(255,255,255,.26); background: rgba(31,42,60,.76); color: #fff; backdrop-filter: blur(12px); }
    .f5tv-cinematic-hero__button.is-secondary:hover { background: rgba(50,63,84,.95); border-color: rgba(255,255,255,.5); }
    .f5tv-cinematic-hero__button.is-disabled { color: rgba(255,255,255,.48); cursor: not-allowed; }
    .f5tv-cinematic-hero__button.is-disabled:hover { transform: none; }
    .f5tv-cinematic-hero__info { display: grid; width: 50px; height: 50px; place-items: center; border: 1px solid rgba(255,255,255,.25); border-radius: 50%; background: rgba(4,12,26,.55); color: #fff; backdrop-filter: blur(10px); }
    .f5tv-cinematic-hero__info svg { width: 23px; fill: none; stroke: currentColor; stroke-width: 1.8; }
    .f5tv-detail-player { border: 1px solid rgba(255,255,255,.14); border-radius: 24px; box-shadow: 0 32px 90px rgba(0,0,0,.48), 0 0 0 1px rgba(229,9,20,.08); isolation: isolate; }
    .f5tv-detail-player::after { content: ""; position: absolute; inset: 0; z-index: 1; border-radius: inherit; box-shadow: inset 0 0 0 1px rgba(255,255,255,.04); pointer-events: none; }
    .f5tv-detail-player__image { opacity: .82; transform: scale(1.005); transition: opacity .5s ease, transform 1.2s ease; }
    .f5tv-detail-player:hover .f5tv-detail-player__image { opacity: .93; transform: scale(1.025); }
    .f5tv-detail-player__shade { z-index: 1; background: linear-gradient(180deg, rgba(2,7,18,.68) 0%, rgba(2,7,18,.05) 34%, rgba(2,7,18,.08) 48%, rgba(2,7,18,.96) 100%), linear-gradient(90deg, rgba(2,7,18,.32), transparent 55%); }
    .f5tv-detail-player__top { z-index: 4; padding: clamp(14px, 2.3vw, 28px); }
    .f5tv-detail-player__brand, .f5tv-detail-player__type, .f5tv-detail-player__trailer-status { display: inline-flex; align-items: center; min-height: 30px; padding: 0 11px; border: 1px solid rgba(255,255,255,.16); border-radius: 999px; background: rgba(2,7,18,.72); backdrop-filter: blur(12px); color: #fff; font: 800 10px/1 monospace; letter-spacing: .1em; text-transform: uppercase; }
    .f5tv-detail-player__brand strong { margin-right: 4px; color: #f21f2b; font-size: 14px; }
    .f5tv-detail-player__trailer-status i { width: 7px; height: 7px; margin-right: 7px; border-radius: 50%; background: #f21f2b; box-shadow: 0 0 0 5px rgba(242,31,43,.15); }
    .f5tv-detail-player__trailer-status.is-pending { color: rgba(255,255,255,.62); }
    .f5tv-detail-player__trailer-status.is-pending i { background: #8791a3; box-shadow: none; }
    .f5tv-detail-player__main-action { z-index: 2; flex-direction: column; gap: 13px; color: #fff; }
    .f5tv-detail-player__play { display: grid; place-items: center; width: clamp(68px, 8vw, 94px); height: clamp(68px, 8vw, 94px); border: 1px solid rgba(255,255,255,.65); border-radius: 50%; background: linear-gradient(145deg, #f5222d, #c80712); box-shadow: 0 18px 45px rgba(229,9,20,.42), 0 0 0 10px rgba(255,255,255,.08); transition: transform .2s ease, box-shadow .2s ease; }
    .f5tv-detail-player__play svg { width: 42%; fill: #fff; margin-left: 5px; }
    .f5tv-detail-player__main-action:hover .f5tv-detail-player__play { transform: scale(1.08); box-shadow: 0 22px 60px rgba(229,9,20,.55), 0 0 0 14px rgba(255,255,255,.1); }
    .f5tv-detail-player__play-label { padding: 8px 13px; border-radius: 999px; background: rgba(2,7,18,.76); color: #fff; font: 800 11px/1 monospace; letter-spacing: .08em; text-transform: uppercase; backdrop-filter: blur(10px); }
    .f5tv-detail-player__footer { z-index: 5; padding: clamp(18px, 2.7vw, 34px); pointer-events: none; }
    .f5tv-detail-player__copy span { color: #ff3340; font: 900 10px/1 monospace; letter-spacing: .14em; text-transform: uppercase; }
    .f5tv-detail-player__copy h2 { max-width: 580px; margin: 7px 0 9px; overflow: hidden; color: #fff; font-size: clamp(18px, 2.2vw, 30px); font-weight: 900; line-height: 1.05; text-overflow: ellipsis; white-space: nowrap; text-shadow: 0 3px 18px #000; }
    .f5tv-detail-player__copy p { display: flex; flex-wrap: wrap; gap: 7px; margin: 0; }
    .f5tv-detail-player__copy b { padding: 4px 7px; border: 1px solid rgba(255,255,255,.13); border-radius: 5px; background: rgba(8,15,30,.7); color: rgba(255,255,255,.76); font: 700 9px/1 monospace; text-transform: uppercase; }
    .f5tv-detail-player__actions { pointer-events: auto; }
    .f5tv-detail-player__button { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 44px; padding: 0 16px; border-radius: 10px; color: #fff; font: 900 10px/1 monospace; letter-spacing: .06em; text-transform: uppercase; transition: transform .2s ease, background .2s ease, border-color .2s ease; }
    .f5tv-detail-player__button:hover { transform: translateY(-2px); }
    .f5tv-detail-player__button svg { width: 16px; fill: currentColor; }
    .f5tv-detail-player__button--trailer { border: 1px solid rgba(255,255,255,.28); background: rgba(8,15,30,.8); backdrop-filter: blur(10px); }
    .f5tv-detail-player__button--trailer:hover { background: rgba(25,35,55,.95); border-color: rgba(255,255,255,.5); }
    .f5tv-detail-player__button--trailer.is-pending { border-color: rgba(255,255,255,.14); color: rgba(255,255,255,.58); background: rgba(8,15,30,.66); }
    span.f5tv-detail-player__button--trailer.is-pending { cursor: not-allowed; }
    .f5tv-detail-player__button--watch { border: 1px solid #f21f2b; background: #e50914; }
    .f5tv-detail-player__button--watch:hover { background: #fa1c28; }
    .f5tv-detail-player__hint { padding: 13px 6px 0; color: #7d879b; font: 700 10px/1.5 monospace; }
    .f5tv-detail-player__hint span svg { width: 14px; flex: 0 0 auto; fill: currentColor; }
    .f5tv-detail-player__hint a { color: #ff3340; font-weight: 900; text-transform: uppercase; white-space: nowrap; }
    @media (max-width: 700px) {
        .f5tv-cinematic-hero { min-height: 620px; }
        .f5tv-cinematic-hero__image { background-position: 66% top; }
        .f5tv-cinematic-hero__shade { background: linear-gradient(0deg, #061831 4%, rgba(6,24,49,.91) 35%, rgba(2,8,21,.2) 73%), linear-gradient(90deg, rgba(2,8,21,.4), rgba(2,8,21,.06)); }
        .f5tv-cinematic-hero__content { justify-content: flex-end; padding-top: 70px; padding-bottom: 46px; }
        .f5tv-cinematic-hero__copy { width: 100%; }
        .f5tv-cinematic-hero h1 { font-size: clamp(36px, 12vw, 54px); }
        .f5tv-cinematic-hero__metadata { margin-top: 17px; gap: 8px 10px; }
        .f5tv-cinematic-hero__metadata span + span::before { margin-right: 8px; }
        .f5tv-cinematic-hero__description { display: -webkit-box; overflow: hidden; font-size: 14px; -webkit-box-orient: vertical; -webkit-line-clamp: 3; }
        .f5tv-cinematic-hero__actions { flex-wrap: nowrap; }
        .f5tv-cinematic-hero__button { min-height: 48px; padding: 0 15px; font-size: 9px; }
        .f5tv-cinematic-hero__info { display: none; }
        .f5tv-detail-player-section { padding-left: 14px; padding-right: 14px; }
        .f5tv-detail-player { min-height: 300px; border-radius: 16px; }
        .f5tv-detail-player__top { align-items: flex-start; }
        .f5tv-detail-player__trailer-status { display: none; }
        .f5tv-detail-player__main-action { padding-bottom: 48px; }
        .f5tv-detail-player__footer { align-items: flex-end; }
        .f5tv-detail-player__copy p, .f5tv-detail-player__button--watch { display: none; }
        .f5tv-detail-player__button { min-height: 42px; padding: 0 12px; }
        .f5tv-detail-player__button span { display: none; }
        .f5tv-detail-player__button svg { width: 20px; }
        .f5tv-detail-player__hint { align-items: flex-start; flex-direction: column; }
    }
    </style>

    <!-- Core Split Grid — lg:grid-cols-3 — idêntico ao ContentDetailsPage.tsx -->
    <div id="sobre-programa" class="max-w-7xl mx-auto px-6 md:px-8 grid grid-cols-1 lg:grid-cols-3 gap-12 mb-16 scroll-mt-24">

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
                    <a href="<?php echo esc_url($watch_cta_url); ?>"
                       class="bg-f5-red hover:bg-f5-red-700 text-white font-bold py-3.5 px-7 rounded-xl text-xs tracking-wider uppercase font-mono flex items-center justify-center gap-2 cursor-pointer transition shadow-lg shadow-f5-red-700/10">
                        <svg class="w-4 h-4 fill-white" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        <span><?php echo is_user_logged_in() ? ($has_series ? 'Assistir Ep. 1' : 'Assistir Agora') : 'Entrar grátis para assistir'; ?></span>
                    </a>
                <?php endif; ?>

                <?php if ($trailer_url): ?>
                    <a href="<?php echo esc_url($trailer_watch_url); ?>"
                       class="bg-f5-blue-900 border border-zinc-800 hover:bg-f5-blue-800 hover:border-zinc-700 text-white font-bold py-3.5 px-6 rounded-xl text-xs tracking-wider uppercase font-mono flex items-center justify-center gap-2 cursor-pointer transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>
                        <span>Assistir Trailer</span>
                    </a>
                <?php elseif ($trailer_admin_url): ?>
                    <a href="<?php echo esc_url($trailer_admin_url); ?>"
                       class="bg-f5-blue-900 border border-zinc-800 hover:bg-f5-blue-800 hover:border-zinc-700 text-white font-bold py-3.5 px-6 rounded-xl text-xs tracking-wider uppercase font-mono flex items-center justify-center gap-2 cursor-pointer transition">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M11 4h2v7h7v2h-7v7h-2v-7H4v-2h7V4z"/></svg>
                        <span>Cadastrar Trailer</span>
                    </a>
                <?php else: ?>
                    <span class="bg-f5-blue-950 border border-zinc-900 text-zinc-500 font-bold py-3.5 px-6 rounded-xl text-xs tracking-wider uppercase font-mono flex items-center justify-center gap-2 cursor-not-allowed" aria-disabled="true" title="Trailer ainda não cadastrado">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        <span>Trailer em breve</span>
                    </span>
                <?php endif; ?>

                <?php f5tv_render_minha_lista_button(get_the_ID()); ?>
            </div>

            <?php if (!empty($program_episodes)): ?>
                <div class="border-t border-zinc-900 pt-8 mt-4">
                    <div class="flex items-center justify-between mb-4"><h3 class="text-xs font-mono font-black uppercase text-zinc-500 tracking-wider">Episódios do Programa</h3><span class="text-[10px] text-zinc-600 font-mono"><?php echo count($program_episodes); ?> episódios</span></div>
                    <div class="flex flex-col gap-3">
                        <?php foreach ($program_episodes as $ep): $ep_video = f5tv_get_field('video_url', $ep->ID); $ep_thumb = f5tv_get_field('thumbnail_url', $ep->ID) ?: get_the_post_thumbnail_url($ep->ID, 'medium'); ?>
                            <a href="<?php echo esc_url($ep_video ? home_url('/assista?id=' . get_the_ID() . '&episodeId=' . $ep->ID) : '#'); ?>" class="bg-f5-blue-950/40 hover:bg-f5-blue-950 border border-zinc-900 p-3 rounded-xl flex gap-3 items-center group transition">
                                <div class="w-24 md:w-32 aspect-video rounded-lg bg-f5-blue-900 overflow-hidden shrink-0 relative"><?php if ($ep_thumb): ?><img src="<?php echo esc_url($ep_thumb); ?>" alt="<?php echo esc_attr($ep->post_title); ?>" class="w-full h-full object-cover opacity-75 group-hover:opacity-100 transition"><?php endif; ?><div class="absolute inset-0 flex items-center justify-center"><span class="w-8 h-8 rounded-full bg-f5-red/90 flex items-center justify-center"><svg class="w-4 h-4 fill-white ml-0.5" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></span></div></div>
                                <div class="min-w-0"><span class="text-[10px] font-mono font-bold text-f5-red uppercase">Episódio <?php echo esc_html(f5tv_get_field('number', $ep->ID) ?: 1); ?></span><h4 class="text-sm font-bold text-zinc-200 group-hover:text-white truncate mt-1"><?php echo esc_html($ep->post_title); ?></h4><span class="text-[10px] text-zinc-600 font-mono"><?php echo esc_html(f5tv_get_field('duration', $ep->ID) ?: ''); ?></span></div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

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
                                        $episode_parent_id = $linked_series_id ?: get_the_ID();
                                        $ep_link   = $ep_video ? home_url('/assista?id=' . $episode_parent_id . '&episodeId=' . $ep->ID) : '#';
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
                            $rel_cover  = f5tv_get_16x9_image($related->ID);
                            $rel_genre  = f5tv_get_field('genre', $related->ID) ?: '';
                            $rel_dur    = f5tv_get_field('duration', $related->ID) ?: '';
                        ?>
                            <a href="<?php echo esc_url(get_permalink($related->ID)); ?>"
                               class="flex gap-3 bg-f5-blue-950 p-2 rounded-xl border border-zinc-900 hover:border-zinc-800 transition cursor-pointer group">
                                <div class="w-20 md:w-24 aspect-video rounded bg-f5-blue-900 overflow-hidden shrink-0">
                                    <?php if ($rel_cover): ?>
                                        <img src="<?php echo esc_url($rel_cover); ?>" alt="<?php echo esc_attr($related->post_title); ?>"
                                             class="w-full h-full object-contain group-hover:opacity-100 opacity-70 transition">
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
