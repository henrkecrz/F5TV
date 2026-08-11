<?php
/**
 * F5TV Theme - REST API Endpoints
 * Criação de endpoints customizados sob o namespace f5tv/v1/
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('rest_api_init', 'f5tv_register_rest_routes');
function f5tv_register_rest_routes(): void
{
    // Rota: Catálogo
    register_rest_route('f5tv/v1', '/catalog', [
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => 'f5tv_rest_handle_catalog',
        'permission_callback' => '__return_true', // Permitir acesso público
    ]);

    // Rota: Canais / Programação Ao Vivo
    register_rest_route('f5tv/v1', '/live', [
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => 'f5tv_rest_handle_live',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('f5tv/v1', '/live/stream/(?P<id>\d+)', [
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'f5tv_rest_handle_live_stream',
        'permission_callback' => '__return_true',
    ]);

    // Rota: Planos e Cupons
    register_rest_route('f5tv/v1', '/billing', [
        'methods'             => [WP_REST_Server::READABLE, WP_REST_Server::CREATABLE],
        'callback'            => 'f5tv_rest_handle_billing',
        'permission_callback' => '__return_true',
    ]);
}

function f5tv_rest_handle_live_stream(WP_REST_Request $request)
{
    $channel_id = absint($request['id']);
    $channel = get_post($channel_id);
    $stream_url = $channel && $channel->post_type === 'f5tv_canal'
        ? (string) get_post_meta($channel_id, 'stream_url', true)
        : '';
    $is_active = $channel && get_post_meta($channel_id, 'active', true) && get_post_meta($channel_id, 'status', true) !== 'offline';

    if (!$is_active || !$stream_url) {
        return new WP_Error('f5tv_live_unavailable', 'Canal indisponivel.', ['status' => 404]);
    }

    $response = new WP_REST_Response(null, 302);
    $response->header('Location', esc_url_raw($stream_url));
    $response->header('Cache-Control', 'no-store, private');
    return $response;
}

/**
 * Handler: Catálogo
 */
