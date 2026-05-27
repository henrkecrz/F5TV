import { Router } from 'express';
import { query } from '../db.js';
import { requireAuth, requireRole } from '../middleware/auth.js';

const router = Router();

router.get('/channels', async (_req, res, next) => {
  try {
    const { rows } = await query(
      `SELECT id, name, description, logo_text AS "logoText", stream_url AS "streamUrl", active, status, category
       FROM channels ORDER BY name ASC`
    );
    return res.json({ channels: rows });
  } catch (error) {
    return next(error);
  }
});

router.get('/schedule', async (req, res, next) => {
  try {
    const { date, channelId } = req.query;
    const params = [];
    const where = [];

    if (date) {
      params.push(date);
      where.push(`ls.date = $${params.length}`);
    }
    if (channelId) {
      params.push(channelId);
      where.push(`ls.channel_id = $${params.length}`);
    }

    const { rows } = await query(
      `SELECT ls.id, ls.channel_id AS "channelId", ch.name AS "channelName", ls.title, ls.description, ls.host,
              ls.date, to_char(ls.start_time, 'HH24:MI') AS "startTime", to_char(ls.end_time, 'HH24:MI') AS "endTime",
              ls.status, ls.image_url AS "imageUrl", ls.is_featured AS "isFeatured"
       FROM live_schedules ls
       JOIN channels ch ON ch.id = ls.channel_id
       ${where.length ? 'WHERE ' + where.join(' AND ') : ''}
       ORDER BY ls.date ASC, ls.start_time ASC`,
      params
    );
    return res.json({ schedule: rows });
  } catch (error) {
    return next(error);
  }
});

router.post('/channels', requireAuth, requireRole(['admin', 'editor']), async (req, res, next) => {
  try {
    const { name, description, logoText, streamUrl, category, active = true, status = 'online' } = req.body || {};
    const { rows } = await query(
      `INSERT INTO channels (name, description, logo_text, stream_url, active, status, category)
       VALUES ($1,$2,$3,$4,$5,$6,$7)
       RETURNING id, name, description, logo_text AS "logoText", stream_url AS "streamUrl", active, status, category`,
      [name, description, logoText, streamUrl, active, status, category]
    );
    return res.status(201).json({ channel: rows[0] });
  } catch (error) {
    return next(error);
  }
});

router.post('/schedule', requireAuth, requireRole(['admin', 'editor']), async (req, res, next) => {
  try {
    const { channelId, title, description, host, date, startTime, endTime, status = 'scheduled', imageUrl, isFeatured = false } = req.body || {};
    const { rows } = await query(
      `INSERT INTO live_schedules (channel_id, title, description, host, date, start_time, end_time, status, image_url, is_featured)
       VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10)
       RETURNING id, channel_id AS "channelId", title, description, host, date, to_char(start_time, 'HH24:MI') AS "startTime", to_char(end_time, 'HH24:MI') AS "endTime", status, image_url AS "imageUrl", is_featured AS "isFeatured"`,
      [channelId, title, description, host || null, date, startTime, endTime, status, imageUrl || null, isFeatured]
    );
    return res.status(201).json({ schedule: rows[0] });
  } catch (error) {
    return next(error);
  }
});

export default router;
