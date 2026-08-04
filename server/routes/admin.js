import { Router } from 'express';
import { query } from '../db.js';
import { requireAuth, requireRole } from '../middleware/auth.js';

const router = Router();

router.use(requireAuth, requireRole(['admin', 'editor', 'finance']));

router.get('/dashboard', async (_req, res, next) => {
  try {
    const [users, payments, contents, uploads, live] = await Promise.all([
      query(`SELECT
        COUNT(*) FILTER (WHERE role = 'subscriber')::int AS "totalSubscribers",
        COUNT(*) FILTER (WHERE role = 'subscriber' AND status = 'active')::int AS "activeSubscribers",
        COUNT(*) FILTER (WHERE role = 'subscriber' AND status = 'pending')::int AS "pendingSubscribers"
       FROM users`),
      query(`SELECT
        COALESCE(SUM(value) FILTER (WHERE status = 'paid'), 0)::numeric AS "totalRevenue",
        COALESCE(SUM(value) FILTER (WHERE status = 'paid' AND date >= date_trunc('month', CURRENT_DATE)), 0)::numeric AS "monthlyRevenue"
       FROM payments`),
      query(`SELECT COUNT(*)::int AS "publishedContents" FROM contents WHERE status = 'published'`),
      query(`SELECT COUNT(*)::int AS "readyUploads" FROM uploads WHERE status = 'ready'`),
      query(`SELECT COUNT(*)::int AS "livePrograms" FROM live_schedules WHERE status = 'live'`),
    ]);

    return res.json({
      metrics: {
        ...users.rows[0],
        ...payments.rows[0],
        ...contents.rows[0],
        ...uploads.rows[0],
        ...live.rows[0],
      },
    });
  } catch (error) {
    return next(error);
  }
});

router.get('/users', requireRole(['admin', 'finance']), async (req, res, next) => {
  try {
    const { role, status } = req.query;
    const params = [];
    const where = [];
    if (role) {
      params.push(role);
      where.push(`role = $${params.length}`);
    }
    if (status) {
      params.push(status);
      where.push(`status = $${params.length}`);
    }

    const { rows } = await query(
      `SELECT id, name, email, phone, role, plan_id AS "planId", status, created_at AS "createdAt", last_login AS "lastLogin"
       FROM users ${where.length ? 'WHERE ' + where.join(' AND ') : ''} ORDER BY created_at DESC`,
      params
    );
    return res.json({ users: rows });
  } catch (error) {
    return next(error);
  }
});

router.patch('/users/:id/status', requireRole(['admin', 'finance']), async (req, res, next) => {
  try {
    const { status } = req.body || {};
    const { rows } = await query(
      `UPDATE users SET status = $1 WHERE id = $2 RETURNING id, name, email, role, status`,
      [status, req.params.id]
    );
    return res.json({ user: rows[0] });
  } catch (error) {
    return next(error);
  }
});

router.get('/media', requireRole(['admin', 'editor']), async (_req, res, next) => {
  try {
    const { rows } = await query(
      `SELECT id, type, title, url, file_name AS "fileName", mime_type AS "mimeType", size, duration, status, created_at AS "createdAt"
       FROM media_assets ORDER BY created_at DESC`
    );
    return res.json({ media: rows });
  } catch (error) {
    return next(error);
  }
});

router.post('/media', requireRole(['admin', 'editor']), async (req, res, next) => {
  try {
    const { type, title, url, fileName, mimeType, size, duration, status = 'ready' } = req.body || {};
    const { rows } = await query(
      `INSERT INTO media_assets (type, title, url, file_name, mime_type, size, duration, status)
       VALUES ($1,$2,$3,$4,$5,$6,$7,$8)
       RETURNING id, type, title, url, file_name AS "fileName", mime_type AS "mimeType", size, duration, status`,
      [type, title, url, fileName || null, mimeType || null, size || null, duration || null, status]
    );
    return res.status(201).json({ media: rows[0] });
  } catch (error) {
    return next(error);
  }
});

router.get('/reviews', requireRole(['admin', 'editor']), async (_req, res, next) => {
  try {
    const { rows } = await query(
      `SELECT r.id, r.content_id AS "contentId", c.title AS "contentTitle", r.profile_name AS "profileName", r.rating, r.comment, r.status, r.created_at AS "createdAt"
       FROM reviews r
       JOIN contents c ON c.id = r.content_id
       ORDER BY r.created_at DESC`
    );
    return res.json({ reviews: rows });
  } catch (error) {
    return next(error);
  }
});

router.patch('/reviews/:id/status', requireRole(['admin', 'editor']), async (req, res, next) => {
  try {
    const { status } = req.body || {};
    const { rows } = await query('UPDATE reviews SET status = $1 WHERE id = $2 RETURNING id, status', [status, req.params.id]);
    return res.json({ review: rows[0] });
  } catch (error) {
    return next(error);
  }
});

export default router;
