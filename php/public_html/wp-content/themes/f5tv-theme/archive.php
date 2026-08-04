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
            <?php if (is_post_type_archive('f5tv_conteudo')): ?>
                <div class="f5tv-archive-filters">
                    <?php
                    $taxonomies = ['f5tv_categoria', 'f5tv_genero'];
                    foreach ($taxonomies as $tax) {
                        echo '<div class="f5tv-filter-group">';
                        echo '<label>' . esc_html(get_taxonomy($tax)->label) . '</label>';
                        echo '<select onchange="window.location.href=this.value">';
                        echo '<option value="">Todos</option>';
                        $terms = get_terms(['taxonomy' => $tax, 'hide_empty' => false]);
                        foreach ($terms as $term) {
                            printf('<option value="%s" %s>%s</option>',
                                esc_url(get_term_link($term)),
                                (get_queried_object_id() === $term->term_id) ? 'selected' : '',
                                esc_html($term->name)
                            );
                        }
                        echo '</select>';
                        echo '</div>';
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="f5tv-archive-grid">
            <?php while (have_posts()): the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('f5tv-card'); ?>>
                    <?php if (has_post_thumbnail()): ?>
                        <div class="f5tv-card-image">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('f5tv-content-cover'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="f5tv-card-content">
                        <h2 class="f5tv-card-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        <div class="f5tv-card-meta">
                            <?php echo get_the_date(); ?>
                        </div>
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
