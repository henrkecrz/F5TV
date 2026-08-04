<?php
/**
 * Default Page template for F5TV Theme with full Elementor support
 */

get_header();
?>

<div id="f5tv-page-wrapper" class="min-h-screen bg-f5-blue text-white font-sans w-full">
    <?php while (have_posts()): the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('w-full'); ?>>
            <div class="f5tv-article-content w-full">
                <?php the_content(); ?>
            </div>
        </article>
    <?php endwhile; ?>
</div>

<?php
get_footer();
