# F5 TV Streaming Platform

**F5 TV — Atualize sua forma de assistir.**

Plataforma MVP classe A para uma emissora digital com streaming sob demanda, transmissão ao vivo, grade de programação, assinaturas, checkout simulado, painel administrativo, financeiro, upload, biblioteca de mídia, avaliações e CRM de assinantes.

Este repositório agora contém:

- Frontend React + Vite + TypeScript + Tailwind CSS.
- Backend Express em Node.js.
- Banco de dados SQL real com PostgreSQL.
- Schema completo em `database/schema.sql`.
- Dados iniciais em `database/seed.sql`.

---

## Visão geral

A F5 TV é uma plataforma de streaming para emissora, com foco em:

- Jornalismo.
- Séries originais.
- Programas de TV.
- Documentários.
- Esportes.
- Especiais.
- Transmissão ao vivo.
- Grade de programação.

A área Kids não faz parte desta fase.

---

## Tecnologias

### Frontend

- React 19
- Vite
- TypeScript
- Tailwind CSS
- React Router
- Recharts
- Lucide React

### Backend

- Node.js
- Express
- PostgreSQL
- `pg`
- JWT para sessão da API
- CORS
- Morgan
- dotenv

### Banco de dados

- PostgreSQL 14+
- `pgcrypto` para geração de IDs
- Tabelas relacionais para usuários, planos, conteúdos, assinaturas, pagamentos, canais, programação, cupons, mídia e avaliações

---

## Estrutura principal

```text
src/
  components/
  context/
  data/
  layouts/
  pages/
  routes/
  types.ts

server/
  index.js
  db.js
  middleware/
    auth.js
  routes/
    auth.js
    catalog.js
    live.js
    billing.js
    admin.js

database/
  schema.sql
  seed.sql
```

---

## Rotas principais do frontend

### Públicas

```text
/
/landing
/login
/cadastro
/recuperar-senha
/planos
/sobre
/contato
/termos
/privacidade
```

### Assinante

```text
/app
/app/perfis
/app/conteudo/:id
/app/assistir/:id
/app/minha-lista
/app/continuar-assistindo
/app/minha-conta
/app/busca
/app/ao-vivo
/app/programacao
/app/dispositivos
/checkout
/checkout/sucesso
```

### Administrativo

```text
/admin
/admin/usuarios
/admin/assinantes
/admin/conteudos
/admin/series
/admin/temporadas
/admin/episodios
/admin/uploads
/admin/programacao
/admin/canais
/admin/midia
/admin/financeiro
/admin/planos
/admin/banners
/admin/cupons
/admin/avaliacoes
/admin/relatorios
/admin/configuracoes
```

---

## Endpoints principais da API

Base local:

```text
http://localhost:4000/api
```

### Healthcheck

```text
GET /api/health
```

### Auth

```text
POST /api/auth/login
POST /api/auth/register
GET  /api/auth/me
```

### Catálogo

```text
GET  /api/catalog/categories
GET  /api/catalog/contents
GET  /api/catalog/contents/:id
GET  /api/catalog/series
GET  /api/catalog/series/:id/seasons
POST /api/catalog/contents
```

### Ao vivo e programação

```text
GET  /api/live/channels
GET  /api/live/schedule
POST /api/live/channels
POST /api/live/schedule
```

### Assinaturas e financeiro

```text
GET  /api/billing/plans
GET  /api/billing/coupons
POST /api/billing/coupons/validate
POST /api/billing/checkout
GET  /api/billing/payments
```

### Admin

```text
GET   /api/admin/dashboard
GET   /api/admin/users
PATCH /api/admin/users/:id/status
GET   /api/admin/media
POST  /api/admin/media
GET   /api/admin/reviews
PATCH /api/admin/reviews/:id/status
```

---

## Banco SQL real

O banco real está em PostgreSQL.

### Arquivos

```text
database/schema.sql
database/seed.sql
```

O schema cria tabelas para:

- plans
- users
- profiles
- categories
- genres
- contents
- series
- seasons
- episodes
- subscriptions
- payments
- uploads
- media_assets
- banners
- watch_history
- favorites
- notifications
- reviews
- coupons
- channels
- live_schedules
- connected_devices
- editorial_history

---

## Como rodar localmente

### 1. Instalar dependências

```bash
npm install
```

### 2. Criar `.env.local`

Copie o arquivo de exemplo:

```bash
cp .env.example .env.local
```

Configure pelo menos:

```bash
VITE_API_URL=http://localhost:4000/api
API_PORT=4000
CORS_ORIGIN=http://localhost:3000
JWT_SECRET=replace_me_with_a_long_random_value
DATABASE_URL=postgres://user:password@localhost:5432/f5tv
DATABASE_SSL=false
```

### 3. Criar banco PostgreSQL

Exemplo local:

```bash
createdb f5tv
```

### 4. Rodar schema e seed

```bash
npm run db:schema
npm run db:seed
```

Ou resetar tudo:

```bash
npm run db:reset
```

### 5. Rodar backend

```bash
npm run dev:api
```

API local:

```text
http://localhost:4000/api/health
```

### 6. Rodar frontend

Em outro terminal:

```bash
npm run dev
```

Frontend local:

```text
http://localhost:3000
```

### 7. Rodar frontend e backend juntos

```bash
npm run dev:full
```

---

## Contas de teste do seed

```text
admin@f5tv.com.br
editor@f5tv.com.br
financeiro@f5tv.com.br
henrikeaps@gmail.com
```

A autenticação da API nesta fase está preparada como base de MVP e deve ser endurecida antes de produção com validação de senha, hashing obrigatório, refresh tokens e políticas de sessão.

---

## Scripts npm

```bash
npm run dev        # frontend Vite
npm run dev:api    # backend Express
npm run dev:full   # frontend + backend
npm run build      # build do frontend
npm run preview    # preview do frontend
npm run lint       # TypeScript check
npm run db:schema  # aplica schema SQL
npm run db:seed    # insere dados iniciais
npm run db:reset   # recria schema e aplica seed
```

---

## Status do MVP

### Já existe no frontend

- Landing page
- Login/cadastro
- Área do assinante
- Catálogo
- Player
- Ao vivo
- Programação
- Dispositivos
- Checkout simulado
- Admin completo visual
- Conteúdos, séries, temporadas, episódios
- Uploads
- Mídia
- Financeiro
- Planos
- Cupons
- Banners
- Avaliações
- Relatórios
- Configurações

### Adicionado nesta etapa

- Backend Express
- PostgreSQL schema
- Seed SQL
- API de autenticação base
- API de catálogo
- API de programação ao vivo
- API de billing/checkout
- API administrativa
- README completo

---

## Próximos passos para produção

1. Conectar o frontend ao backend usando `VITE_API_URL`.
2. Substituir gradualmente `mockDatabase/localStorage` por chamadas HTTP.
3. Implementar autenticação completa:
   - senha obrigatória;
   - hash seguro;
   - refresh token;
   - recuperação de senha;
   - expiração e rotação de sessão.
4. Adicionar migrations versionadas.
5. Adicionar storage real para vídeos, capas e trailers.
6. Integrar gateway real de pagamento.
7. Criar testes de API.
8. Criar CI/CD.
9. Deploy do banco e API em ambiente separado.
10. Monitoramento, logs e backups.

---

## Observação importante

Este projeto é um MVP avançado. Ele agora possui uma fundação real de backend e banco SQL, mas o frontend ainda usa parte relevante de dados mockados. A migração completa deve ser feita por módulo, começando por planos, login, catálogo, checkout e ao vivo.
