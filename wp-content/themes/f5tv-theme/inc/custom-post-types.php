<?php
/**
 * F5TV Theme - Custom Post Types
 * Registro de Custom Post Types
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', 'f5tv_register_post_types');
function f5tv_register_post_types(): void
{
    // 1. CPT: Conteúdo (f5tv_conteudo)
    $conteudo_labels = [
        'name'               => __('Conteúdos', 'f5tv-theme'),
        'singular_name'      => __('Conteúdo', 'f5tv-theme'),
        'add_new'            => __('Adicionar Novo', 'f5tv-theme'),
        'add_new_item'       => __('Adicionar Novo Conteúdo', 'f5tv-theme'),
        'edit_item'          => __('Editar Conteúdo', 'f5tv-theme'),
        'new_item'           => __('Novo Conteúdo', 'f5tv-theme'),
        'view_item'          => __('Visualizar Conteúdo', 'f5tv-theme'),
        'search_items'       => __('Buscar Conteúdos', 'f5tv-theme'),
        'not_found'          => __('Nenhum conteúdo encontrado', 'f5tv-theme'),
        'not_found_in_trash' => __('Nenhum conteúdo na lixeira', 'f5tv-theme'),
        'all_items'          => __('Todos os Conteúdos', 'f5tv-theme'),
        'menu_name'          => __('F5 Streaming', 'f5tv-theme'),
    ];

    $conteudo_args = [
        'labels'             => $conteudo_labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => ['slug' => 'assista'],
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-format-video',
        'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
        'show_in_rest'       => true,
    ];

    register_post_type('f5tv_conteudo', $conteudo_args);

    // 2. CPT: Séries (f5tv_serie)
    $serie_labels = [
        'name'               => __('Séries', 'f5tv-theme'),
        'singular_name'      => __('Série', 'f5tv-theme'),
        'add_new'            => __('Adicionar Nova', 'f5tv-theme'),
        'add_new_item'       => __('Adicionar Nova Série', 'f5tv-theme'),
        'edit_item'          => __('Editar Série', 'f5tv-theme'),
        'new_item'           => __('Nova Série', 'f5tv-theme'),
        'view_item'          => __('Visualizar Série', 'f5tv-theme'),
        'search_items'       => __('Buscar Séries', 'f5tv-theme'),
        'not_found'          => __('Nenhuma série encontrada', 'f5tv-theme'),
        'not_found_in_trash' => __('Nenhuma série na lixeira', 'f5tv-theme'),
        'all_items'          => __('Todas as Séries', 'f5tv-theme'),
        'menu_name'          => __('Séries', 'f5tv-theme'),
    ];

    $serie_args = [
        'labels'             => $serie_labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => 'edit.php?post_type=f5tv_conteudo', // Agrupa no menu principal do streaming
        'query_var'          => true,
        'rewrite'            => ['slug' => 'series'],
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
        'show_in_rest'       => true,
    ];

    register_post_type('f5tv_serie', $serie_args);

    // 3. CPT: Temporadas (f5tv_temporada)
    $temporada_labels = [
        'name'               => __('Temporadas', 'f5tv-theme'),
        'singular_name'      => __('Temporada', 'f5tv-theme'),
        'add_new'            => __('Adicionar Nova', 'f5tv-theme'),
        'add_new_item'       => __('Adicionar Nova Temporada', 'f5tv-theme'),
        'edit_item'          => __('Editar Temporada', 'f5tv-theme'),
        'new_item'           => __('Nova Temporada', 'f5tv-theme'),
        'view_item'          => __('Visualizar Temporada', 'f5tv-theme'),
        'search_items'       => __('Buscar Temporadas', 'f5tv-theme'),
        'not_found'          => __('Nenhuma temporada encontrada', 'f5tv-theme'),
        'not_found_in_trash' => __('Nenhuma temporada na lixeira', 'f5tv-theme'),
        'all_items'          => __('Todas as Temporadas', 'f5tv-theme'),
        'menu_name'          => __('Temporadas', 'f5tv-theme'),
    ];

    $temporada_args = [
        'labels'             => $temporada_labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => 'edit.php?post_type=f5tv_conteudo',
        'query_var'          => true,
        'rewrite'            => ['slug' => 'temporadas'],
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'supports'           => ['title', 'custom-fields'],
        'show_in_rest'       => true,
    ];

    register_post_type('f5tv_temporada', $temporada_args);

    // 4. CPT: Episódios (f5tv_episodio)
    $episodio_labels = [
        'name'               => __('Episódios', 'f5tv-theme'),
        'singular_name'      => __('Episódio', 'f5tv-theme'),
        'add_new'            => __('Adicionar Novo', 'f5tv-theme'),
        'add_new_item'       => __('Adicionar Novo Episódio', 'f5tv-theme'),
        'edit_item'          => __('Editar Episódio', 'f5tv-theme'),
        'new_item'           => __('Novo Episódio', 'f5tv-theme'),
        'view_item'          => __('Visualizar Episódio', 'f5tv-theme'),
        'search_items'       => __('Buscar Episódios', 'f5tv-theme'),
        'not_found'          => __('Nenhum episódio encontrado', 'f5tv-theme'),
        'not_found_in_trash' => __('Nenhum episódio na lixeira', 'f5tv-theme'),
        'all_items'          => __('Todos os Episódios', 'f5tv-theme'),
        'menu_name'          => __('Episódios', 'f5tv-theme'),
    ];

    $episodio_args = [
        'labels'             => $episodio_labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => 'edit.php?post_type=f5tv_conteudo',
        'query_var'          => true,
        'rewrite'            => ['slug' => 'episodio'],
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
        'show_in_rest'       => true,
    ];

    register_post_type('f5tv_episodio', $episodio_args);

    // 5. CPT: Canais (f5tv_canal)
    $canal_labels = [
        'name'               => __('Canais', 'f5tv-theme'),
        'singular_name'      => __('Canal', 'f5tv-theme'),
        'add_new'            => __('Adicionar Novo', 'f5tv-theme'),
        'add_new_item'       => __('Adicionar Novo Canal', 'f5tv-theme'),
        'edit_item'          => __('Editar Canal', 'f5tv-theme'),
        'new_item'           => __('Novo Canal', 'f5tv-theme'),
        'view_item'          => __('Visualizar Canal', 'f5tv-theme'),
        'search_items'       => __('Buscar Canais', 'f5tv-theme'),
        'not_found'          => __('Nenhum canal encontrado', 'f5tv-theme'),
        'not_found_in_trash' => __('Nenhum canal na lixeira', 'f5tv-theme'),
        'all_items'          => __('Todos os Canais', 'f5tv-theme'),
        'menu_name'          => __('TV ao Vivo', 'f5tv-theme'),
    ];

    $canal_args = [
        'labels'             => $canal_labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => ['slug' => 'canal'],
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 6,
        'menu_icon'          => 'dashicons-video-alt',
        'supports'           => ['title', 'editor', 'thumbnail', 'custom-fields'],
        'show_in_rest'       => true,
    ];

    register_post_type('f5tv_canal', $canal_args);

    // 6. CPT: Programação (f5tv_programacao)
    $programacao_labels = [
        'name'               => __('Programações', 'f5tv-theme'),
        'singular_name'      => __('Programação', 'f5tv-theme'),
        'add_new'            => __('Adicionar Nova', 'f5tv-theme'),
        'add_new_item'       => __('Adicionar Nova Programação', 'f5tv-theme'),
        'edit_item'          => __('Editar Programação', 'f5tv-theme'),
        'new_item'           => __('Nova Programação', 'f5tv-theme'),
        'view_item'          => __('Visualizar Programação', 'f5tv-theme'),
        'search_items'       => __('Buscar Programações', 'f5tv-theme'),
        'not_found'          => __('Nenhuma programação encontrada', 'f5tv-theme'),
        'not_found_in_trash' => __('Nenhuma programação na lixeira', 'f5tv-theme'),
        'all_items'          => __('Grade de Programação', 'f5tv-theme'),
        'menu_name'          => __('Grade de Programação', 'f5tv-theme'),
    ];

    $programacao_args = [
        'labels'             => $programacao_labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => 'edit.php?post_type=f5tv_canal', // Agrupa na TV ao vivo
        'query_var'          => true,
        'rewrite'            => ['slug' => 'programacao'],
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'supports'           => ['title', 'custom-fields'],
        'show_in_rest'       => true,
    ];

    register_post_type('f5tv_programacao', $programacao_args);
}

/** Keep the grouped WordPress submenu useful as content is added. */
add_action('admin_menu', 'f5tv_refresh_content_menu_counts', 999);
function f5tv_refresh_content_menu_counts(): void
{
    foreach (['f5tv_serie' => 'Todas as Séries', 'f5tv_temporada' => 'Todas as Temporadas', 'f5tv_episodio' => 'Todos os Episódios'] as $post_type => $label) {
        $counts = wp_count_posts($post_type);
        $total = 0;
        if ($counts) {
            foreach (['publish', 'draft', 'pending', 'future', 'private'] as $status) {
                $total += (int) ($counts->{$status} ?? 0);
            }
        }
        $object = get_post_type_object($post_type);
        if ($object) {
            $object->labels->all_items = sprintf('%s (%d)', $label, $total);
        }
    }
}

