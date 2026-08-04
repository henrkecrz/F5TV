<?php
/**
 * Archive template for F5TV Theme
 */

get_header();
?>

<div class="f5tv-page-content">
    <?php if (have_posts()): ?>
        <div class="f5tv-archive-header">
            <h1 class="f5tv-archive-title"><?php post_type_archive_title(); ?></h1>
        </div>

        <div class="f5tv-archive-grid">
            <?php while (have_posts()): the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('f5tv-card'); ?>>
                    <?php if (has_post_thumbnail()): ?>
                        <div class="f5tv-card-image">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('f5tv-channel-logo'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="f5tv-card-content">
                        <h2 class="f5tv-card-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>

        <div class="f5tv-pagination">
            <?php the_posts_pagination(); ?>
        </div>
    <?php else: ?>
        <p class="f5tv-no-posts">Nenhum conteúdo encontrado.</p>
    <?php endif; ?>
</div>

<?php
get_footer();
