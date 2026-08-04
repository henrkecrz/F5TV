<?php
/**
 * F5TV Theme - Taxonomies
 * Registro de taxonomias customizadas
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', 'f5tv_register_taxonomies');
function f5tv_register_taxonomies(): void
{
    // Taxonomia: Categorias de Conteúdo (Hierárquica)
    $cat_labels = [
        'name'              => _x('Categorias', 'taxonomy general name', 'f5tv-theme'),
        'singular_name'     => _x('Categoria', 'taxonomy singular name', 'f5tv-theme'),
        'search_items'      => __('Buscar Categorias', 'f5tv-theme'),
        'all_items'         => __('Todas as Categorias', 'f5tv-theme'),
        'parent_item'       => __('Categoria Pai', 'f5tv-theme'),
        'parent_item_colon' => __('Categoria Pai:', 'f5tv-theme'),
        'edit_item'         => __('Editar Categoria', 'f5tv-theme'),
        'update_item'       => __('Atualizar Categoria', 'f5tv-theme'),
        'add_new_item'      => __('Adicionar Nova Categoria', 'f5tv-theme'),
        'new_item_name'     => __('Novo Nome de Categoria', 'f5tv-theme'),
        'menu_name'         => __('Categorias', 'f5tv-theme'),
    ];

    $cat_args = [
        'hierarchical'      => true,
        'labels'            => $cat_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => ['slug' => 'categoria-streaming'],
        'show_in_rest'      => true, // Importante para Gutenberg e REST API
    ];

    register_taxonomy('f5tv_categoria', ['f5tv_conteudo', 'f5tv_serie'], $cat_args);

    // Taxonomia: Gêneros de Conteúdo (Não hierárquica, tipo tags)
    $genre_labels = [
        'name'              => _x('Gêneros', 'taxonomy general name', 'f5tv-theme'),
        'singular_name'     => _x('Gênero', 'taxonomy singular name', 'f5tv-theme'),
        'search_items'      => __('Buscar Gêneros', 'f5tv-theme'),
        'all_items'         => __('Todos os Gêneros', 'f5tv-theme'),
        'parent_item'       => null,
        'parent_item_colon' => null,
        'edit_item'         => __('Editar Gênero', 'f5tv-theme'),
        'update_item'       => __('Atualizar Gênero', 'f5tv-theme'),
        'add_new_item'      => __('Adicionar Novo Gênero', 'f5tv-theme'),
        'new_item_name'     => __('Novo Nome de Gênero', 'f5tv-theme'),
        'menu_name'         => __('Gêneros', 'f5tv-theme'),
    ];

    $genre_args = [
        'hierarchical'      => false,
        'labels'            => $genre_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => ['slug' => 'genero-streaming'],
        'show_in_rest'      => true,
    ];

    register_taxonomy('f5tv_genero', ['f5tv_conteudo', 'f5tv_serie'], $genre_args);
}
