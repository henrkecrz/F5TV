import { Router } from 'express';
import { query } from '../db.js';
import { signToken, requireAuth } from '../middleware/auth.js';

const router = Router();

function publicUser(row) {
  if (!row) return null;
  return {
    id: row.id,
    name: row.name,
    email: row.email,
    phone: row.phone,
    role: row.role,
    planId: row.plan_id,
    status: row.status,
    createdAt: row.created_at,
    lastLogin: row.last_login,
  };
}

router.post('/login', async (req, res, next) => {
  try {
    const { email } = req.body || {};
    if (!email) {
      return res.status(400).json({ error: 'EMAIL_REQUIRED', message: 'Informe o e-mail.' });
    }

    const { rows } = await query('SELECT * FROM users WHERE lower(email) = lower($1) LIMIT 1', [email]);
    const user = rows[0];

    if (!user) {
      return res.status(401).json({ error: 'INVALID_CREDENTIALS', message: 'E-mail não encontrado.' });
    }

    if (user.status === 'blocked') {
      return res.status(403).json({ error: 'USER_BLOCKED', message: 'Usuário bloqueado.' });
    }

    await query('UPDATE users SET last_login = now() WHERE id = $1', [user.id]);
    return res.json({ token: signToken(user), user: publicUser(user) });
  } catch (error) {
    return next(error);
  }
});

router.post('/register', async (req, res, next) => {
  try {
    const { name, email, phone, planId = 'plano-premium' } = req.body || {};
    if (!name || !email) {
      return res.status(400).json({ error: 'MISSING_FIELDS', message: 'Nome e e-mail são obrigatórios.' });
    }

    const { rows } = await query(
      `INSERT INTO users (name, email, phone, role, plan_id, status)
       VALUES ($1, $2, $3, 'subscriber', $4, 'pending')
       RETURNING *`,
      [name, email, phone || null, planId]
    );

    const user = rows[0];
    await query('INSERT INTO profiles (user_id, name, avatar_color) VALUES ($1, $2, $3)', [user.id, name.split(' ')[0], 'bg-red-600']);

    return res.status(201).json({ token: signToken(user), user: publicUser(user) });
  } catch (error) {
    if (error.code === '23505') {
      return res.status(409).json({ error: 'EMAIL_EXISTS', message: 'Este e-mail já está cadastrado.' });
    }
    return next(error);
  }
});

router.get('/me', requireAuth, async (req, res, next) => {
  try {
    const { rows } = await query('SELECT * FROM users WHERE id = $1 LIMIT 1', [req.user.sub]);
    return res.json({ user: publicUser(rows[0]) });
  } catch (error) {
    return next(error);
  }
});

export default router;