function f5tv_rest_handle_catalog(WP_REST_Request $request): WP_REST_Response
{
    $action = $request->get_param('action');
    $id = $request->get_param('id');
    $q = $request->get_param('q');
    $type = $request->get_param('type');

    if ($action === 'categories') {
        $terms = get_terms([
            'taxonomy'   => 'f5tv_categoria',
            'hide_empty' => false,
        ]);
        $response = [];
        foreach ($terms as $term) {
            $response[] = [
                'id'   => $term->slug,
                'name' => $term->name,
                'slug' => $term->slug,
            ];
        }
        return new WP_REST_Response($response, 200);
    }

    if ($action === 'series') {
        $args = [
            'post_type'      => 'f5tv_serie',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ];
        if (!empty($id)) {
            $args['name'] = $id; // Buscar por slug
        }
        $query = new WP_Query($args);
        $response = [];
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $response[] = [
                'id'          => get_post_field('post_name', $post_id),
                'title'       => get_the_title(),
                'description' => get_the_content(),
                'coverUrl'    => get_field('cover_url', $post_id) ?: get_the_post_thumbnail_url($post_id, 'f5tv-content-cover'),
                'bannerUrl'   => get_field('banner_url', $post_id) ?: get_the_post_thumbnail_url($post_id, 'f5tv-content-banner'),
                'genre'       => get_field('genre', $post_id) ?: 'Série',
                'viewsCount'  => intval(get_field('views_count', $post_id)),
            ];
        }
        wp_reset_postdata();

        if (!empty($id) && !empty($response)) {
            return new WP_REST_Response($response[0], 200);
        }
        return new WP_REST_Response($response, 200);
    }

    // Default: listar somente programas e documentários do catálogo.
    // Séries possuem a ação explícita "series" para não misturar os CPTs.
    $args = [
        'post_type'      => 'f5tv_conteudo',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ];

    if (!empty($id)) {
        $args['name'] = $id; // Buscar por slug
    }
    if (!empty($type)) {
        $args['meta_query'] = [
            [
                'key'   => 'content_type',
                'value' => $type,
            ]
        ];
    }
    if (!empty($q)) {
        $args['s'] = $q;
    }

    $query = new WP_Query($args);
    $response = [];

    while ($query->have_posts()) {
        $query->the_post();
        $post_id = get_the_ID();
        
        // Categorias
        $cats = get_the_terms($post_id, 'f5tv_categoria');
        $cat_id = ($cats && !is_wp_error($cats)) ? $cats[0]->slug : 'programas';

        // Elenco e diretores salvos como textarea (um por linha)
        $cast_raw = get_field('cast', $post_id);
        $cast = $cast_raw ? array_filter(array_map('trim', explode("\n", $cast_raw))) : [];

        $directors_raw = get_field('directors', $post_id);
        $directors = $directors_raw ? array_filter(array_map('trim', explode("\n", $directors_raw))) : [];

        $response[] = [
            'id'               => get_post_field('post_name', $post_id),
            'type'             => get_field('content_type', $post_id) ?: 'programa',
            'title'            => get_the_title(),
            'shortDescription' => get_the_excerpt() ?: get_field('short_description', $post_id) ?: get_the_title(),
            'fullDescription'  => get_the_content() ?: get_field('full_description', $post_id) ?: get_the_title(),
            'categoryId'       => $cat_id,
            'genre'            => get_field('genre', $post_id) ?: 'Streaming',
            'ageRating'        => get_field('age_rating', $post_id) ?: 'Livre',
            'year'             => intval(get_field('year', $post_id)) ?: date('Y'),
            'duration'         => get_field('duration', $post_id) ?: '1 Temp',
            'cast'             => $cast,
            'directors'        => $directors,
            'coverUrl'         => f5tv_get_16x9_image($post_id, 'https://images.unsplash.com/photo-1594909122845-11baa439b7bf?q=80&w=1280'),
            'bannerUrl'        => f5tv_get_16x9_image($post_id, 'https://images.unsplash.com/photo-1594909122845-11baa439b7bf?q=80&w=1280'),
            'trailerUrl'       => get_field('trailer_url', $post_id) ?: '',
            'videoUrl'         => get_field('video_url', $post_id) ?: 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
            'status'           => get_post_status($post_id) === 'publish' ? 'published' : 'draft',
            'isFeatured'       => (bool) get_field('is_featured', $post_id),
            'isFree'           => (bool) get_field('is_free', $post_id),
            'isExclusive'      => (bool) get_field('is_exclusive', $post_id),
            'publishDate'      => get_the_date('Y-m-d'),
            'tags'             => wp_get_post_tags($post_id, ['fields' => 'names']) ?: [],
            'viewsCount'       => intval(get_field('views_count', $post_id)),
        ];
    }
    wp_reset_postdata();

    // Fallback de demonstração para a Área do Assinante se não houver itens gravados
    if (empty($response) && empty($id) && empty($q)) {
        $response = [
            [
                'id'               => 'conexao-f5-golpes-ciberneticos',
                'type'             => 'series',
                'title'            => 'Conexão F5: Golpes Cibernéticos',
                'shortDescription' => 'Uma investigação profunda sobre quadrilhas cibernéticas e engenharia social no Brasil.',
                'fullDescription'  => 'Episódios inéditos expondo esquemas de fraudes digitais com depoimentos exclusivos de especialistas e vítimas.',
                'categoryId'       => 'series',
                'genre'            => 'Investigativo',
                'ageRating'        => '16',
                'year'             => 2026,
                'duration'         => '1 Temp (8 eps)',
                'cast'             => ['Equipe de Jornalismo F5'],
                'directors'        => ['Redação F5 TV'],
                'coverUrl'         => 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=600',
                'bannerUrl'        => 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=1200',
                'trailerUrl'       => '',
                'videoUrl'         => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
                'status'           => 'published',
                'isFeatured'       => true,
                'isFree'           => false,
                'isExclusive'      => true,
                'publishDate'      => '2026-08-01',
                'tags'             => ['Investigativo', 'Exclusivo', 'F5 TV'],
                'viewsCount'       => 14200,
            ],
            [
                'id'               => 'jornal-f5-ao-vivo',
                'type'             => 'movie',
                'title'            => 'Jornal F5 Ao Vivo',
                'shortDescription' => 'Cobertura tática, boletins em tempo real e notícias urgentes do Brasil e do mundo.',
                'fullDescription'  => 'Transmissão contínua com ancoragem de ponta e repórteres em todas as capitais.',
                'categoryId'       => 'jornalismo',
                'genre'            => 'Jornalismo',
                'ageRating'        => 'Livre',
                'year'             => 2026,
                'duration'         => 'Ao Vivo',
                'cast'             => ['Âncoras F5'],
                'directors'        => ['Direção de Jornalismo'],
                'coverUrl'         => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=600',
                'bannerUrl'        => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=1200',
                'trailerUrl'       => '',
                'videoUrl'         => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4',
                'status'           => 'published',
                'isFeatured'       => false,
                'isFree'           => true,
                'isExclusive'      => false,
                'publishDate'      => '2026-08-01',
                'tags'             => ['Jornalismo', 'Ao Vivo'],
                'viewsCount'       => 9800,
            ],
            [
                'id'               => 'rondas-noturnas-sp',
                'type'             => 'series',
                'title'            => 'Rondas Noturnas SP',
                'shortDescription' => 'Acompanhe as operações e chamados de emergência durante a madrugada nas metrópoles.',
                'fullDescription'  => 'Documentário tático sobre as equipes de resgate, patrulhamento e bombeiros.',
                'categoryId'       => 'series',
                'genre'            => 'Documentário',
                'ageRating'        => '14',
                'year'             => 2026,
                'duration'         => '1 Temp (6 eps)',
                'cast'             => ['Equipes de Emergência'],
                'directors'        => ['Produções F5'],
                'coverUrl'         => 'https://images.unsplash.com/photo-1594909122845-11baa439b7bf?q=80&w=600',
                'bannerUrl'        => 'https://images.unsplash.com/photo-1594909122845-11baa439b7bf?q=80&w=1200',
                'trailerUrl'       => '',
                'videoUrl'         => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4',
                'status'           => 'published',
                'isFeatured'       => false,
                'isFree'           => false,
                'isExclusive'      => true,
                'publishDate'      => '2026-08-01',
                'tags'             => ['Documentário', 'Tático'],
                'viewsCount'       => 11500,
            ],
            [
                'id'               => 'bastidores-do-poder',
                'type'             => 'series',
                'title'            => 'Bastidores do Poder',
                'shortDescription' => 'Análises sem filtro dos acontecimentos políticos e econômicos mais relevantes.',
                'fullDescription'  => 'Debates francos com especialistas renomados.',
                'categoryId'       => 'politica',
                'genre'            => 'Política',
                'ageRating'        => '12',
                'year'             => 2026,
                'duration'         => '1 Temp (10 eps)',
                'cast'             => ['Comentaristas F5'],
                'directors'        => ['Redação F5'],
                'coverUrl'         => 'https://images.unsplash.com/photo-1578328819058-b69f3a3b0f6b?q=80&w=600',
                'bannerUrl'        => 'https://images.unsplash.com/photo-1578328819058-b69f3a3b0f6b?q=80&w=1200',
                'trailerUrl'       => '',
                'videoUrl'         => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4',
                'status'           => 'published',
                'isFeatured'       => false,
                'isFree'           => false,
                'isExclusive'      => false,
                'publishDate'      => '2026-08-01',
                'tags'             => ['Política'],
                'viewsCount'       => 7400,
            ],
            [
                'id'               => 'operacao-fronteira-24h',
                'type'             => 'series',
                'title'            => 'Operação Fronteira 24h',
                'shortDescription' => 'Bastidores do combate ao contrabando e fiscalização nas divisas brasileiras.',
                'fullDescription'  => 'Série de ação cobrindo agentes de fiscalização em terra e ar.',
                'categoryId'       => 'series',
                'genre'            => 'Ação / Realidade',
                'ageRating'        => '16',
                'year'             => 2026,
                'duration'         => '1 Temp (12 eps)',
                'cast'             => ['Agentes de Segurança'],
                'directors'        => ['F5 Documentários'],
                'coverUrl'         => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?q=80&w=600',
                'bannerUrl'        => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?q=80&w=1200',
                'trailerUrl'       => '',
                'videoUrl'         => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4',
                'status'           => 'published',
                'isFeatured'       => false,
                'isFree'           => false,
                'isExclusive'      => true,
                'publishDate'      => '2026-08-01',
                'tags'             => ['Ação', 'Fronteira'],
                'viewsCount'       => 16800,
            ],
        ];
    }

    if (!empty($id) && !empty($response)) {
        return new WP_REST_Response($response[0], 200);
    }

    return new WP_REST_Response($response, 200);
}

