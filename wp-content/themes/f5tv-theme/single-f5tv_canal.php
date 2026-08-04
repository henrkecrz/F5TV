<?php
/**
 * Single channel template for F5TV Theme
 */

get_header();

while (have_posts()): the_post();
    $stream_url = get_field('stream_url');
    $logo_text = get_field('logo_text');
    $status = get_field('status');
?>

<div class="f5tv-channel-single">
    <div class="f5tv-channel-header">
        <h1 class="f5tv-channel-title"><?php the_title(); ?></h1>
        <?php if ($logo_text): ?>
            <span class="f5tv-channel-logo"><?php echo esc_html($logo_text); ?></span>
        <?php endif; ?>
        <span class="f5tv-channel-status f5tv-status-<?php echo esc_attr($status); ?>">
            <?php echo esc_html(ucfirst($status)); ?>
        </span>
    </div>

    <div class="f5tv-channel-player">
        <?php if ($stream_url): ?>
            <video controls autoplay class="f5tv-video-player f5tv-live-player">
                <source src="<?php echo esc_url($stream_url); ?>" type="application/x-mpegURL">
                Seu navegador não suporta reprodução de vídeo.
            </video>
        <?php endif; ?>
    </div>

    <div class="f5tv-channel-info">
        <div class="f5tv-channel-description">
            <?php the_content(); ?>
        </div>
    </div>
</div>

<?php endwhile; ?>

<?php
get_footer();
