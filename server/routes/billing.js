import { Router } from 'express';
import { query, withTransaction } from '../db.js';
import { requireAuth, requireRole } from '../middleware/auth.js';

const router = Router();

router.get('/plans', async (_req, res, next) => {
  try {
    const { rows } = await query('SELECT id, name, price, features, active, max_screens AS "maxScreens", quality FROM plans ORDER BY price ASC');
    return res.json({ plans: rows });
  } catch (error) {
    return next(error);
  }
});

router.get('/coupons', requireAuth, requireRole(['admin', 'finance']), async (_req, res, next) => {
  try {
    const { rows } = await query(
      `SELECT id, code, discount_type AS "discountType", discount_value AS "discountValue", expires_at AS "expiresAt",
              usage_limit AS "usageLimit", usage_count AS "usageCount", applicable_plans AS "applicablePlans", status
       FROM coupons ORDER BY created_at DESC`
    );
    return res.json({ coupons: rows });
  } catch (error) {
    return next(error);
  }
});

router.post('/coupons/validate', async (req, res, next) => {
  try {
    const { code, planId } = req.body || {};
    const { rows } = await query(
      `SELECT id, code, discount_type AS "discountType", discount_value AS "discountValue", expires_at AS "expiresAt",
              usage_limit AS "usageLimit", usage_count AS "usageCount", applicable_plans AS "applicablePlans", status
       FROM coupons WHERE upper(code) = upper($1) LIMIT 1`,
      [code]
    );

    const coupon = rows[0];
    if (!coupon) return res.status(404).json({ valid: false, message: 'Cupom não encontrado.' });
    if (coupon.status !== 'active') return res.status(400).json({ valid: false, message: 'Cupom inativo.' });
    if (new Date(coupon.expiresAt) < new Date()) return res.status(400).json({ valid: false, message: 'Cupom expirado.' });
    if (coupon.usageCount >= coupon.usageLimit) return res.status(400).json({ valid: false, message: 'Limite de usos esgotado.' });
    if (coupon.applicablePlans?.length && planId && !coupon.applicablePlans.includes(planId)) {
      return res.status(400).json({ valid: false, message: 'Cupom não aplicável ao plano selecionado.' });
    }

    return res.json({ valid: true, coupon });
  } catch (error) {
    return next(error);
  }
});

router.post('/checkout', requireAuth, async (req, res, next) => {
  try {
    const { planId, paymentMethod = 'pix', couponCode } = req.body || {};
    const userId = req.user.sub;

    const result = await withTransaction(async (client) => {
      const planResult = await client.query('SELECT * FROM plans WHERE id = $1 AND active = true LIMIT 1', [planId]);
      const plan = planResult.rows[0];
      if (!plan) throw Object.assign(new Error('Plano inválido.'), { statusCode: 404 });

      let total = Number(plan.price);
      let coupon = null;

      if (couponCode) {
        const couponResult = await client.query('SELECT * FROM coupons WHERE upper(code) = upper($1) LIMIT 1', [couponCode]);
        coupon = couponResult.rows[0];
        if (coupon && coupon.status === 'active' && coupon.usage_count < coupon.usage_limit) {
          const plans = coupon.applicable_plans || [];
          if (!plans.length || plans.includes(planId)) {
            const discount = coupon.discount_type === 'percent'
              ? total * (Number(coupon.discount_value) / 100)
              : Number(coupon.discount_value);
            total = Math.max(0, total - discount);
            await client.query('UPDATE coupons SET usage_count = usage_count + 1 WHERE id = $1', [coupon.id]);
          }
        }
      }

      const nextBilling = new Date();
      nextBilling.setDate(nextBilling.getDate() + 30);

      const subResult = await client.query(
        `INSERT INTO subscriptions (user_id, plan_id, status, start_date, next_billing_date, payment_method)
         VALUES ($1, $2, 'active', CURRENT_DATE, $3, $4)
         RETURNING *`,
        [userId, planId, nextBilling.toISOString().slice(0, 10), paymentMethod]
      );
      const subscription = subResult.rows[0];

      const paymentResult = await client.query(
        `INSERT INTO payments (user_id, subscription_id, value, date, status, payment_method, gateway_reference)
         VALUES ($1, $2, $3, CURRENT_DATE, 'paid', $4, $5)
         RETURNING *`,
        [userId, subscription.id, total, paymentMethod, 'sandbox-' + Date.now()]
      );

      await client.query('UPDATE users SET plan_id = $1, status = $2 WHERE id = $3', [planId, 'active', userId]);

      return { subscription, payment: paymentResult.rows[0], total };
    });

    return res.status(201).json(result);
  } catch (error) {
    return next(error);
  }
});

router.get('/payments', requireAuth, requireRole(['admin', 'finance']), async (req, res, next) => {
  try {
    const { status } = req.query;
    const params = [];
    let where = '';
    if (status) {
      params.push(status);
      where = `WHERE p.status = $${params.length}`;
    }
    const { rows } = await query(
      `SELECT p.id, p.user_id AS "userId", u.name AS "userName", p.subscription_id AS "subscriptionId", p.value, p.date, p.status, p.payment_method AS "paymentMethod"
       FROM payments p
       JOIN users u ON u.id = p.user_id
       ${where}
       ORDER BY p.date DESC, p.created_at DESC`,
      params
    );
    return res.json({ payments: rows });
  } catch (error) {
    return next(error);
  }
});

export default router;
