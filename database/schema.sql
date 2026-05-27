-- F5 TV Streaming Platform - PostgreSQL schema
-- Target: PostgreSQL 14+
-- This schema turns the current mock/localStorage MVP into a database-backed product foundation.

CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- Drop order is intentionally explicit for local reset workflows.
DROP TABLE IF EXISTS editorial_history CASCADE;
DROP TABLE IF EXISTS media_assets CASCADE;
DROP TABLE IF EXISTS connected_devices CASCADE;
DROP TABLE IF EXISTS reviews CASCADE;
DROP TABLE IF EXISTS live_schedules CASCADE;
DROP TABLE IF EXISTS channels CASCADE;
DROP TABLE IF EXISTS coupons CASCADE;
DROP TABLE IF EXISTS notifications CASCADE;
DROP TABLE IF EXISTS favorites CASCADE;
DROP TABLE IF EXISTS watch_history CASCADE;
DROP TABLE IF EXISTS banners CASCADE;
DROP TABLE IF EXISTS uploads CASCADE;
DROP TABLE IF EXISTS payments CASCADE;
DROP TABLE IF EXISTS subscriptions CASCADE;
DROP TABLE IF EXISTS episodes CASCADE;
DROP TABLE IF EXISTS seasons CASCADE;
DROP TABLE IF EXISTS series CASCADE;
DROP TABLE IF EXISTS contents CASCADE;
DROP TABLE IF EXISTS genres CASCADE;
DROP TABLE IF EXISTS categories CASCADE;
DROP TABLE IF EXISTS profiles CASCADE;
DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS plans CASCADE;

