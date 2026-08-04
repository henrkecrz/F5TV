<?php
/**
 * F5TV Theme - Template Tags
 * Funções utilitárias auxiliares para renderização no tema
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Retorna a imagem de capa do conteúdo. Se não houver capa em ACF ou Thumbnail, usa um placeholder
 */
function f5tv_get_content_cover(int $post_id = 0, string $size = 'f5tv-content-cover'): string
{
    $post_id = $post_id ?: get_the_ID();
    if (!$post_id) {
        return '';
    }

    $cover_url = get_field('cover_url', $post_id);
    if ($cover_url) {
        return esc_url($cover_url);
    }

    $thumb = get_the_post_thumbnail_url($post_id, $size);
    if ($thumb) {
        return esc_url($thumb);
    }

    // Fallback: Gerar imagem gradiente placeholder
    return 'https://images.unsplash.com/photo-1594909122845-11baa439b7bf?q=80&w=600';
}

/**
 * Retorna o banner horizontal do conteúdo. Se não houver, usa imagem destacada ou fallback
 */
function f5tv_get_content_banner(int $post_id = 0, string $size = 'f5tv-content-banner'): string
{
    $post_id = $post_id ?: get_the_ID();
    if (!$post_id) {
        return '';
    }

    $banner_url = get_field('banner_url', $post_id);
    if ($banner_url) {
        return esc_url($banner_url);
    }

    $thumb = get_the_post_thumbnail_url($post_id, $size);
    if ($thumb) {
        return esc_url($thumb);
    }

    // Fallback banner
    return 'https://images.unsplash.com/photo-1495020689067-958852a7765e?q=80&w=1400';
}

/**
 * Formata duração de exibição
 */
function f5tv_format_duration(string $duration_str): string
{
    if (empty($duration_str)) {
        return '';
    }
    return esc_html($duration_str);
}

/**
 * Exibe badges de controle (Exclusivo, Grátis, Categoria)
 */
function f5tv_render_content_badges(int $post_id = 0): void
{
    $post_id = $post_id ?: get_the_ID();
    if (!$post_id) {
        return;
    }

    $is_free = get_field('is_free', $post_id);
    $is_exclusive = get_field('is_exclusive', $post_id);

    echo '<div class="f5tv-badges flex gap-2 mb-2">';
    if ($is_free) {
        echo '<span class="badge bg-emerald-600 text-white text-xs px-2 py-0.5 rounded font-semibold uppercase">' . esc_html__('Grátis', 'f5tv-theme') . '</span>';
    }
    if ($is_exclusive) {
        echo '<span class="badge bg-red-600 text-white text-xs px-2 py-0.5 rounded font-semibold uppercase">' . esc_html__('Exclusivo', 'f5tv-theme') . '</span>';
    }
    
    $cats = get_the_terms($post_id, 'f5tv_categoria');
    if ($cats && !is_wp_error($cats)) {
        echo '<span class="badge bg-zinc-800 text-zinc-300 text-xs px-2 py-0.5 rounded font-semibold uppercase">' . esc_html($cats[0]->name) . '</span>';
    }
    echo '</div>';
}