/**
 * Handler: TV Ao Vivo
 */
function f5tv_rest_handle_live(WP_REST_Request $request): WP_REST_Response
{
    $action = $request->get_param('action');
    $date = $request->get_param('date') ?: date('Y-m-d');
    $channel_id = $request->get_param('channel_id');

    if ($action === 'schedule') {
        $args = [
            'post_type'      => 'f5tv_programacao',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'meta_query'     => [
                'relation' => 'AND',
                [
                    'key'     => 'date',
                    'value'   => $date,
                    'compare' => '=',
                ]
            ]
        ];

        if (!empty($channel_id)) {
            // Se passar o canal (slug ou ID), buscar o post correspondente do canal
            $channel_post = get_page_by_path($channel_id, OBJECT, 'f5tv_canal');
            if ($channel_post) {
                $args['meta_query'][] = [
                    'key'     => 'channel_id',
                    'value'   => $channel_post->ID,
                    'compare' => '=',
                ];
            }
        }

        $query = new WP_Query($args);
        $response = [];

        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();

            // Pega o slug/post_name do canal associado
            $chan_post_id = get_field('channel_id', $post_id);
            $chan_slug = $chan_post_id ? get_post_field('post_name', $chan_post_id) : '';

            $response[] = [
                'id'          => get_post_field('post_name', $post_id),
                'channelId'   => $chan_slug,
                'title'       => get_the_title(),
                'description' => get_field('description', $post_id) ?: '',
                'host'        => get_field('host', $post_id) ?: '',
                'date'        => get_field('date', $post_id),
                'startTime'   => substr(get_field('start_time', $post_id), 0, 5), // HH:MM
                'endTime'     => substr(get_field('end_time', $post_id), 0, 5), // HH:MM
                'status'      => get_field('status', $post_id) ?: 'scheduled',
                'imageUrl'    => get_field('image_url', $post_id) ?: '',
                'isFeatured'  => (bool) get_field('is_featured', $post_id),
            ];
        }
        wp_reset_postdata();
        return new WP_REST_Response($response, 200);
    }

    // Default: Listar Canais
    $query = new WP_Query([
        'post_type'      => 'f5tv_canal',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ]);

    $response = [];
    while ($query->have_posts()) {
        $query->the_post();
        $post_id = get_the_ID();

        // Categorias associadas
        $terms = get_the_terms($post_id, 'f5tv_categoria');
        $category = ($terms && !is_wp_error($terms)) ? $terms[0]->name : 'Geral';

        $response[] = [
            'id'          => get_post_field('post_name', $post_id),
            'name'        => get_the_title(),
            'description' => get_the_content(),
            'logoText'    => get_field('logo_text', $post_id) ?: 'F5',
            'streamUrl'   => get_field('stream_url', $post_id) ?: '',
            'active'      => (bool) get_field('active', $post_id),
            'status'      => get_field('status', $post_id) ?: 'online',
            'category'    => $category,
        ];
    }
    wp_reset_postdata();

    return new WP_REST_Response($response, 200);
}

/**
 * Handler: Billing (Planos, Checkout, Cupons)
 */
function f5tv_rest_handle_billing(WP_REST_Request $request): WP_REST_Response
{
    $action = $request->get_param('action');

    if ($action === 'plans') {
        $response = [
            [
                'id'       => 'plano-basico',
                'name'     => 'Básico',
                'price'    => 19.90,
                'features' => ['Catálogo sob demanda', '1 tela', 'Qualidade HD'],
                'active'   => true
            ],
            [
                'id'       => 'plano-familia',
                'name'     => 'Família',
                'price'    => 34.90,
                'features' => ['Catálogo completo', '3 telas', 'Full HD', 'Ao vivo'],
                'active'   => true
            ],
            [
                'id'       => 'plano-premium',
                'name'     => 'Premium',
                'price'    => 49.90,
                'features' => ['Catálogo completo', '5 telas', 'Full HD/4K', 'Ao vivo', 'Estreias e especiais'],
                'active'   => true
            ]
        ];
        
        return new WP_REST_Response($response, 200);
    }

    return new WP_REST_Response(['message' => 'Action not implemented.'], 400);
}
