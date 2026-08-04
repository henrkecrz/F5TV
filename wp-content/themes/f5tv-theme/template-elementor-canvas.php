<?php
/**
 * Template Name: Elementor Canvas (Tela Limpa)
 * Template Post Type: page, post, f5tv_conteudo, f5tv_serie
 * Description: Modelo Elementor Canvas sem cabeçalho nem rodapé padrão.
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class('bg-f5-blue text-white font-sans antialiased'); ?>>
<?php wp_body_open(); ?>

<div id="elementor-canvas-wrapper" class="w-full min-h-screen">
    <?php
    while (have_posts()): the_post();
        the_content();
    endwhile;
    ?>
</div>

<?php wp_footer(); ?>
</body>
</html>
