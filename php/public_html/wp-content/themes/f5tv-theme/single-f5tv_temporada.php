<?php
/**
 * Single temporada template for F5TV Theme
 */

get_header();

while (have_posts()): the_post();
    $series_id = get_field('series_id');
    $number = get_field('number');
?>

<div class="f5tv-season-single">
    <div class="f5tv-season-header">
        <h1 class="f5tv-season-title"><?php the_title(); ?></h1>
        <?php if ($series_id): ?>
            <a href="<?php echo esc_url(get_permalink($series_id)); ?>" class="f5tv-season-back">
                &larr; <?php echo esc_html(get_the_title($series_id)); ?>
            </a>
        <?php endif; ?>
    </div>

    <div class="f5tv-season-description">
        <?php the_content(); ?>
    </div>

    <?php
    $episodes = get_posts([
        'post_type' => 'f5tv_episodio',
        'posts_per_page' => -1,
        'meta_key' => 'season_id',
        'meta_value' => get_the_ID(),
        'orderby' => 'meta_value_num',
        'meta_query' => [
            [
                'key' => 'number',
                'type' => 'NUMERIC',
            ],
        ],
        'order' => 'ASC',
    ]);

    if ($episodes):
    ?>
        <div class="f5tv-episodes-list">
            <?php foreach ($episodes as $episode): ?>
                <div class="f5tv-episode-card">
                    <span class="f5tv-episode-number">E<?php echo esc_html(get_field('number', $episode->ID)); ?></span>
                    <span class="f5tv-episode-title"><?php echo esc_html($episode->post_title); ?></span>
                    <span class="f5tv-episode-duration"><?php echo esc_html(get_field('duration', $episode->ID)); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php endwhile; ?>

<?php
get_footer();