CREATE TABLE plans (
  id TEXT PRIMARY KEY,
  name TEXT NOT NULL,
  price NUMERIC(10,2) NOT NULL CHECK (price >= 0),
  features JSONB NOT NULL DEFAULT '[]'::jsonb,
  active BOOLEAN NOT NULL DEFAULT TRUE,
  max_screens INTEGER NOT NULL DEFAULT 1,
  quality TEXT NOT NULL DEFAULT 'HD',
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE users (
  id TEXT PRIMARY KEY DEFAULT 'user-' || encode(gen_random_bytes(8), 'hex'),
  name TEXT NOT NULL,
  email TEXT NOT NULL UNIQUE,
  phone TEXT,
  password_hash TEXT,
  role TEXT NOT NULL CHECK (role IN ('admin', 'editor', 'finance', 'subscriber')),
  plan_id TEXT REFERENCES plans(id) ON DELETE SET NULL,
  status TEXT NOT NULL DEFAULT 'active' CHECK (status IN ('active', 'blocked', 'pending')),
  last_login TIMESTAMPTZ,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE profiles (
  id TEXT PRIMARY KEY DEFAULT 'prof-' || encode(gen_random_bytes(8), 'hex'),
  user_id TEXT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  name TEXT NOT NULL,
  avatar_color TEXT NOT NULL DEFAULT 'bg-red-600',
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE categories (
  id TEXT PRIMARY KEY,
  name TEXT NOT NULL,
  slug TEXT NOT NULL UNIQUE,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE genres (
  id TEXT PRIMARY KEY DEFAULT 'genre-' || encode(gen_random_bytes(8), 'hex'),
  name TEXT NOT NULL UNIQUE,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE contents (
  id TEXT PRIMARY KEY DEFAULT 'content-' || encode(gen_random_bytes(8), 'hex'),
  type TEXT NOT NULL CHECK (type IN ('movie', 'series', 'tv_show', 'news', 'sports', 'documentary', 'special')),
  title TEXT NOT NULL,
  short_description TEXT NOT NULL,
  full_description TEXT NOT NULL,
  category_id TEXT REFERENCES categories(id) ON DELETE SET NULL,
  genre TEXT NOT NULL,
  age_rating TEXT NOT NULL CHECK (age_rating IN ('L', '10', '12', '14', '16', '18')),
  year INTEGER NOT NULL,
  duration TEXT NOT NULL,
  cast_members JSONB NOT NULL DEFAULT '[]'::jsonb,
  directors JSONB NOT NULL DEFAULT '[]'::jsonb,
  cover_url TEXT NOT NULL,
  banner_url TEXT NOT NULL,
  trailer_url TEXT,
  video_url TEXT,
  status TEXT NOT NULL DEFAULT 'draft' CHECK (status IN ('draft', 'review', 'approved', 'scheduled', 'published', 'archived')),
  is_featured BOOLEAN NOT NULL DEFAULT FALSE,
  is_free BOOLEAN NOT NULL DEFAULT FALSE,
  is_exclusive BOOLEAN NOT NULL DEFAULT TRUE,
  publish_date DATE,
  scheduled_at TIMESTAMPTZ,
  featured_until TIMESTAMPTZ,
  tags JSONB NOT NULL DEFAULT '[]'::jsonb,
  views_count INTEGER NOT NULL DEFAULT 0,
  editorial_owner_id TEXT REFERENCES users(id) ON DELETE SET NULL,
  internal_notes TEXT,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE series (
  id TEXT PRIMARY KEY DEFAULT 'series-' || encode(gen_random_bytes(8), 'hex'),
  title TEXT NOT NULL,
  description TEXT NOT NULL,
  cover_url TEXT NOT NULL,
  banner_url TEXT NOT NULL,
  genre TEXT NOT NULL,
  status TEXT NOT NULL DEFAULT 'published' CHECK (status IN ('published', 'hidden', 'draft', 'scheduled')),
  views_count INTEGER NOT NULL DEFAULT 0,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE seasons (
  id TEXT PRIMARY KEY DEFAULT 'season-' || encode(gen_random_bytes(8), 'hex'),
  series_id TEXT NOT NULL REFERENCES series(id) ON DELETE CASCADE,
  number INTEGER NOT NULL CHECK (number > 0),
  title TEXT NOT NULL,
  status TEXT NOT NULL DEFAULT 'published' CHECK (status IN ('published', 'hidden', 'draft', 'scheduled')),
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  UNIQUE(series_id, number)
);

CREATE TABLE episodes (
  id TEXT PRIMARY KEY DEFAULT 'ep-' || encode(gen_random_bytes(8), 'hex'),
  season_id TEXT NOT NULL REFERENCES seasons(id) ON DELETE CASCADE,
  number INTEGER NOT NULL CHECK (number > 0),
  title TEXT NOT NULL,
  description TEXT NOT NULL,
  duration TEXT NOT NULL,
  video_url TEXT,
  thumbnail_url TEXT,
  status TEXT NOT NULL DEFAULT 'published' CHECK (status IN ('published', 'hidden', 'draft', 'review', 'scheduled')),
  views_count INTEGER NOT NULL DEFAULT 0,
  scheduled_at TIMESTAMPTZ,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  UNIQUE(season_id, number)
);

CREATE TABLE subscriptions (
  id TEXT PRIMARY KEY DEFAULT 'sub-' || encode(gen_random_bytes(8), 'hex'),
  user_id TEXT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  plan_id TEXT NOT NULL REFERENCES plans(id),
  status TEXT NOT NULL DEFAULT 'active' CHECK (status IN ('active', 'inactive', 'canceled', 'unpaid')),
  start_date DATE NOT NULL DEFAULT CURRENT_DATE,
  next_billing_date DATE NOT NULL,
  payment_method TEXT NOT NULL CHECK (payment_method IN ('credit_card', 'pix', 'boleto')),
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE payments (
  id TEXT PRIMARY KEY DEFAULT 'pay-' || encode(gen_random_bytes(8), 'hex'),
  user_id TEXT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  subscription_id TEXT REFERENCES subscriptions(id) ON DELETE SET NULL,
  value NUMERIC(10,2) NOT NULL CHECK (value >= 0),
  date DATE NOT NULL DEFAULT CURRENT_DATE,
  status TEXT NOT NULL CHECK (status IN ('paid', 'pending', 'overdue', 'refunded', 'canceled')),
  payment_method TEXT NOT NULL CHECK (payment_method IN ('credit_card', 'pix', 'boleto')),
  gateway_reference TEXT,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE uploads (
  id TEXT PRIMARY KEY DEFAULT 'upload-' || encode(gen_random_bytes(8), 'hex'),
  file_name TEXT NOT NULL,
  file_type TEXT NOT NULL,
  size TEXT NOT NULL,
  progress INTEGER NOT NULL DEFAULT 0 CHECK (progress BETWEEN 0 AND 100),
  status TEXT NOT NULL DEFAULT 'uploading' CHECK (status IN ('uploading', 'processing', 'ready', 'error')),
  category_id TEXT REFERENCES categories(id) ON DELETE SET NULL,
  title TEXT NOT NULL,
  cover_url TEXT,
  banner_url TEXT,
  video_url TEXT,
  detected_quality TEXT,
  detected_duration TEXT,
  error_message TEXT,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE media_assets (
  id TEXT PRIMARY KEY DEFAULT 'media-' || encode(gen_random_bytes(8), 'hex'),
  upload_id TEXT REFERENCES uploads(id) ON DELETE SET NULL,
  type TEXT NOT NULL CHECK (type IN ('video', 'cover', 'banner', 'trailer', 'image')),
  title TEXT NOT NULL,
  url TEXT NOT NULL,
  file_name TEXT,
  mime_type TEXT,
  size TEXT,
  duration TEXT,
  status TEXT NOT NULL DEFAULT 'ready' CHECK (status IN ('processing', 'ready', 'error')),
  created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE banners (
  id TEXT PRIMARY KEY DEFAULT 'banner-' || encode(gen_random_bytes(8), 'hex'),
  title TEXT NOT NULL,
  subtitle TEXT,
  image_url TEXT NOT NULL,
  cta_label TEXT,
  link_url TEXT,
  type TEXT NOT NULL CHECK (type IN ('public', 'subscriber', 'category', 'promotion', 'live')),
  active BOOLEAN NOT NULL DEFAULT TRUE,
  display_order INTEGER NOT NULL DEFAULT 1,
  starts_at TIMESTAMPTZ,
  ends_at TIMESTAMPTZ,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE watch_history (
  id TEXT PRIMARY KEY DEFAULT 'hist-' || encode(gen_random_bytes(8), 'hex'),
  user_id TEXT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  content_id TEXT REFERENCES contents(id) ON DELETE CASCADE,
  episode_id TEXT REFERENCES episodes(id) ON DELETE CASCADE,
  watched_percent INTEGER NOT NULL DEFAULT 0 CHECK (watched_percent BETWEEN 0 AND 100),
  content_type TEXT NOT NULL,
  title TEXT NOT NULL,
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE favorites (
  id TEXT PRIMARY KEY DEFAULT 'fav-' || encode(gen_random_bytes(8), 'hex'),
  user_id TEXT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  content_id TEXT NOT NULL REFERENCES contents(id) ON DELETE CASCADE,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  UNIQUE(user_id, content_id)
);

CREATE TABLE notifications (
  id TEXT PRIMARY KEY DEFAULT 'notif-' || encode(gen_random_bytes(8), 'hex'),
  user_id TEXT REFERENCES users(id) ON DELETE CASCADE,
  title TEXT NOT NULL,
  message TEXT NOT NULL,
  read BOOLEAN NOT NULL DEFAULT FALSE,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE reviews (
  id TEXT PRIMARY KEY DEFAULT 'review-' || encode(gen_random_bytes(8), 'hex'),
  content_id TEXT NOT NULL REFERENCES contents(id) ON DELETE CASCADE,
  profile_id TEXT REFERENCES profiles(id) ON DELETE SET NULL,
  profile_name TEXT NOT NULL,
  avatar_color TEXT NOT NULL DEFAULT 'bg-red-600',
  rating INTEGER NOT NULL CHECK (rating BETWEEN 1 AND 5),
  comment TEXT NOT NULL,
  status TEXT NOT NULL DEFAULT 'published' CHECK (status IN ('published', 'oculta', 'denunciada')),
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE coupons (
  id TEXT PRIMARY KEY DEFAULT 'coupon-' || encode(gen_random_bytes(8), 'hex'),
  code TEXT NOT NULL UNIQUE,
  discount_type TEXT NOT NULL CHECK (discount_type IN ('percent', 'fixed')),
  discount_value NUMERIC(10,2) NOT NULL CHECK (discount_value >= 0),
  expires_at DATE NOT NULL,
  usage_limit INTEGER NOT NULL DEFAULT 1 CHECK (usage_limit >= 0),
  usage_count INTEGER NOT NULL DEFAULT 0 CHECK (usage_count >= 0),
  applicable_plans JSONB NOT NULL DEFAULT '[]'::jsonb,
  status TEXT NOT NULL DEFAULT 'active' CHECK (status IN ('active', 'inactive')),
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE channels (
  id TEXT PRIMARY KEY DEFAULT 'channel-' || encode(gen_random_bytes(8), 'hex'),
  name TEXT NOT NULL,
  description TEXT NOT NULL,
  logo_text TEXT NOT NULL,
  stream_url TEXT NOT NULL,
  active BOOLEAN NOT NULL DEFAULT TRUE,
  status TEXT NOT NULL DEFAULT 'online' CHECK (status IN ('online', 'offline')),
  category TEXT NOT NULL,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE live_schedules (
  id TEXT PRIMARY KEY DEFAULT 'schedule-' || encode(gen_random_bytes(8), 'hex'),
  channel_id TEXT NOT NULL REFERENCES channels(id) ON DELETE CASCADE,
  title TEXT NOT NULL,
  description TEXT NOT NULL,
  host TEXT,
  date DATE NOT NULL,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  status TEXT NOT NULL DEFAULT 'scheduled' CHECK (status IN ('live', 'premiere', 'rerun', 'scheduled', 'ended')),
  image_url TEXT,
  is_featured BOOLEAN NOT NULL DEFAULT FALSE,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE connected_devices (
  id TEXT PRIMARY KEY DEFAULT 'device-' || encode(gen_random_bytes(8), 'hex'),
  user_id TEXT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
  device_name TEXT NOT NULL,
  device_type TEXT NOT NULL CHECK (device_type IN ('browser', 'mobile', 'smart_tv', 'desktop', 'tablet')),
  last_active TIMESTAMPTZ NOT NULL DEFAULT now(),
  location TEXT NOT NULL,
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE editorial_history (
  id TEXT PRIMARY KEY DEFAULT 'edit-' || encode(gen_random_bytes(8), 'hex'),
  entity_type TEXT NOT NULL CHECK (entity_type IN ('content', 'series', 'season', 'episode', 'schedule')),
  entity_id TEXT NOT NULL,
  action TEXT NOT NULL,
  actor_id TEXT REFERENCES users(id) ON DELETE SET NULL,
  notes TEXT,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role_status ON users(role, status);
CREATE INDEX idx_contents_status_type ON contents(status, type);
CREATE INDEX idx_contents_category ON contents(category_id);
CREATE INDEX idx_episodes_season ON episodes(season_id);
CREATE INDEX idx_payments_user_date ON payments(user_id, date DESC);
CREATE INDEX idx_subscriptions_user_status ON subscriptions(user_id, status);
CREATE INDEX idx_live_schedules_channel_date ON live_schedules(channel_id, date);
CREATE INDEX idx_reviews_content_status ON reviews(content_id, status);
CREATE INDEX idx_devices_user ON connected_devices(user_id);

CREATE OR REPLACE FUNCTION touch_updated_at()
RETURNS TRIGGER AS $$
BEGIN
  NEW.updated_at = now();
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER touch_plans BEFORE UPDATE ON plans FOR EACH ROW EXECUTE FUNCTION touch_updated_at();
CREATE TRIGGER touch_users BEFORE UPDATE ON users FOR EACH ROW EXECUTE FUNCTION touch_updated_at();
CREATE TRIGGER touch_profiles BEFORE UPDATE ON profiles FOR EACH ROW EXECUTE FUNCTION touch_updated_at();
CREATE TRIGGER touch_contents BEFORE UPDATE ON contents FOR EACH ROW EXECUTE FUNCTION touch_updated_at();
CREATE TRIGGER touch_series BEFORE UPDATE ON series FOR EACH ROW EXECUTE FUNCTION touch_updated_at();
CREATE TRIGGER touch_seasons BEFORE UPDATE ON seasons FOR EACH ROW EXECUTE FUNCTION touch_updated_at();
CREATE TRIGGER touch_episodes BEFORE UPDATE ON episodes FOR EACH ROW EXECUTE FUNCTION touch_updated_at();
CREATE TRIGGER touch_subscriptions BEFORE UPDATE ON subscriptions FOR EACH ROW EXECUTE FUNCTION touch_updated_at();
CREATE TRIGGER touch_uploads BEFORE UPDATE ON uploads FOR EACH ROW EXECUTE FUNCTION touch_updated_at();
CREATE TRIGGER touch_banners BEFORE UPDATE ON banners FOR EACH ROW EXECUTE FUNCTION touch_updated_at();
CREATE TRIGGER touch_reviews BEFORE UPDATE ON reviews FOR EACH ROW EXECUTE FUNCTION touch_updated_at();
CREATE TRIGGER touch_coupons BEFORE UPDATE ON coupons FOR EACH ROW EXECUTE FUNCTION touch_updated_at();
CREATE TRIGGER touch_channels BEFORE UPDATE ON channels FOR EACH ROW EXECUTE FUNCTION touch_updated_at();
CREATE TRIGGER touch_live_schedules BEFORE UPDATE ON live_schedules FOR EACH ROW EXECUTE FUNCTION touch_updated_at();
