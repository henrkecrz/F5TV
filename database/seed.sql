-- F5 TV Streaming Platform - seed data
-- Run after database/schema.sql.

INSERT INTO plans (id, name, price, features, active, max_screens, quality) VALUES
('plano-basico', 'Básico', 19.90, '["Catálogo sob demanda", "1 tela", "Qualidade HD"]', true, 1, 'HD'),
('plano-familia', 'Família', 34.90, '["Catálogo completo", "3 telas", "Full HD", "Ao vivo"]', true, 3, 'Full HD'),
('plano-premium', 'Premium', 49.90, '["Catálogo completo", "5 telas", "Full HD/4K", "Ao vivo", "Estreias e especiais"]', true, 5, '4K');

INSERT INTO users (id, name, email, phone, role, plan_id, status, password_hash) VALUES
('user-admin', 'Administrador F5', 'admin@f5tv.com.br', '(11) 90000-0001', 'admin', null, 'active', '$2a$10$mock.hash.demo'),
('user-editor', 'Editor de Conteúdo', 'editor@f5tv.com.br', '(11) 90000-0002', 'editor', null, 'active', '$2a$10$mock.hash.demo'),
('user-finance', 'Diretor Financeiro', 'financeiro@f5tv.com.br', '(11) 90000-0003', 'finance', null, 'active', '$2a$10$mock.hash.demo'),
('user-assinante', 'Henrique Vasconcelos', 'henrikeaps@gmail.com', '(11) 99999-9999', 'subscriber', 'plano-premium', 'active', '$2a$10$mock.hash.demo');

INSERT INTO profiles (id, user_id, name, avatar_color) VALUES
('prof-henrique', 'user-assinante', 'Henrique', 'bg-red-600');

INSERT INTO categories (id, name, slug) VALUES
('cat-jornalismo', 'Jornalismo', 'jornalismo'),
('cat-series', 'Séries', 'series'),
('cat-documentarios', 'Documentários', 'documentarios'),
('cat-esportes', 'Esportes', 'esportes'),
('cat-entretenimento', 'Entretenimento', 'entretenimento'),
('cat-especiais', 'Especiais', 'especiais');

