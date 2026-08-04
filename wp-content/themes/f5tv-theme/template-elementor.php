<?php
/**
 * Template Name: Elementor Largura Total (Full Width)
 * Template Post Type: page, post, f5tv_conteudo, f5tv_serie
 * Description: Modelo oficial para edição total com Elementor preservando cabeçalho e rodapé da F5 TV.
 */

get_header();
?>

<div id="elementor-full-width-page" class="min-h-screen bg-f5-blue text-white font-sans w-full">
    <?php
    while (have_posts()): the_post();
        the_content();
    endwhile;
    ?>
</div>

<?php get_footer(); ?>
