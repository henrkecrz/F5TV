<?php
/**
 * Single episode template for F5TV Theme
 */

get_header();

while (have_posts()): the_post();
    $season_id = get_field('season_id');
    $number = get_field('number');
    $duration = get_field('duration');
    $video_url = get_field('video_url');
    $thumbnail_url = get_field('thumbnail_url');
    $content_id = absint(get_post_meta(get_the_ID(), 'content_id', true));
    if (!$content_id && $season_id) {
        $content_id = absint(get_post_meta($season_id, 'series_id', true));
    }
    $watch_url = home_url('/assista?id=' . $content_id . '&episodeId=' . get_the_ID());
?>

<div class="f5tv-episode-single">
    <div class="f5tv-episode-player">
        <?php if ($thumbnail_url): ?>
            <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php the_title(); ?>" class="f5tv-episode-thumbnail">
        <?php endif; ?>
        <?php if ($video_url && is_user_logged_in()): ?>
            <a href="<?php echo esc_url($watch_url); ?>" class="f5tv-button f5tv-button-primary">Assistir episódio</a>
        <?php elseif ($video_url): ?>
            <a href="<?php echo esc_url(add_query_arg('redirect_to', $watch_url, home_url('/login/'))); ?>" class="f5tv-button f5tv-button-primary">Entrar grátis para assistir</a>
        <?php endif; ?>
    </div>

    <div class="f5tv-episode-info">
        <h1 class="f5tv-episode-title">
            <?php if ($season_id): ?>
                <a href="<?php echo esc_url(get_permalink($season_id)); ?>">
                    <?php echo esc_html(get_the_title($season_id)); ?>
                </a> /
            <?php endif; ?>
            Episódio <?php echo esc_html($number); ?>
        </h1>
        <p class="f5tv-episode-description"><?php the_content(); ?></p>
        <?php if ($duration): ?>
            <span class="f5tv-episode-duration"><?php echo esc_html($duration); ?></span>
        <?php endif; ?>
    </div>
</div>

<?php endwhile; ?>

<?php
get_footer();
