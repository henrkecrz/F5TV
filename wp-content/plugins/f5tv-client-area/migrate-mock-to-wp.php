<?php
/**
 * F5TV Streaming - Migração de dados do mock React para WordPress
 *
 * Uso:
 * - Via WP-CLI: wp f5tv migrate
 * - Via include: require_once 'migrate-mock-to-wp.php'; f5tv_migrate_mock_data();
 *
 * IMPORTANTE: Execute em ambiente de desenvolvimento/staging primeiro.
 */

if (!defined('ABSPATH')) {
    exit;
}

if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('f5tv migrate', function () {
        f5tv_migrate_mock_data();
    });
}

function f5tv_migrate_mock_data(): void
{
    global $wpdb;

    $profiles_table = $wpdb->prefix . 'f5tv_profiles';
    $subs_table = $wpdb->prefix . 'f5tv_subscriptions';
    $history_table = $wpdb->prefix . 'f5tv_watch_history';
    $mylist_table = $wpdb->prefix . 'f5tv_my_list';
    $devices_table = $wpdb->prefix . 'f5tv_devices';

    WP_CLI::log('Iniciando migração de dados...');

    // 1. Taxonomias
    $categories = [
        ['slug' => 'ao-vivo', 'name' => 'Ao Vivo'],
        ['slug' => 'jornalismo', 'name' => 'Jornalismo'],
        ['slug' => 'series', 'name' => 'Séries'],
        ['slug' => 'programastv', 'name' => 'Programas de TV'],
        ['slug' => 'esportes', 'name' => 'Esportes'],
        ['slug' => 'documentarios', 'name' => 'Documentários'],
        ['slug' => 'entretenimento', 'name' => 'Entretenimento'],
        ['slug' => 'infantil', 'name' => 'Infantil'],
        ['slug' => 'bastidores', 'name' => 'Bastidores'],
        ['slug' => 'especiais', 'name' => 'Especiais F5 TV'],
    ];

    $genre_list = [
        'Jornalismo Investigativo', 'Infantil / Educativo', 'Urbanismo & Sociedade',
        'Ação & Investigação Policial', 'Tecnologia & Futuro', 'Infantil / Educação Científica',
        'Noticiário Diário', 'Talk Show', 'Documentário Social', 'Musical Acústico',
        'Transmissão Esportiva', 'Stand-up Comedy', 'Documentário de Tecnologia',
        'Formativa de Esporte', 'Plantão Especial', 'Animação Educativa', 'Esporte Radical',
        'Documentário Ecológico'
    ];

    foreach ($categories as $cat) {
        if (!term_exists($cat['slug'], 'f5tv_categoria')) {
            wp_insert_term($cat['name'], 'f5tv_categoria', ['slug' => $cat['slug']]);
            WP_CLI::log("Categoria criada: {$cat['name']}");
        }
    }

    foreach ($genre_list as $genre) {
        if (!term_exists($genre, 'f5tv_genero')) {
            wp_insert_term($genre, 'f5tv_genero');
            WP_CLI::log("Gênero criado: {$genre}");
        }
    }

    // 2. Usuários
    $users_data = [
        ['email' => 'admin@f5tv.com.br', 'name' => 'Henrique Administrador', 'role' => 'administrator', 'plan_id' => '', 'plan_status' => ''],
        ['email' => 'editor@f5tv.com.br', 'name' => 'Carolina Editora', 'role' => 'editor', 'plan_id' => '', 'plan_status' => ''],
        ['email' => 'financeiro@f5tv.com.br', 'name' => 'Rodrigo Financeiro', 'role' => 'subscriber', 'plan_id' => 'plano-premium', 'plan_status' => 'active'],
        ['email' => 'henrikeaps@gmail.com', 'name' => 'Gisele Assinante', 'role' => 'subscriber', 'plan_id' => 'plano-premium', 'plan_status' => 'active'],
        ['email' => 'arthur@gmail.com', 'name' => 'Arthur Souza', 'role' => 'subscriber', 'plan_id' => 'plano-basico', 'plan_status' => 'active'],
        ['email' => 'marcos@bol.com.br', 'name' => 'Marcos Inadimplente', 'role' => 'subscriber', 'plan_id' => 'plano-familia', 'plan_status' => 'past_due'],
        ['email' => 'roberto@yahoo.com', 'name' => 'Roberto Bloqueado', 'role' => 'subscriber', 'plan_id' => 'plano-basico', 'plan_status' => 'canceled'],
    ];

    $user_ids = [];
    foreach ($users_data as $user_data) {
        $user = get_user_by('email', $user_data['email']);
        if (!$user) {
            $user_id = wp_create_user($user_data['email'], 'f5tv123', $user_data['email']);
            wp_update_user(['ID' => $user_id, 'display_name' => $user_data['name'], 'role' => $user_data['role']]);
            $user = get_userdata($user_id);
            WP_CLI::log("Usuário criado: {$user_data['name']}");
        }
        $user_ids[$user_data['email']] = $user->ID;

        if (!empty($user_data['plan_id'])) {
            update_user_meta($user->ID, 'f5tv_plan', $user_data['plan_id']);
            update_user_meta($user->ID, 'f5tv_subscription_status', $user_data['plan_status']);
        }
    }

    // 3. Séries
    $series_data = [
        ['title' => 'Conexão F5', 'description' => 'A série jornalística investigativa que desvenda os grandes mistérios e as maiores fraudes tecnológicas do Brasil.', 'slug' => 'conexao-f5', 'genre' => 'Jornalismo Investigativo', 'views' => 12540],
        ['title' => 'Mundo F5 Kids', 'description' => 'Animações educativas divertidas, jogos cognitivos interativos e muita música alegre para acelerar o desenvolvimento criativo.', 'slug' => 'mundo-f5-kids', 'genre' => 'Infantil / Educativo', 'views' => 8430],
        ['title' => 'Bastidores da Cidade', 'description' => 'Os segredos das maiores metrópoles brasileiras. A vida noturna, a infraestrutura invisível dos metrôs, os túneis centenários.', 'slug' => 'bastidores-da-cidade', 'genre' => 'Urbanismo & Sociedade', 'views' => 9320],
        ['title' => 'Rastreadores Criminais', 'description' => 'Acompanhe de perto as operações táticas da polícia civil e federal brasileira no combate a crimes organizados.', 'slug' => 'rastreadores-criminais', 'genre' => 'Ação & Investigação Policial', 'views' => 16500],
        ['title' => 'F5 Tech Docs: Fronteira da I.A.', 'description' => 'Uma expedição pelos laboratórios de ponta no Brasil e Vale do Silício para entender a inteligência artificial.', 'slug' => 'f5-tech-docs-fronteira-da-ia', 'genre' => 'Tecnologia & Futuro', 'views' => 21900],
        ['title' => 'Pequenos Exploradores', 'description' => 'Como funciona um vulcão? Por que chove? Aventuras animadas de forma lúdica em 3D.', 'slug' => 'pequenos-exploradores', 'genre' => 'Infantil / Educação Científica', 'views' => 11050],
    ];

    $series_ids = [];
    foreach ($series_data as $series) {
        $post_id = wp_insert_post([
            'post_title' => $series['title'],
            'post_content' => $series['description'],
            'post_status' => 'publish',
            'post_type' => 'f5tv_serie',
            'post_name' => $series['slug'],
        ]);

        if (!is_wp_error($post_id)) {
            wp_set_object_terms($post_id, [$series['genre']], 'f5tv_genero');
            update_post_meta($post_id, 'cover_url', "https://images.unsplash.com/photo-1509198397868-475647b2a1e5?q=80&w=600");
            update_post_meta($post_id, 'banner_url', "https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=1200");
            update_post_meta($post_id, 'views_count', $series['views']);
            $series_ids[$series['slug']] = $post_id;
            WP_CLI::log("Série criada: {$series['title']}");
        }
    }

    // 4. Temporadas e Episódios
    $seasons_data = [
        ['id' => 'season-conexao-s1', 'series_slug' => 'conexao-f5', 'number' => 1, 'title' => 'Temporada 1: Ciber-ameaças'],
        ['id' => 'season-conexao-s2', 'series_slug' => 'conexao-f5', 'number' => 2, 'title' => 'Temporada 2: Crimes Ambientais'],
        ['id' => 'season-kids-s1', 'series_slug' => 'mundo-f5-kids', 'number' => 1, 'title' => 'Temporada 1: Letras e Cores'],
        ['id' => 'season-bastidores-s1', 'series_slug' => 'bastidores-da-cidade', 'number' => 1, 'title' => 'Temporada 1: O Coração de Ferro'],
        ['id' => 'season-rastreadores-s1', 'series_slug' => 'rastreadores-criminais', 'number' => 1, 'title' => 'Temporada 1: Na Linha de Frente'],
        ['id' => 'season-fronteira-s1', 'series_slug' => 'f5-tech-docs-fronteira-da-ia', 'number' => 1, 'title' => 'Temporada 1: A Nova Era Cognitiva'],
        ['id' => 'season-exploradores-s1', 'series_slug' => 'pequenos-exploradores', 'number' => 1, 'title' => 'Temporada 1: Mistérios da Ciência'],
    ];

    $season_ids = [];
    foreach ($seasons_data as $season) {
        $series_id = $series_ids[$season['series_slug']] ?? 0;
        if (!$series_id) continue;

        $post_id = wp_insert_post([
            'post_title' => $season['title'],
            'post_status' => 'publish',
            'post_type' => 'f5tv_temporada',
        ]);

        if (!is_wp_error($post_id)) {
            update_post_meta($post_id, 'series_id', $series_id);
            update_post_meta($post_id, 'number', $season['number']);
            $season_ids[$season['id']] = $post_id;
        }
    }

    $episodes_data = [
        ['id' => 'ep-conexao-1', 'season_id' => 'season-conexao-s1', 'number' => 1, 'title' => 'O Golpe do Pix Reverso', 'duration' => '45m', 'views' => 4320],
        ['id' => 'ep-conexao-2', 'season_id' => 'season-conexao-s1', 'number' => 2, 'title' => 'Infiltração na Darknet', 'duration' => '50m', 'views' => 3810],
        ['id' => 'ep-conexao-3', 'season_id' => 'season-conexao-s1', 'number' => 3, 'title' => 'A Ira do Ransomware', 'duration' => '48m', 'views' => 3120],
        ['id' => 'ep-conexao-s2-1', 'season_id' => 'season-conexao-s2', 'number' => 1, 'title' => 'Mercadores do Fogo', 'duration' => '52m', 'views' => 1290],
        ['id' => 'ep-kids-1', 'season_id' => 'season-kids-s1', 'number' => 1, 'title' => 'A Dança dos Animais Coloridos', 'duration' => '15m', 'views' => 5100],
        ['id' => 'ep-kids-2', 'season_id' => 'season-kids-s1', 'number' => 2, 'title' => 'Aventura Ecológica da Turma F5', 'duration' => '18m', 'views' => 3330],
        ['id' => 'ep-bast-1', 'season_id' => 'season-bastidores-s1', 'number' => 1, 'title' => 'Metrô Subterrâneo de São Paulo', 'duration' => '42m', 'views' => 9320],
        ['id' => 'ep-rastro-1', 'season_id' => 'season-rastreadores-s1', 'number' => 1, 'title' => 'A Rota do Tráfico de Armas', 'duration' => '46m', 'views' => 5200],
        ['id' => 'ep-rastro-2', 'season_id' => 'season-rastreadores-s1', 'number' => 2, 'title' => 'O Rei do Colarinho Branco', 'duration' => '50m', 'views' => 4100],
        ['id' => 'ep-front-1', 'season_id' => 'season-fronteira-s1', 'number' => 1, 'title' => 'Sua Mente em Silício', 'duration' => '52m', 'views' => 6300],
        ['id' => 'ep-front-2', 'season_id' => 'season-fronteira-s1', 'number' => 2, 'title' => 'A Bio-Impressora 3D de Órgãos', 'duration' => '45m', 'views' => 5120],
        ['id' => 'ep-expl-1', 'season_id' => 'season-exploradores-s1', 'number' => 1, 'title' => 'De Onde Vem a Chuva?', 'duration' => '18m', 'views' => 3800],
        ['id' => 'ep-expl-2', 'season_id' => 'season-exploradores-s1', 'number' => 2, 'title' => 'O Mistério da Gravidade', 'duration' => '19m', 'views' => 4200],
    ];

    foreach ($episodes_data as $episode) {
        $season_post_id = $season_ids[$episode['season_id']] ?? 0;
        if (!$season_post_id) continue;

        $post_id = wp_insert_post([
            'post_title' => $episode['title'],
            'post_status' => 'publish',
            'post_type' => 'f5tv_episodio',
        ]);

        if (!is_wp_error($post_id)) {
            update_post_meta($post_id, 'season_id', $season_post_id);
            update_post_meta($post_id, 'number', $episode['number']);
            update_post_meta($post_id, 'duration', $episode['duration']);
            update_post_meta($post_id, 'video_url', 'https://assets.mixkit.co/videos/preview/mixkit-software-developer-working-on-his-computer-34289-large.mp4');
            update_post_meta($post_id, 'views_count', $episode['views']);
        }
    }

    WP_CLI::log('Temporadas e episódios criados.');

    // 5. Conteúdos standalone
    $contents_data = [
        ['title' => 'Jornal F5: Edição Ao Vivo', 'type' => 'news', 'category' => 'jornalismo', 'genre' => 'Noticiário Diário', 'year' => 2026, 'duration' => '1h 20m', 'is_featured' => true, 'is_free' => true, 'is_exclusive' => false, 'views' => 22800],
        ['title' => 'F5 Entrevista: Grandes Ideias', 'type' => 'tv_show', 'category' => 'programastv', 'genre' => 'Talk Show', 'year' => 2026, 'duration' => '55m', 'is_featured' => false, 'is_free' => false, 'is_exclusive' => true, 'views' => 8900],
        ['title' => 'Vozes do Brasil: O Sertão Tecnológico', 'type' => 'documentary', 'category' => 'documentarios', 'genre' => 'Documentário Social', 'year' => 2025, 'duration' => '1h 12m', 'is_featured' => false, 'is_free' => false, 'is_exclusive' => true, 'views' => 15400],
        ['title' => 'Noite F5 Acústica: MPB em Alto Contraste', 'type' => 'special', 'category' => 'especiais', 'genre' => 'Musical Acústico', 'year' => 2026, 'duration' => '1h 38m', 'is_featured' => true, 'is_free' => false, 'is_exclusive' => true, 'views' => 19800],
        ['title' => 'Supercopa F5: Semifinal Paulista', 'type' => 'sports', 'category' => 'esportes', 'genre' => 'Transmissão Esportiva', 'year' => 2026, 'duration' => '2h 15m', 'is_featured' => false, 'is_free' => true, 'is_exclusive' => false, 'views' => 31200],
        ['title' => 'F5 Stand-up Show: Humor sem Filtro', 'type' => 'special', 'category' => 'entretenimento', 'genre' => 'Stand-up Comedy', 'year' => 2025, 'duration' => '1h 05m', 'is_featured' => false, 'is_free' => false, 'is_exclusive' => false, 'views' => 11200],
        ['title' => 'O Código de Ferro: Hackers vs Estado', 'type' => 'documentary', 'category' => 'documentarios', 'genre' => 'Documentário de Tecnologia', 'year' => 2026, 'duration' => '1h 15m', 'is_featured' => true, 'is_free' => false, 'is_exclusive' => true, 'views' => 20450],
        ['title' => 'F5 Esportes: Segredos nos Boxes de Interlagos', 'type' => 'sports', 'category' => 'esportes', 'genre' => 'Formativa de Esporte', 'year' => 2026, 'duration' => '48m', 'is_featured' => false, 'is_free' => false, 'is_exclusive' => true, 'views' => 14200],
        ['title' => 'F5 Plantão Especial: Reforma Tributária', 'type' => 'news', 'category' => 'jornalismo', 'genre' => 'Plantão Especial', 'year' => 2026, 'duration' => '1h 10m', 'is_featured' => false, 'is_free' => true, 'is_exclusive' => false, 'views' => 29850],
        ['title' => 'O Pequeno Astronauta: Viagem à Lua', 'type' => 'special', 'category' => 'infantil', 'genre' => 'Animação Educativa', 'year' => 2026, 'duration' => '35m', 'is_featured' => false, 'is_free' => true, 'is_exclusive' => true, 'views' => 9780],
        ['title' => 'Grandes Ondas: Nazaré & Saquarema', 'type' => 'sports', 'category' => 'esportes', 'genre' => 'Esporte Radical', 'year' => 2025, 'duration' => '1h 05m', 'is_featured' => false, 'is_free' => false, 'is_exclusive' => false, 'views' => 18900],
        ['title' => 'Cidades Sustentáveis: O Futuro Urbano', 'type' => 'documentary', 'category' => 'documentarios', 'genre' => 'Documentário Ecológico', 'year' => 2026, 'duration' => '1h 10m', 'is_featured' => false, 'is_free' => true, 'is_exclusive' => true, 'views' => 16120],
    ];

    $content_ids = [];
    foreach ($contents_data as $content) {
        $post_id = wp_insert_post([
            'post_title' => $content['title'],
            'post_content' => $content['title'] . ' - Conteúdo da F5 TV.',
            'post_status' => 'publish',
            'post_type' => 'f5tv_conteudo',
        ]);

        if (!is_wp_error($post_id)) {
            wp_set_object_terms($post_id, [$content['category']], 'f5tv_categoria');
            wp_set_object_terms($post_id, [$content['genre']], 'f5tv_genero');

            update_post_meta($post_id, 'content_type', $content['type']);
            update_post_meta($post_id, 'age_rating', 'L');
            update_post_meta($post_id, 'year', $content['year']);
            update_post_meta($post_id, 'duration', $content['duration']);
            update_post_meta($post_id, 'is_featured', $content['is_featured']);
            update_post_meta($post_id, 'is_free', $content['is_free']);
            update_post_meta($post_id, 'is_exclusive', $content['is_exclusive']);
            update_post_meta($post_id, 'views_count', $content['views']);
            update_post_meta($post_id, 'cover_url', 'https://images.unsplash.com/photo-1594909122845-11baa439b7bf?q=80&w=600');
            update_post_meta($post_id, 'banner_url', 'https://images.unsplash.com/photo-1495020689067-958852a7765e?q=80&w=1400');
            update_post_meta($post_id, 'video_url', 'https://assets.mixkit.co/videos/preview/mixkit-software-developer-working-on-his-computer-34289-large.mp4');

            $content_ids[] = $post_id;
            WP_CLI::log("Conteúdo criado: {$content['title']}");
        }
    }

    // 6. Canais
    $channels_data = [
        ['title' => 'F5 TV Ao Vivo', 'logo_text' => 'F5 TV', 'stream_url' => 'https://assets.mixkit.co/videos/preview/mixkit-software-developer-working-on-his-computer-34289-large.mp4', 'status' => 'online', 'active' => true, 'category' => 'Geral'],
        ['title' => 'F5 News', 'logo_text' => 'F5 NEWS', 'stream_url' => 'https://assets.mixkit.co/videos/preview/mixkit-camera-viewfinder-screen-recording-close-up-34304-large.mp4', 'status' => 'online', 'active' => true, 'category' => 'Jornalismo'],
        ['title' => 'F5 Esportes', 'logo_text' => 'F5 SPORTS', 'stream_url' => 'https://assets.mixkit.co/videos/preview/mixkit-stadium-lights-shining-brightly-over-the-field-28406-large.mp4', 'status' => 'online', 'active' => true, 'category' => 'Esportes'],
        ['title' => 'F5 Documentários', 'logo_text' => 'F5 DOCS', 'stream_url' => 'https://assets.mixkit.co/videos/preview/mixkit-forest-fire-burning-at-night-42284-large.mp4', 'status' => 'online', 'active' => true, 'category' => 'Documentários'],
    ];

    $channel_ids = [];
    foreach ($channels_data as $channel) {
        $post_id = wp_insert_post([
            'post_title' => $channel['title'],
            'post_content' => $channel['title'],
            'post_status' => 'publish',
            'post_type' => 'f5tv_canal',
        ]);

        if (!is_wp_error($post_id)) {
            wp_set_object_terms($post_id, [$channel['category']], 'f5tv_categoria');
            update_post_meta($post_id, 'logo_text', $channel['logo_text']);
            update_post_meta($post_id, 'status', $channel['status']);
            update_post_meta($post_id, 'active', $channel['active']);
            update_post_meta($post_id, 'stream_url', $channel['stream_url']);
            $channel_ids[] = $post_id;
            WP_CLI::log("Canal criado: {$channel['title']}");
        }
    }

    // 7. Programação
    $schedules_data = [
        ['channel_idx' => 0, 'title' => 'Jornal F5 Primeira Edição', 'host' => 'Sandro Albuquerque', 'date' => '2026-05-27', 'start' => '07:00', 'end' => '08:30', 'status' => 'ended'],
        ['channel_idx' => 0, 'title' => 'F5 Entrevista Especial', 'host' => 'Juliana Beltrão', 'date' => '2026-05-27', 'start' => '13:00', 'end' => '14:30', 'status' => 'scheduled'],
        ['channel_idx' => 0, 'title' => 'Conexão F5 Ao Vivo', 'host' => 'Sandro Albuquerque', 'date' => '2026-05-27', 'start' => '18:00', 'end' => '19:30', 'status' => 'live', 'featured' => true],
        ['channel_idx' => 0, 'title' => 'Noite F5 Acústica', 'host' => 'Mariana Lessa', 'date' => '2026-05-27', 'start' => '21:00', 'end' => '22:30', 'status' => 'scheduled'],
        ['channel_idx' => 1, 'title' => 'Jornal F5: Edição do Dia', 'host' => 'Renata Vasconcelos', 'date' => '2026-05-27', 'start' => '12:00', 'end' => '13:30', 'status' => 'rerun'],
        ['channel_idx' => 1, 'title' => 'Plantão Reforma Tributária', 'host' => 'Sandro Albuquerque', 'date' => '2026-05-27', 'start' => '18:15', 'end' => '19:45', 'status' => 'live'],
        ['channel_idx' => 2, 'title' => 'Resenha F5 Esportes', 'host' => 'Kléber Machado', 'date' => '2026-05-27', 'start' => '11:00', 'end' => '12:00', 'status' => 'ended'],
        ['channel_idx' => 2, 'title' => 'Supercopa F5 Base', 'host' => 'Narrado por Kléber Machado', 'date' => '2026-05-27', 'start' => '15:00', 'end' => '17:00', 'status' => 'premiere'],
        ['channel_idx' => 3, 'title' => 'Maratona Fronteiras da I.A.', 'host' => 'Narração de Camila Pitanga', 'date' => '2026-05-27', 'start' => '20:00', 'end' => '22:00', 'status' => 'scheduled'],
    ];

    foreach ($schedules_data as $schedule) {
        $channel_id = $channel_ids[$schedule['channel_idx']] ?? 0;
        if (!$channel_id) continue;

        $post_id = wp_insert_post([
            'post_title' => $schedule['title'],
            'post_status' => 'publish',
            'post_type' => 'f5tv_programacao',
        ]);

        if (!is_wp_error($post_id)) {
            update_post_meta($post_id, 'channel_id', $channel_id);
            update_post_meta($post_id, 'date', $schedule['date']);
            update_post_meta($post_id, 'start_time', $schedule['start'] . ':00');
            update_post_meta($post_id, 'end_time', $schedule['end'] . ':00');
            update_post_meta($post_id, 'status', $schedule['status']);
            update_post_meta($post_id, 'host', $schedule['host']);
            if (!empty($schedule['featured'])) {
                update_post_meta($post_id, 'is_featured', 1);
            }
        }
    }

    WP_CLI::log('Programações criadas.');

    // 8. Perfis, assinaturas, histórico, favoritos, dispositivos
    $subscriber_map = [
        'henrikeaps@gmail.com' => ['plan' => 'plano-premium', 'status' => 'active', 'profiles' => [
            ['name' => 'Gisele Principal', 'color' => 'bg-f5-red', 'kids' => false],
            ['name' => 'Kids F5', 'color' => 'bg-emerald-600', 'kids' => true],
            ['name' => 'Julio (Amigo)', 'color' => 'bg-indigo-600', 'kids' => false],
        ]],
        'arthur@gmail.com' => ['plan' => 'plano-basico', 'status' => 'active', 'profiles' => [
            ['name' => 'Arthur', 'color' => 'bg-f5-red', 'kids' => false],
        ]],
        'marcos@bol.com.br' => ['plan' => 'plano-familia', 'status' => 'past_due', 'profiles' => [
            ['name' => 'Marcos', 'color' => 'bg-f5-red', 'kids' => false],
        ]],
        'roberto@yahoo.com' => ['plan' => 'plano-basico', 'status' => 'canceled', 'profiles' => [
            ['name' => 'Roberto', 'color' => 'bg-f5-red', 'kids' => false],
        ]],
    ];

    foreach ($subscriber_map as $email => $data) {
        $user_id = $user_ids[$email] ?? 0;
        if (!$user_id) continue;

        update_user_meta($user_id, 'f5tv_plan', $data['plan']);
        update_user_meta($user_id, 'f5tv_subscription_status', $data['status']);

        $wpdb->replace($subs_table, [
            'user_id' => $user_id,
            'plan_id' => $data['plan'],
            'status' => $data['status'],
            'gateway' => 'woocommerce',
            'current_period_start' => current_time('mysql'),
            'current_period_end' => date('Y-m-d H:i:s', strtotime('+1 month')),
        ]);

        foreach ($data['profiles'] as $profile) {
            $wpdb->insert($profiles_table, [
                'user_id' => $user_id,
                'name' => $profile['name'],
                'avatar_color' => $profile['color'],
                'is_kids' => $profile['kids'] ? 1 : 0,
            ]);
            $profile_id = $wpdb->insert_id;

            if (!empty($content_ids)) {
                $content_id = $content_ids[array_rand($content_ids)];
                $watched = rand(60, 3600);
                $total = rand(3600, 7200);
                $progress = min(100, round(($watched / $total) * 100, 2));

                $wpdb->insert($history_table, [
                    'user_id' => $user_id,
                    'profile_id' => $profile_id,
                    'content_id' => $content_id,
                    'watched_seconds' => $watched,
                    'total_seconds' => $total,
                    'progress' => $progress,
                    'completed' => $progress >= 90 ? 1 : 0,
                ]);

                if (rand(0, 1)) {
                    $wpdb->insert($mylist_table, [
                        'user_id' => $user_id,
                        'profile_id' => $profile_id,
                        'content_id' => $content_id,
                    ]);
                }
            }

            $device_types = ['browser', 'mobile', 'smart_tv', 'desktop'];
            $device_names = ['Chrome no Windows', 'Safari no iPhone', 'Smart TV Samsung', 'Android TV Mi Box'];
            $idx = array_rand($device_types);
            $wpdb->insert($devices_table, [
                'user_id' => $user_id,
                'device_name' => $device_names[$idx],
                'device_type' => $device_types[$idx],
                'device_fingerprint' => 'fp_' . md5($user_id . $profile_id . time()),
                'user_agent' => 'Mozilla/5.0',
                'ip_address' => '192.168.1.' . rand(1, 254),
                'location' => 'São Paulo, BR',
                'last_active' => current_time('mysql'),
                'is_active' => 1,
            ]);
        }

        WP_CLI::log('Dados de assinante criados para: ' . get_userdata($user_id)->display_name);
    }

    WP_CLI::success('Migração concluída.');
}