add_filter('manage_f5tv_episodio_posts_columns', 'f5tv_episode_admin_columns');
function f5tv_episode_admin_columns(array $columns): array
{
    return [
        'cb' => $columns['cb'] ?? '',
        'title' => __('Episódio', 'f5tv-theme'),
        'program' => __('Programa', 'f5tv-theme'),
        'series' => __('Série', 'f5tv-theme'),
        'season' => __('Temporada', 'f5tv-theme'),
        'number' => __('Nº', 'f5tv-theme'),
        'date' => $columns['date'] ?? __('Data', 'f5tv-theme'),
    ];
}

add_action('manage_f5tv_episodio_posts_custom_column', 'f5tv_episode_admin_column_data', 10, 2);
function f5tv_episode_admin_column_data(string $column, int $post_id): void
{
    if ($column === 'program') {
        $program_id = absint(get_post_meta($post_id, 'content_id', true));
        echo $program_id ? esc_html(get_the_title($program_id)) : '<span style="color:#9ca3af">—</span>';
    } elseif ($column === 'season') {
        $season_id = absint(get_post_meta($post_id, 'season_id', true));
        echo $season_id ? esc_html(get_the_title($season_id)) : '<span style="color:#9ca3af">Episódio direto</span>';
    } elseif ($column === 'series') {
        $season_id = absint(get_post_meta($post_id, 'season_id', true));
        $series_id = $season_id ? absint(get_post_meta($season_id, 'series_id', true)) : 0;
        echo $series_id ? esc_html(get_the_title($series_id)) : '<span style="color:#9ca3af">—</span>';
    } elseif ($column === 'number') {
        echo esc_html(get_post_meta($post_id, 'number', true) ?: '1');
    }
}

