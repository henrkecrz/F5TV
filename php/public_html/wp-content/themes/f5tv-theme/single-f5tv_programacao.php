<?php
/**
 * Single programacao template for F5TV Theme
 */

get_header();

while (have_posts()): the_post();
    $channel_id = get_field('channel_id');
    $date = get_field('date');
    $start_time = get_field('start_time');
    $end_time = get_field('end_time');
    $status = get_field('status');
    $host = get_field('host');
    $is_featured = get_field('is_featured');
?>

<div class="f5tv-schedule-single">
    <div class="f5tv-schedule-header">
        <h1 class="f5tv-schedule-title"><?php the_title(); ?></h1>
        <span class="f5tv-schedule-status f5tv-status-<?php echo esc_attr($status); ?>">
            <?php echo esc_html(ucfirst($status)); ?>
        </span>
    </div>

    <div class="f5tv-schedule-meta">
        <?php if ($channel_id): ?>
            <p class="f5tv-schedule-channel">
                <a href="<?php echo esc_url(get_permalink($channel_id)); ?>">
                    <?php echo esc_html(get_the_title($channel_id)); ?>
                </a>
            </p>
        <?php endif; ?>
        <?php if ($date): ?>
            <p class="f5tv-schedule-date"><?php echo esc_html(date_i18n('d/m/Y', strtotime($date))); ?></p>
        <?php endif; ?>
        <?php if ($start_time && $end_time): ?>
            <p class="f5tv-schedule-time">
                <?php echo esc_html(date_i18n('H:i', strtotime($start_time))); ?> - <?php echo esc_html(date_i18n('H:i', strtotime($end_time))); ?>
            </p>
        <?php endif; ?>
        <?php if ($host): ?>
            <p class="f5tv-schedule-host"><?php echo esc_html($host); ?></p>
        <?php endif; ?>
    </div>

    <div class="f5tv-schedule-description">
        <?php the_content(); ?>
    </div>
</div>

<?php endwhile; ?>

<?php
get_footer();
