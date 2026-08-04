import pg from 'pg';

const { Pool } = pg;

const connectionString = process.env.DATABASE_URL;

export const pool = new Pool({
  connectionString,
  ssl: process.env.DATABASE_SSL === 'true' ? { rejectUnauthorized: false } : false,
});

export async function query(text, params = []) {
  const startedAt = Date.now();
  try {
    const result = await pool.query(text, params);
    const duration = Date.now() - startedAt;
    if (process.env.SQL_DEBUG === 'true') {
      console.log('[sql]', { text, duration, rows: result.rowCount });
    }
    return result;
  } catch (error) {
    console.error('[sql:error]', { text, message: error.message });
    throw error;
  }
}

export async function withTransaction(callback) {
  const client = await pool.connect();
  try {
    await client.query('BEGIN');
    const result = await callback(client);
    await client.query('COMMIT');
    return result;
  } catch (error) {
    await client.query('ROLLBACK');
    throw error;
  } finally {
    client.release();
  }
}