/**
 * Colunas Customizadas para o Admin (F5 Streaming e Séries)
 */
add_filter('manage_f5tv_conteudo_posts_columns', 'f5tv_conteudo_custom_columns');
function f5tv_conteudo_custom_columns(array $columns): array
{
    $new_columns = [];
    $new_columns['cb']     = $columns['cb'];
    $new_columns['cover']  = __('Capa', 'f5tv-theme');
    $new_columns['title']  = __('Título do Conteúdo', 'f5tv-theme');
    $new_columns['genre']  = __('Gênero', 'f5tv-theme');
    $new_columns['type']   = __('Tipo', 'f5tv-theme');
    $new_columns['rating'] = __('Classificação', 'f5tv-theme');
    $new_columns['views']  = __('Visualizações', 'f5tv-theme');
    $new_columns['date']   = $columns['date'];
    return $new_columns;
}

add_action('manage_f5tv_conteudo_posts_custom_column', 'f5tv_conteudo_custom_columns_data', 10, 2);
function f5tv_conteudo_custom_columns_data(string $column, int $post_id): void
{
    if ($column === 'cover') {
        $cover = f5tv_get_field('cover_url', $post_id) ?: get_the_post_thumbnail_url($post_id, 'thumbnail') ?: 'https://images.unsplash.com/photo-1594909122845-11baa439b7bf?q=80&w=200';
        echo '<img src="' . esc_url($cover) . '" style="width:38px;height:52px;object-fit:cover;border-radius:4px;box-shadow:0 2px 6px rgba(0,0,0,0.3);">';
    } elseif ($column === 'genre') {
        echo esc_html(f5tv_get_field('genre', $post_id) ?: 'Streaming');
    } elseif ($column === 'type') {
        echo '<span style="font-family:monospace;font-size:10px;padding:2px 6px;background:#e50914;color:#fff;border-radius:3px;font-weight:bold;text-transform:uppercase;">' . esc_html(f5tv_get_field('content_type', $post_id) ?: 'Filme') . '</span>';
    } elseif ($column === 'rating') {
        echo '<span style="font-family:monospace;font-size:11px;font-weight:bold;color:#f59e0b;">' . esc_html(f5tv_get_field('age_rating', $post_id) ?: 'Livre') . '</span>';
    } elseif ($column === 'views') {
        echo esc_html(number_format(intval(f5tv_get_field('views_count', $post_id))));
    }
}

