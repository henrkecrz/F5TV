import 'dotenv/config';
import express from 'express';
import cors from 'cors';
import morgan from 'morgan';
import authRoutes from './routes/auth.js';
import catalogRoutes from './routes/catalog.js';
import liveRoutes from './routes/live.js';
import billingRoutes from './routes/billing.js';
import adminRoutes from './routes/admin.js';
import { query, pool } from './db.js';

const app = express();
const port = Number(process.env.API_PORT || process.env.PORT || 4000);

app.use(cors({ origin: process.env.CORS_ORIGIN || '*', credentials: true }));
app.use(express.json({ limit: '10mb' }));
app.use(morgan(process.env.NODE_ENV === 'production' ? 'combined' : 'dev'));

app.get('/api/health', async (_req, res) => {
  try {
    const dbResult = await query('SELECT now() AS now');
    return res.json({ ok: true, service: 'F5 TV API', database: 'online', time: dbResult.rows[0].now });
  } catch (error) {
    return res.status(503).json({ ok: false, service: 'F5 TV API', database: 'offline', message: error.message });
  }
});

app.use('/api/auth', authRoutes);
app.use('/api/catalog', catalogRoutes);
app.use('/api/live', liveRoutes);
app.use('/api/billing', billingRoutes);
app.use('/api/admin', adminRoutes);

app.use((req, res) => {
  res.status(404).json({ error: 'NOT_FOUND', message: `Rota não encontrada: ${req.method} ${req.path}` });
});

app.use((error, _req, res, _next) => {
  const status = error.statusCode || 500;
  if (process.env.NODE_ENV !== 'test') {
    console.error('[api:error]', error);
  }
  res.status(status).json({
    error: error.code || 'INTERNAL_ERROR',
    message: error.message || 'Erro interno da API.',
  });
});

const server = app.listen(port, () => {
  console.log(`F5 TV API listening on http://localhost:${port}`);
});

function shutdown() {
  server.close(async () => {
    await pool.end();
    process.exit(0);
  });
}

process.on('SIGINT', shutdown);
process.on('SIGTERM', shutdown);
