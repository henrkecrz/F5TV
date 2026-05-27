-- F5 TV Streaming Platform - MySQL/MariaDB schema for Hostinger
-- Target: MySQL 8+ or MariaDB 10.6+
-- Charset: utf8mb4

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS editorial_history;
DROP TABLE IF EXISTS media_assets;
DROP TABLE IF EXISTS connected_devices;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS live_schedules;
DROP TABLE IF EXISTS channels;
DROP TABLE IF EXISTS coupons;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS favorites;
DROP TABLE IF EXISTS watch_history;
DROP TABLE IF EXISTS banners;
DROP TABLE IF EXISTS uploads;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS subscriptions;
DROP TABLE IF EXISTS episodes;
DROP TABLE IF EXISTS seasons;
DROP TABLE IF EXISTS series;
DROP TABLE IF EXISTS contents;
DROP TABLE IF EXISTS genres;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS profiles;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS plans;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE plans (
  id VARCHAR(80) PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  features JSON NOT NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,
  max_screens INT NOT NULL DEFAULT 1,
  quality VARCHAR(50) NOT NULL DEFAULT 'HD',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE users (
  id VARCHAR(80) PRIMARY KEY,
  name VARCHAR(180) NOT NULL,
  email VARCHAR(180) NOT NULL UNIQUE,
  phone VARCHAR(40),
  password_hash VARCHAR(255),
  role ENUM('admin', 'editor', 'finance', 'subscriber') NOT NULL,
  plan_id VARCHAR(80),
  status ENUM('active', 'blocked', 'pending') NOT NULL DEFAULT 'active',
  last_login DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_users_plan FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE SET NULL,
  INDEX idx_users_email (email),
  INDEX idx_users_role_status (role, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE profiles (
  id VARCHAR(80) PRIMARY KEY,
  user_id VARCHAR(80) NOT NULL,
  name VARCHAR(120) NOT NULL,
  avatar_color VARCHAR(80) NOT NULL DEFAULT 'bg-red-600',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_profiles_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_profiles_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE categories (
  id VARCHAR(80) PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(120) NOT NULL UNIQUE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE genres (
  id VARCHAR(80) PRIMARY KEY,
  name VARCHAR(120) NOT NULL UNIQUE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE contents (
  id VARCHAR(80) PRIMARY KEY,
  type ENUM('movie', 'series', 'tv_show', 'news', 'sports', 'documentary', 'special') NOT NULL,
  title VARCHAR(220) NOT NULL,
  short_description TEXT NOT NULL,
  full_description MEDIUMTEXT NOT NULL,
  category_id VARCHAR(80),
  genre VARCHAR(120) NOT NULL,
  age_rating ENUM('L', '10', '12', '14', '16', '18') NOT NULL,
  year INT NOT NULL,
  duration VARCHAR(40) NOT NULL,
  cast_members JSON NOT NULL,
  directors JSON NOT NULL,
  cover_url TEXT NOT NULL,
  banner_url TEXT NOT NULL,
  trailer_url TEXT,
  video_url TEXT,
  status ENUM('draft', 'review', 'approved', 'scheduled', 'published', 'archived') NOT NULL DEFAULT 'draft',
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  is_free TINYINT(1) NOT NULL DEFAULT 0,
  is_exclusive TINYINT(1) NOT NULL DEFAULT 1,
  publish_date DATE NULL,
  scheduled_at DATETIME NULL,
  featured_until DATETIME NULL,
  tags JSON NOT NULL,
  views_count INT NOT NULL DEFAULT 0,
  editorial_owner_id VARCHAR(80),
  internal_notes TEXT,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_contents_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
  CONSTRAINT fk_contents_editor FOREIGN KEY (editorial_owner_id) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_contents_status_type (status, type),
  INDEX idx_contents_category (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE series (
  id VARCHAR(80) PRIMARY KEY,
  title VARCHAR(220) NOT NULL,
  description MEDIUMTEXT NOT NULL,
  cover_url TEXT NOT NULL,
  banner_url TEXT NOT NULL,
  genre VARCHAR(120) NOT NULL,
  status ENUM('published', 'hidden', 'draft', 'scheduled') NOT NULL DEFAULT 'published',
  views_count INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE seasons (
  id VARCHAR(80) PRIMARY KEY,
  series_id VARCHAR(80) NOT NULL,
  number INT NOT NULL,
  title VARCHAR(180) NOT NULL,
  status ENUM('published', 'hidden', 'draft', 'scheduled') NOT NULL DEFAULT 'published',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_seasons_series FOREIGN KEY (series_id) REFERENCES series(id) ON DELETE CASCADE,
  UNIQUE KEY uq_season_number (series_id, number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE episodes (
  id VARCHAR(80) PRIMARY KEY,
  season_id VARCHAR(80) NOT NULL,
  number INT NOT NULL,
  title VARCHAR(220) NOT NULL,
  description MEDIUMTEXT NOT NULL,
  duration VARCHAR(40) NOT NULL,
  video_url TEXT,
  thumbnail_url TEXT,
  status ENUM('published', 'hidden', 'draft', 'review', 'scheduled') NOT NULL DEFAULT 'published',
  views_count INT NOT NULL DEFAULT 0,
  scheduled_at DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_episodes_season FOREIGN KEY (season_id) REFERENCES seasons(id) ON DELETE CASCADE,
  UNIQUE KEY uq_episode_number (season_id, number),
  INDEX idx_episodes_season (season_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE subscriptions (
  id VARCHAR(80) PRIMARY KEY,
  user_id VARCHAR(80) NOT NULL,
  plan_id VARCHAR(80) NOT NULL,
  status ENUM('active', 'inactive', 'canceled', 'unpaid') NOT NULL DEFAULT 'active',
  start_date DATE NOT NULL,
  next_billing_date DATE NOT NULL,
  payment_method ENUM('credit_card', 'pix', 'boleto') NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_subscriptions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_subscriptions_plan FOREIGN KEY (plan_id) REFERENCES plans(id),
  INDEX idx_subscriptions_user_status (user_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE payments (
  id VARCHAR(80) PRIMARY KEY,
  user_id VARCHAR(80) NOT NULL,
  subscription_id VARCHAR(80),
  value DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  date DATE NOT NULL,
  status ENUM('paid', 'pending', 'overdue', 'refunded', 'canceled') NOT NULL,
  payment_method ENUM('credit_card', 'pix', 'boleto') NOT NULL,
  gateway_reference VARCHAR(180),
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_payments_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_payments_subscription FOREIGN KEY (subscription_id) REFERENCES subscriptions(id) ON DELETE SET NULL,
  INDEX idx_payments_user_date (user_id, date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE uploads (
  id VARCHAR(80) PRIMARY KEY,
  file_name VARCHAR(220) NOT NULL,
  file_type VARCHAR(120) NOT NULL,
  size VARCHAR(60) NOT NULL,
  progress INT NOT NULL DEFAULT 0,
  status ENUM('uploading', 'processing', 'ready', 'error') NOT NULL DEFAULT 'uploading',
  category_id VARCHAR(80),
  title VARCHAR(220) NOT NULL,
  cover_url TEXT,
  banner_url TEXT,
  video_url TEXT,
  detected_quality VARCHAR(40),
  detected_duration VARCHAR(40),
  error_message TEXT,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_uploads_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE media_assets (
  id VARCHAR(80) PRIMARY KEY,
  upload_id VARCHAR(80),
  type ENUM('video', 'cover', 'banner', 'trailer', 'image') NOT NULL,
  title VARCHAR(220) NOT NULL,
  url TEXT NOT NULL,
  file_name VARCHAR(220),
  mime_type VARCHAR(120),
  size VARCHAR(60),
  duration VARCHAR(40),
  status ENUM('processing', 'ready', 'error') NOT NULL DEFAULT 'ready',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_media_upload FOREIGN KEY (upload_id) REFERENCES uploads(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE banners (
  id VARCHAR(80) PRIMARY KEY,
  title VARCHAR(220) NOT NULL,
  subtitle TEXT,
  image_url TEXT NOT NULL,
  cta_label VARCHAR(120),
  link_url TEXT,
  type ENUM('public', 'subscriber', 'category', 'promotion', 'live') NOT NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,
  display_order INT NOT NULL DEFAULT 1,
  starts_at DATETIME NULL,
  ends_at DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE watch_history (
  id VARCHAR(80) PRIMARY KEY,
  user_id VARCHAR(80) NOT NULL,
  content_id VARCHAR(80),
  episode_id VARCHAR(80),
  watched_percent INT NOT NULL DEFAULT 0,
  content_type VARCHAR(80) NOT NULL,
  title VARCHAR(220) NOT NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_history_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_history_content FOREIGN KEY (content_id) REFERENCES contents(id) ON DELETE CASCADE,
  CONSTRAINT fk_history_episode FOREIGN KEY (episode_id) REFERENCES episodes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE favorites (
  id VARCHAR(80) PRIMARY KEY,
  user_id VARCHAR(80) NOT NULL,
  content_id VARCHAR(80) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_favorites_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_favorites_content FOREIGN KEY (content_id) REFERENCES contents(id) ON DELETE CASCADE,
  UNIQUE KEY uq_favorite_user_content (user_id, content_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE notifications (
  id VARCHAR(80) PRIMARY KEY,
  user_id VARCHAR(80),
  title VARCHAR(220) NOT NULL,
  message TEXT NOT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE reviews (
  id VARCHAR(80) PRIMARY KEY,
  content_id VARCHAR(80) NOT NULL,
  profile_id VARCHAR(80),
  profile_name VARCHAR(120) NOT NULL,
  avatar_color VARCHAR(80) NOT NULL DEFAULT 'bg-red-600',
  rating INT NOT NULL,
  comment TEXT NOT NULL,
  status ENUM('published', 'oculta', 'denunciada') NOT NULL DEFAULT 'published',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_reviews_content FOREIGN KEY (content_id) REFERENCES contents(id) ON DELETE CASCADE,
  CONSTRAINT fk_reviews_profile FOREIGN KEY (profile_id) REFERENCES profiles(id) ON DELETE SET NULL,
  INDEX idx_reviews_content_status (content_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE coupons (
  id VARCHAR(80) PRIMARY KEY,
  code VARCHAR(80) NOT NULL UNIQUE,
  discount_type ENUM('percent', 'fixed') NOT NULL,
  discount_value DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  expires_at DATE NOT NULL,
  usage_limit INT NOT NULL DEFAULT 1,
  usage_count INT NOT NULL DEFAULT 0,
  applicable_plans JSON NOT NULL,
  status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE channels (
  id VARCHAR(80) PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  description TEXT NOT NULL,
  logo_text VARCHAR(40) NOT NULL,
  stream_url TEXT NOT NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,
  status ENUM('online', 'offline') NOT NULL DEFAULT 'online',
  category VARCHAR(120) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE live_schedules (
  id VARCHAR(80) PRIMARY KEY,
  channel_id VARCHAR(80) NOT NULL,
  title VARCHAR(220) NOT NULL,
  description TEXT NOT NULL,
  host VARCHAR(160),
  date DATE NOT NULL,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  status ENUM('live', 'premiere', 'rerun', 'scheduled', 'ended') NOT NULL DEFAULT 'scheduled',
  image_url TEXT,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_schedule_channel FOREIGN KEY (channel_id) REFERENCES channels(id) ON DELETE CASCADE,
  INDEX idx_live_schedule_channel_date (channel_id, date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE connected_devices (
  id VARCHAR(80) PRIMARY KEY,
  user_id VARCHAR(80) NOT NULL,
  device_name VARCHAR(180) NOT NULL,
  device_type ENUM('browser', 'mobile', 'smart_tv', 'desktop', 'tablet') NOT NULL,
  last_active DATETIME NOT NULL,
  location VARCHAR(180) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_devices_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_devices_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE editorial_history (
  id VARCHAR(80) PRIMARY KEY,
  entity_type ENUM('content', 'series', 'season', 'episode', 'schedule') NOT NULL,
  entity_id VARCHAR(80) NOT NULL,
  action VARCHAR(160) NOT NULL,
  actor_id VARCHAR(80),
  notes TEXT,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_editorial_actor FOREIGN KEY (actor_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