add_filter('manage_f5tv_serie_posts_columns', 'f5tv_serie_custom_columns');
function f5tv_serie_custom_columns(array $columns): array
{
    $new_columns = [];
    $new_columns['cb']    = $columns['cb'];
    $new_columns['cover'] = __('Capa', 'f5tv-theme');
    $new_columns['title'] = __('Título da Série', 'f5tv-theme');
    $new_columns['genre'] = __('Gênero', 'f5tv-theme');
    $new_columns['views'] = __('Visualizações', 'f5tv-theme');
    $new_columns['date']  = $columns['date'];
    return $new_columns;
}

add_action('manage_f5tv_serie_posts_custom_column', 'f5tv_serie_custom_columns_data', 10, 2);
function f5tv_serie_custom_columns_data(string $column, int $post_id): void
{
    if ($column === 'cover') {
        $cover = f5tv_get_field('cover_url', $post_id) ?: get_the_post_thumbnail_url($post_id, 'thumbnail') ?: 'https://images.unsplash.com/photo-1594909122845-11baa439b7bf?q=80&w=200';
        echo '<img src="' . esc_url($cover) . '" style="width:38px;height:52px;object-fit:cover;border-radius:4px;box-shadow:0 2px 6px rgba(0,0,0,0.3);">';
    } elseif ($column === 'genre') {
        echo esc_html(f5tv_get_field('genre', $post_id) ?: 'Investigativo');
    } elseif ($column === 'views') {
        echo esc_html(number_format(intval(f5tv_get_field('views_count', $post_id))));
    }
}

/**
 * Redirecionar 'Editar' para o F5 Streaming Studio Editor nas tabelas do WP
 */
add_filter('post_row_actions', 'f5tv_customize_row_actions', 10, 2);
function f5tv_customize_row_actions(array $actions, WP_Post $post): array
{
    if (in_array($post->post_type, ['f5tv_conteudo', 'f5tv_serie'])) {
        $studio_url = admin_url('admin.php?page=f5tv-content-studio&action=edit&id=' . $post->ID);
        $actions['edit'] = '<a href="' . esc_url($studio_url) . '" style="font-weight:bold;color:#e50914;">🎬 Editor F5 Studio</a>';
    }
    return $actions;
}

