import { Router } from 'express';
import { query } from '../db.js';
import { requireAuth, requireRole } from '../middleware/auth.js';

const router = Router();

const contentSelect = `
  SELECT
    c.id,
    c.type,
    c.title,
    c.short_description AS "shortDescription",
    c.full_description AS "fullDescription",
    c.category_id AS "categoryId",
    cat.name AS "categoryName",
    c.genre,
    c.age_rating AS "ageRating",
    c.year,
    c.duration,
    c.cast_members AS "cast",
    c.directors,
    c.cover_url AS "coverUrl",
    c.banner_url AS "bannerUrl",
    c.trailer_url AS "trailerUrl",
    c.video_url AS "videoUrl",
    c.status,
    c.is_featured AS "isFeatured",
    c.is_free AS "isFree",
    c.is_exclusive AS "isExclusive",
    c.publish_date AS "publishDate",
    c.tags,
    c.views_count AS "viewsCount"
  FROM contents c
  LEFT JOIN categories cat ON cat.id = c.category_id
`;

router.get('/categories', async (_req, res, next) => {
  try {
    const { rows } = await query('SELECT id, name, slug FROM categories ORDER BY name ASC');
    return res.json({ categories: rows });
  } catch (error) {
    return next(error);
  }
});

router.get('/contents', async (req, res, next) => {
  try {
    const { q, type, categoryId, status = 'published' } = req.query;
    const params = [];
    const where = [];

    if (status !== 'all') {
      params.push(status);
      where.push(`c.status = $${params.length}`);
    }
    if (q) {
      params.push(`%${q}%`);
      where.push(`(c.title ILIKE $${params.length} OR c.genre ILIKE $${params.length} OR c.short_description ILIKE $${params.length})`);
    }
    if (type) {
      params.push(type);
      where.push(`c.type = $${params.length}`);
    }
    if (categoryId) {
      params.push(categoryId);
      where.push(`c.category_id = $${params.length}`);
    }

    const sql = `${contentSelect} ${where.length ? 'WHERE ' + where.join(' AND ') : ''} ORDER BY c.is_featured DESC, c.views_count DESC, c.created_at DESC`;
    const { rows } = await query(sql, params);
    return res.json({ contents: rows });
  } catch (error) {
    return next(error);
  }
});

router.get('/contents/:id', async (req, res, next) => {
  try {
    const { rows } = await query(`${contentSelect} WHERE c.id = $1 LIMIT 1`, [req.params.id]);
    if (!rows[0]) return res.status(404).json({ error: 'CONTENT_NOT_FOUND', message: 'Conteúdo não encontrado.' });

    const { rows: reviewRows } = await query(
      `SELECT id, content_id AS "contentId", profile_name AS "profileName", avatar_color AS "avatarColor", rating, comment, status, created_at AS "createdAt"
       FROM reviews WHERE content_id = $1 AND status = 'published' ORDER BY created_at DESC`,
      [req.params.id]
    );

    return res.json({ content: rows[0], reviews: reviewRows });
  } catch (error) {
    return next(error);
  }
});

router.get('/series', async (_req, res, next) => {
  try {
    const { rows } = await query('SELECT id, title, description, cover_url AS "coverUrl", banner_url AS "bannerUrl", genre, status, views_count AS "viewsCount" FROM series ORDER BY views_count DESC');
    return res.json({ series: rows });
  } catch (error) {
    return next(error);
  }
});

router.get('/series/:id/seasons', async (req, res, next) => {
  try {
    const { rows: seasons } = await query('SELECT id, series_id AS "seriesId", number, title, status FROM seasons WHERE series_id = $1 ORDER BY number ASC', [req.params.id]);
    const seasonIds = seasons.map((s) => s.id);
    let episodes = [];

    if (seasonIds.length) {
      const { rows } = await query(
        `SELECT id, season_id AS "seasonId", number, title, description, duration, video_url AS "videoUrl", thumbnail_url AS "thumbnailUrl", status, views_count AS "viewsCount"
         FROM episodes WHERE season_id = ANY($1::text[]) ORDER BY season_id, number ASC`,
        [seasonIds]
      );
      episodes = rows;
    }

    return res.json({ seasons, episodes });
  } catch (error) {
    return next(error);
  }
});

router.post('/contents', requireAuth, requireRole(['admin', 'editor']), async (req, res, next) => {
  try {
    const body = req.body || {};
    const { rows } = await query(
      `INSERT INTO contents (type, title, short_description, full_description, category_id, genre, age_rating, year, duration, cast_members, directors, cover_url, banner_url, trailer_url, video_url, status, is_featured, is_free, is_exclusive, publish_date, tags)
       VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10::jsonb,$11::jsonb,$12,$13,$14,$15,$16,$17,$18,$19,$20,$21::jsonb)
       RETURNING id, title, status`,
      [
        body.type,
        body.title,
        body.shortDescription,
        body.fullDescription,
        body.categoryId,
        body.genre,
        body.ageRating,
        body.year,
        body.duration,
        JSON.stringify(body.cast || []),
        JSON.stringify(body.directors || []),
        body.coverUrl,
        body.bannerUrl,
        body.trailerUrl || null,
        body.videoUrl || null,
        body.status || 'draft',
        Boolean(body.isFeatured),
        Boolean(body.isFree),
        body.isExclusive !== false,
        body.publishDate || null,
        JSON.stringify(body.tags || []),
      ]
    );
    return res.status(201).json({ content: rows[0] });
  } catch (error) {
    return next(error);
  }
});

export default router;