INSERT INTO contents (id, type, title, short_description, full_description, category_id, genre, age_rating, year, duration, cast_members, directors, cover_url, banner_url, trailer_url, video_url, status, is_featured, is_free, is_exclusive, publish_date, tags, views_count) VALUES
('content-jornal-f5', 'news', 'Jornal F5', 'As principais notícias do dia.', 'Cobertura diária com análise, entrevistas e atualização em tempo real.', 'cat-jornalismo', 'Notícias', '10', 2026, '45m', '["Equipe F5"]', '["Redação F5"]', 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?q=80&w=600', 'https://images.unsplash.com/photo-1495020689067-958852a7765e?q=80&w=1400', null, 'https://assets.mixkit.co/videos/preview/mixkit-news-studio-background-screen-3045-large.mp4', 'published', true, false, true, CURRENT_DATE, '["jornalismo", "ao vivo", "notícias"]', 2450),
('content-conexao-f5', 'series', 'Conexão F5', 'Série original sobre tecnologia, poder e sociedade.', 'Uma série documental sobre bastidores da inovação, segurança digital e os impactos da tecnologia no Brasil.', 'cat-series', 'Tecnologia', '12', 2026, '50m', '["Equipe F5 Docs"]', '["Produção F5"]', 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=600', 'https://images.unsplash.com/photo-1518709268805-4e9042af2176?q=80&w=1400', null, 'https://assets.mixkit.co/videos/preview/mixkit-hands-of-a-hacker-typing-code-on-a-keyboard-34293-large.mp4', 'published', true, false, true, CURRENT_DATE, '["série", "tecnologia", "documental"]', 3210),
('content-vozes-brasil', 'documentary', 'Vozes do Brasil', 'Histórias reais de transformação.', 'Documentário especial da F5 TV com personagens, entrevistas e cenas de impacto social.', 'cat-documentarios', 'Sociedade', '10', 2026, '1h 12m', '["Convidados especiais"]', '["F5 Documentários"]', 'https://images.unsplash.com/photo-1529107386315-e1a2ed48a620?q=80&w=600', 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?q=80&w=1400', null, 'https://assets.mixkit.co/videos/preview/mixkit-group-of-people-walking-through-a-city-square-4436-large.mp4', 'published', false, false, true, CURRENT_DATE, '["documentário", "brasil", "histórias"]', 1805);

INSERT INTO series (id, title, description, cover_url, banner_url, genre, status, views_count) VALUES
('series-conexao-f5', 'Conexão F5', 'Série original sobre tecnologia, poder e sociedade.', 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=600', 'https://images.unsplash.com/photo-1518709268805-4e9042af2176?q=80&w=1400', 'Tecnologia', 'published', 3210);

INSERT INTO seasons (id, series_id, number, title, status) VALUES
('season-conexao-1', 'series-conexao-f5', 1, 'Temporada 1', 'published');

INSERT INTO episodes (id, season_id, number, title, description, duration, video_url, thumbnail_url, status, views_count) VALUES
('ep-conexao-1', 'season-conexao-1', 1, 'Fronteira da I.A.', 'O impacto da inteligência artificial nos próximos anos.', '48m', 'https://assets.mixkit.co/videos/preview/mixkit-hands-of-a-hacker-typing-code-on-a-keyboard-34293-large.mp4', 'https://images.unsplash.com/photo-1677442136019-21780ecad995?q=80&w=600', 'published', 1420),
('ep-conexao-2', 'season-conexao-1', 2, 'Rastreadores Digitais', 'Investigação sobre segurança, dados e privacidade.', '51m', 'https://assets.mixkit.co/videos/preview/mixkit-software-developer-working-on-his-computer-34289-large.mp4', 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?q=80&w=600', 'published', 1180);

INSERT INTO subscriptions (id, user_id, plan_id, status, start_date, next_billing_date, payment_method) VALUES
('sub-henrique-premium', 'user-assinante', 'plano-premium', 'active', CURRENT_DATE, CURRENT_DATE + INTERVAL '30 days', 'pix');

INSERT INTO payments (id, user_id, subscription_id, value, date, status, payment_method) VALUES
('pay-henrique-001', 'user-assinante', 'sub-henrique-premium', 49.90, CURRENT_DATE, 'paid', 'pix');

INSERT INTO channels (id, name, description, logo_text, stream_url, active, status, category) VALUES
('channel-f5tv', 'F5 TV Ao Vivo', 'Canal principal com programação da emissora em tempo real.', 'F5', 'https://assets.mixkit.co/videos/preview/mixkit-news-studio-background-screen-3045-large.mp4', true, 'online', 'Geral'),
('channel-f5news', 'F5 News', 'Jornalismo, entrevistas e análise ao vivo.', 'NEWS', 'https://assets.mixkit.co/videos/preview/mixkit-business-people-in-a-meeting-2868-large.mp4', true, 'online', 'Jornalismo'),
('channel-f5sports', 'F5 Esportes', 'Coberturas esportivas, debates e especiais.', 'SPORT', 'https://assets.mixkit.co/videos/preview/mixkit-people-in-a-stadium-cheering-4506-large.mp4', true, 'online', 'Esportes');

INSERT INTO live_schedules (id, channel_id, title, description, host, date, start_time, end_time, status, image_url, is_featured) VALUES
('schedule-jornal-f5-live', 'channel-f5tv', 'Jornal F5', 'Notícias, análise e atualizações do dia.', 'Redação F5', CURRENT_DATE, '18:00', '19:00', 'live', 'https://images.unsplash.com/photo-1495020689067-958852a7765e?q=80&w=1400', true),
('schedule-f5-entrevista', 'channel-f5tv', 'F5 Entrevista', 'Conversa especial com convidados da semana.', 'Equipe F5', CURRENT_DATE, '20:00', '21:00', 'scheduled', 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?q=80&w=1400', false),
('schedule-f5-esportes', 'channel-f5sports', 'F5 Esportes Debate', 'Mesa redonda e análise das rodadas.', 'F5 Esportes', CURRENT_DATE, '21:00', '22:00', 'scheduled', 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?q=80&w=1400', false);

INSERT INTO coupons (id, code, discount_type, discount_value, expires_at, usage_limit, usage_count, applicable_plans, status) VALUES
('coupon-bemvindo', 'F5BEMVINDO', 'percent', 15, CURRENT_DATE + INTERVAL '90 days', 500, 0, '["plano-basico", "plano-familia", "plano-premium"]', 'active'),
('coupon-premium', 'F5PREMIUM', 'fixed', 10, CURRENT_DATE + INTERVAL '60 days', 200, 0, '["plano-premium"]', 'active');

INSERT INTO connected_devices (id, user_id, device_name, device_type, last_active, location, is_active) VALUES
('device-henrique-chrome', 'user-assinante', 'Chrome no Windows', 'desktop', now(), 'São Paulo, BR', true),
('device-henrique-tv', 'user-assinante', 'Smart TV Samsung', 'smart_tv', now() - INTERVAL '2 days', 'Sala principal', true);

INSERT INTO reviews (id, content_id, profile_id, profile_name, avatar_color, rating, comment, status) VALUES
('review-conexao-1', 'content-conexao-f5', 'prof-henrique', 'Henrique', 'bg-red-600', 5, 'Produção excelente e tema muito atual.', 'published');
