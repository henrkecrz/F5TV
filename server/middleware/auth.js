import jwt from 'jsonwebtoken';

const JWT_SECRET = process.env.JWT_SECRET || 'f5tv-dev-secret-change-me';

export function signToken(user) {
  return jwt.sign(
    {
      sub: user.id,
      role: user.role,
      email: user.email,
      name: user.name,
    },
    JWT_SECRET,
    { expiresIn: process.env.JWT_EXPIRES_IN || '7d' }
  );
}

export function requireAuth(req, res, next) {
  const header = req.headers.authorization || '';
  const token = header.startsWith('Bearer ') ? header.slice(7) : null;

  if (!token) {
    return res.status(401).json({ error: 'AUTH_REQUIRED', message: 'Token de autenticação ausente.' });
  }

  try {
    req.user = jwt.verify(token, JWT_SECRET);
    return next();
  } catch (_) {
    return res.status(401).json({ error: 'INVALID_TOKEN', message: 'Token inválido ou expirado.' });
  }
}

export function requireRole(allowedRoles = []) {
  return (req, res, next) => {
    if (!req.user) {
      return res.status(401).json({ error: 'AUTH_REQUIRED', message: 'Autenticação obrigatória.' });
    }

    if (!allowedRoles.includes(req.user.role)) {
      return res.status(403).json({ error: 'FORBIDDEN', message: 'Perfil sem permissão para esta ação.' });
    }

    return next();
  };
}
