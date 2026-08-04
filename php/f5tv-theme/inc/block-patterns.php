<?php
/**
 * F5TV Theme - Block Patterns
 * Registro de padrões de blocos reutilizáveis para o Gutenberg
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', 'f5tv_register_block_patterns');
function f5tv_register_block_patterns(): void
{
    // Categoria de Padrões F5TV
    register_block_pattern_category('f5tv', [
        'label' => __('F5 TV Streaming', 'f5tv-theme'),
    ]);

    // 1. Padrão: Hero Banner
    register_block_pattern('f5tv/hero-banner', [
        'title'       => __('Hero Banner Principal', 'f5tv-theme'),
        'description' => __('Banner de destaque do streaming com gradiente F5', 'f5tv-theme'),
        'categories'  => ['f5tv', 'header'],
        'content'     => '<!-- wp:cover {"overlayColor":"f5-blue","minHeight":600,"align":"full"} -->
<div class="wp-block-cover alignfull" style="min-height:600px"><span aria-hidden="true" class="wp-block-cover__background wp-block-cover__gradient-background has-f5-blue-background-color has-background-dim-100 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"lineHeight":"1.1"}},"fontSize":"6xl"} -->
<h1 class="wp-block-heading has-text-align-center has-6-xl-font-size" style="line-height:1.1"><strong>AS MELHORES HISTÓRIAS,</strong><br>NO SEU TEMPO.</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","fontSize":"lg"} -->
<p class="has-text-align-center has-lg-font-size">Assista a produções exclusivas, jornalismo independente e transmissões ao vivo em qualquer dispositivo.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-fill"} -->
<div class="wp-block-button is-style-fill"><a class="wp-block-button__link wp-element-button">Assinar Agora</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button">Conheça os Planos</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div></div>
<!-- /wp:cover -->',
    ]);

    // 2. Padrão: Card de Planos
    register_block_pattern('f5tv/plan-card', [
        'title'       => __('Card de Planos', 'f5tv-theme'),
        'description' => __('Grade de preços e recursos para planos da F5 TV', 'f5tv-theme'),
        'categories'  => ['f5tv', 'columns'],
        'content'     => '<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"style":{"color":{"background":"var(--wp--preset--color--f5-card)"}}} -->
<div class="wp-block-column has-background" style="background-color:var(--wp--preset--color--f5-card);padding:2rem;border-radius:12px"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Básico</h3>
<!-- /wp:heading -->
<!-- wp:paragraph {"fontSize":"3xl"} -->
<p class="has-3-xl-font-size"><strong>R$ 19,90</strong><span style="font-size:1rem">/mês</span></p>
<!-- /wp:paragraph -->
<!-- wp:list -->
<ul class="wp-block-list"><li>Catálogo sob demanda</li><li>1 tela simultânea</li><li>Qualidade HD</li></ul>
<!-- /wp:list -->
<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"width":100} -->
<div class="wp-block-button has-custom-width wp-block-button__width-100"><a class="wp-block-button__link wp-element-button">Começar Agora</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->',
    ]);
}
