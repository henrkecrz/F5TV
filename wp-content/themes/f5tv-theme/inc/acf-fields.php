<?php
/**
 * F5TV Theme - ACF Fields
 * Registro programático de campos personalizados via ACF Pro
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('acf/init', 'f5tv_register_acf_fields');
function f5tv_register_acf_fields(): void
{
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    // 1. Grupo de Campos: Conteúdo (f5tv_conteudo)
    acf_add_local_field_group([
        'key' => 'group_f5tv_conteudo',
        'title' => __('Campos do Conteúdo', 'f5tv-theme'),
        'fields' => [
            [
                'key' => 'field_f5tv_content_type',
                'label' => __('Tipo de Conteúdo', 'f5tv-theme'),
                'name' => 'content_type',
                'type' => 'select',
                'choices' => [
                    'movie'       => __('Filme', 'f5tv-theme'),
                    'series'      => __('Série', 'f5tv-theme'),
                    'tv_show'     => __('Show de TV', 'f5tv-theme'),
                    'news'        => __('Notícias', 'f5tv-theme'),
                    'sports'      => __('Esportes', 'f5tv-theme'),
                    'documentary' => __('Documentário', 'f5tv-theme'),
                    'special'     => __('Especial', 'f5tv-theme'),
                ],
                'default_value' => 'movie',
                'return_format' => 'value',
                'wrapper' => ['width' => '50'],
            ],
            [
                'key' => 'field_f5tv_age_rating',
                'label' => __('Classificação Indicativa', 'f5tv-theme'),
                'name' => 'age_rating',
                'type' => 'select',
                'choices' => [
                    'L'  => __('Livre', 'f5tv-theme'),
                    '10' => __('10 anos', 'f5tv-theme'),
                    '12' => __('12 anos', 'f5tv-theme'),
                    '14' => __('14 anos', 'f5tv-theme'),
                    '16' => __('16 anos', 'f5tv-theme'),
                    '18' => __('18 anos', 'f5tv-theme'),
                ],
                'default_value' => 'L',
                'wrapper' => ['width' => '50'],
            ],
            [
                'key' => 'field_f5tv_year',
                'label' => __('Ano de Lançamento', 'f5tv-theme'),
                'name' => 'year',
                'type' => 'number',
                'default_value' => date('Y'),
                'wrapper' => ['width' => '33'],
            ],
            [
                'key' => 'field_f5tv_duration',
                'label' => __('Duração', 'f5tv-theme'),
                'name' => 'duration',
                'type' => 'text',
                'placeholder' => 'ex: 1h 45m ou 45m',
                'wrapper' => ['width' => '33'],
            ],
            [
                'key' => 'field_f5tv_views_count',
                'label' => __('Visualizações', 'f5tv-theme'),
                'name' => 'views_count',
                'type' => 'number',
                'default_value' => 0,
                'readonly' => true,
                'wrapper' => ['width' => '34'],
            ],
            [
                'key' => 'field_f5tv_cast',
                'label' => __('Elenco (Atores)', 'f5tv-theme'),
                'name' => 'cast',
                'type' => 'textarea',
                'instructions' => __('Insira um por linha', 'f5tv-theme'),
                'rows' => 3,
                'wrapper' => ['width' => '50'],
            ],
            [
                'key' => 'field_f5tv_directors',
                'label' => __('Diretores', 'f5tv-theme'),
                'name' => 'directors',
                'type' => 'textarea',
                'instructions' => __('Insira um por linha', 'f5tv-theme'),
                'rows' => 3,
                'wrapper' => ['width' => '50'],
            ],
            [
                'key' => 'field_f5tv_cover_url',
                'label' => __('URL da Capa (Vertical)', 'f5tv-theme'),
                'name' => 'cover_url',
                'type' => 'text',
                'instructions' => __('URL externa ou deixe em branco para usar a Imagem Destacada', 'f5tv-theme'),
                'wrapper' => ['width' => '50'],
            ],
            [
                'key' => 'field_f5tv_banner_url',
                'label' => __('URL do Banner (Horizontal)', 'f5tv-theme'),
                'name' => 'banner_url',
                'type' => 'text',
                'instructions' => __('URL externa para o banner principal', 'f5tv-theme'),
                'wrapper' => ['width' => '50'],
            ],
            [
                'key' => 'field_f5tv_trailer_url',
                'label' => __('URL do Trailer', 'f5tv-theme'),
                'name' => 'trailer_url',
                'type' => 'text',
                'placeholder' => 'ex: URL do YouTube, Vimeo ou MP4',
                'wrapper' => ['width' => '50'],
            ],
            [
                'key' => 'field_f5tv_video_url',
                'label' => __('URL do Vídeo Principal (m3u8/mp4)', 'f5tv-theme'),
                'name' => 'video_url',
                'type' => 'text',
                'instructions' => __('URL do streaming (HLS/DASH/MP4)', 'f5tv-theme'),
                'wrapper' => ['width' => '50'],
            ],
            [
                'key' => 'field_f5tv_is_featured',
                'label' => __('Destacado?', 'f5tv-theme'),
                'name' => 'is_featured',
                'type' => 'true_false',
                'ui' => 1,
                'default_value' => 0,
                'wrapper' => ['width' => '33'],
            ],
            [
                'key' => 'field_f5tv_is_free',
                'label' => __('Gratuito (Sem assinatura)?', 'f5tv-theme'),
                'name' => 'is_free',
                'type' => 'true_false',
                'ui' => 1,
                'default_value' => 0,
                'wrapper' => ['width' => '33'],
            ],
            [
                'key' => 'field_f5tv_is_exclusive',
                'label' => __('Exclusivo Premium?', 'f5tv-theme'),
                'name' => 'is_exclusive',
                'type' => 'true_false',
                'ui' => 1,
                'default_value' => 1,
                'wrapper' => ['width' => '34'],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'f5tv_conteudo',
                ],
            ],
        ],
    ]);

    // 2. Grupo de Campos: Séries (f5tv_serie)
    acf_add_local_field_group([
        'key' => 'group_f5tv_serie',
        'title' => __('Campos da Série', 'f5tv-theme'),
        'fields' => [
            [
                'key' => 'field_f5tv_serie_cover_url',
                'label' => __('URL da Capa', 'f5tv-theme'),
                'name' => 'cover_url',
                'type' => 'text',
                'wrapper' => ['width' => '50'],
            ],
            [
                'key' => 'field_f5tv_serie_banner_url',
                'label' => __('URL do Banner', 'f5tv-theme'),
                'name' => 'banner_url',
                'type' => 'text',
                'wrapper' => ['width' => '50'],
            ],
            [
                'key' => 'field_f5tv_serie_views_count',
                'label' => __('Visualizações', 'f5tv-theme'),
                'name' => 'views_count',
                'type' => 'number',
                'default_value' => 0,
                'readonly' => true,
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'f5tv_serie',
                ],
            ],
        ],
    ]);

    // 3. Grupo de Campos: Temporadas (f5tv_temporada)
    acf_add_local_field_group([
        'key' => 'group_f5tv_temporada',
        'title' => __('Campos da Temporada', 'f5tv-theme'),
        'fields' => [
            [
                'key' => 'field_f5tv_temp_series_id',
                'label' => __('Série Relacionada', 'f5tv-theme'),
                'name' => 'series_id',
                'type' => 'post_object',
                'post_type' => ['f5tv_serie'],
                'allow_null' => 0,
                'multiple' => 0,
                'return_format' => 'id',
                'wrapper' => ['width' => '50'],
            ],
            [
                'key' => 'field_f5tv_temp_number',
                'label' => __('Número da Temporada', 'f5tv-theme'),
                'name' => 'number',
                'type' => 'number',
                'default_value' => 1,
                'wrapper' => ['width' => '50'],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'f5tv_temporada',
                ],
            ],
        ],
    ]);

    // 4. Grupo de Campos: Episódios (f5tv_episodio)
    acf_add_local_field_group([
        'key' => 'group_f5tv_episodio',
        'title' => __('Campos do Episódio', 'f5tv-theme'),
        'fields' => [
            [
                'key' => 'field_f5tv_ep_season_id',
                'label' => __('Temporada Relacionada', 'f5tv-theme'),
                'name' => 'season_id',
                'type' => 'post_object',
                'post_type' => ['f5tv_temporada'],
                'allow_null' => 0,
                'multiple' => 0,
                'return_format' => 'id',
                'wrapper' => ['width' => '50'],
            ],
            [
                'key' => 'field_f5tv_ep_number',
                'label' => __('Número do Episódio', 'f5tv-theme'),
                'name' => 'number',
                'type' => 'number',
                'default_value' => 1,
                'wrapper' => ['width' => '50'],
            ],
            [
                'key' => 'field_f5tv_ep_duration',
                'label' => __('Duração', 'f5tv-theme'),
                'name' => 'duration',
                'type' => 'text',
                'placeholder' => 'ex: 45m',
                'wrapper' => ['width' => '33'],
            ],
            [
                'key' => 'field_f5tv_ep_thumbnail_url',
                'label' => __('URL da Thumbnail', 'f5tv-theme'),
                'name' => 'thumbnail_url',
                'type' => 'text',
                'wrapper' => ['width' => '67'],
            ],
            [
                'key' => 'field_f5tv_ep_video_url',
                'label' => __('URL do Vídeo (m3u8/mp4)', 'f5tv-theme'),
                'name' => 'video_url',
                'type' => 'text',
                'wrapper' => ['width' => '100'],
            ],
            [
                'key' => 'field_f5tv_ep_views_count',
                'label' => __('Visualizações', 'f5tv-theme'),
                'name' => 'views_count',
                'type' => 'number',
                'default_value' => 0,
                'readonly' => true,
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'f5tv_episodio',
                ],
            ],
        ],
    ]);

    // 5. Grupo de Campos: Canais (f5tv_canal)
    acf_add_local_field_group([
        'key' => 'group_f5tv_canal',
        'title' => __('Campos do Canal', 'f5tv-theme'),
        'fields' => [
            [
                'key' => 'field_f5tv_canal_logo_text',
                'label' => __('Texto do Logo', 'f5tv-theme'),
                'name' => 'logo_text',
                'type' => 'text',
                'placeholder' => 'ex: NEWS, SPORT',
                'wrapper' => ['width' => '33'],
            ],
            [
                'key' => 'field_f5tv_canal_status',
                'label' => __('Status da Transmissão', 'f5tv-theme'),
                'name' => 'status',
                'type' => 'select',
                'choices' => [
                    'online'  => 'Online',
                    'offline' => 'Offline',
                ],
                'default_value' => 'online',
                'wrapper' => ['width' => '33'],
            ],
            [
                'key' => 'field_f5tv_canal_active',
                'label' => __('Ativo?', 'f5tv-theme'),
                'name' => 'active',
                'type' => 'true_false',
                'ui' => 1,
                'default_value' => 1,
                'wrapper' => ['width' => '34'],
            ],
            [
                'key' => 'field_f5tv_canal_stream_url',
                'label' => __('URL do Stream ao Vivo (m3u8/mp4)', 'f5tv-theme'),
                'name' => 'stream_url',
                'type' => 'text',
                'wrapper' => ['width' => '100'],
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'f5tv_canal',
                ],
            ],
        ],
    ]);

    // 6. Grupo de Campos: Programação (f5tv_programacao)
    acf_add_local_field_group([
        'key' => 'group_f5tv_programacao',
        'title' => __('Campos da Programação', 'f5tv-theme'),
        'fields' => [
            [
                'key' => 'field_f5tv_prog_channel_id',
                'label' => __('Canal Associado', 'f5tv-theme'),
                'name' => 'channel_id',
                'type' => 'post_object',
                'post_type' => ['f5tv_canal'],
                'allow_null' => 0,
                'multiple' => 0,
                'return_format' => 'id',
                'wrapper' => ['width' => '50'],
            ],
            [
                'key' => 'field_f5tv_prog_host',
                'label' => __('Apresentador / Responsável', 'f5tv-theme'),
                'name' => 'host',
                'type' => 'text',
                'wrapper' => ['width' => '50'],
            ],
            [
                'key' => 'field_f5tv_prog_date',
                'label' => __('Data', 'f5tv-theme'),
                'name' => 'date',
                'type' => 'date_picker',
                'display_format' => 'd/m/Y',
                'return_format' => 'Y-m-d',
                'wrapper' => ['width' => '33'],
            ],
            [
                'key' => 'field_f5tv_prog_start_time',
                'label' => __('Hora de Início', 'f5tv-theme'),
                'name' => 'start_time',
                'type' => 'time_picker',
                'display_format' => 'H:i',
                'return_format' => 'H:i:s',
                'wrapper' => ['width' => '33'],
            ],
            [
                'key' => 'field_f5tv_prog_end_time',
                'label' => __('Hora de Término', 'f5tv-theme'),
                'name' => 'end_time',
                'type' => 'time_picker',
                'display_format' => 'H:i',
                'return_format' => 'H:i:s',
                'wrapper' => ['width' => '34'],
            ],
            [
                'key' => 'field_f5tv_prog_status',
                'label' => __('Status do Programa', 'f5tv-theme'),
                'name' => 'status',
                'type' => 'select',
                'choices' => [
                    'live'      => __('Ao Vivo', 'f5tv-theme'),
                    'premiere'  => __('Estreia', 'f5tv-theme'),
                    'rerun'     => __('Reprise', 'f5tv-theme'),
                    'scheduled' => __('Programado', 'f5tv-theme'),
                    'ended'     => __('Encerrado', 'f5tv-theme'),
                ],
                'default_value' => 'scheduled',
                'wrapper' => ['width' => '50'],
            ],
            [
                'key' => 'field_f5tv_prog_image_url',
                'label' => __('URL da Imagem de Capa', 'f5tv-theme'),
                'name' => 'image_url',
                'type' => 'text',
                'wrapper' => ['width' => '50'],
            ],
            [
                'key' => 'field_f5tv_prog_is_featured',
                'label' => __('Destaque na Grade?', 'f5tv-theme'),
                'name' => 'is_featured',
                'type' => 'true_false',
                'ui' => 1,
                'default_value' => 0,
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'f5tv_programacao',
                ],
            ],
        ],
    ]);
}
