<?php
/**
 * Single post template for F5TV Theme
 */

get_header();
?>

<div class="f5tv-page-content">
    <?php while (have_posts()): the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('f5tv-article'); ?>>
            <header class="f5tv-article-header">
                <h1 class="f5tv-article-title"><?php the_title(); ?></h1>
                <div class="f5tv-article-meta">
                    <span class="f5tv-article-date"><?php echo get_the_date(); ?></span>
                </div>
            </header>
            <div class="f5tv-article-content">
                <?php the_content(); ?>
            </div>
        </article>
    <?php endwhile; ?>
</div>

<?php
get_footer();
