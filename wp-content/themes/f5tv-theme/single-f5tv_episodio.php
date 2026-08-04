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
?>

<div class="f5tv-episode-single">
    <div class="f5tv-episode-player">
        <?php if ($thumbnail_url): ?>
            <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php the_title(); ?>" class="f5tv-episode-thumbnail">
        <?php endif; ?>
        <?php if ($video_url): ?>
            <video controls poster="<?php echo esc_url($thumbnail_url); ?>" class="f5tv-video-player">
                <source src="<?php echo esc_url($video_url); ?>" type="application/x-mpegURL">
                Seu navegador não suporta reprodução de vídeo.
            </video>
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
