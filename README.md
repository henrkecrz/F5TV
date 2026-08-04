# F5 TV Streaming Platform

**F5 TV — Atualize sua forma de assistir.**

Plataforma MVP classe A para uma emissora digital com streaming sob demanda, transmissão ao vivo, grade de programação, assinaturas, checkout simulado, painel administrativo, financeiro, upload, biblioteca de mídia, avaliações e CRM de assinantes.

Este repositório contém:

- Frontend React + Vite + TypeScript + Tailwind CSS.
- Banco SQL real em **MySQL/MariaDB**, compatível com Hostinger.
- API PHP simples com PDO para hospedagem compartilhada.
- Cliente frontend `src/services/hostingerApi.ts` para consumir a API PHP.
- Backend Node/Express mantido como alternativa para VPS/Node, mas o alvo principal de hospedagem compartilhada é PHP + MySQL.

---

## Status da integração Hostinger

Já foi implementado:

- Schema MySQL/MariaDB em `database/mysql-schema.sql`.
- Seed MySQL/MariaDB em `database/mysql-seed.sql`.
- API PHP pública em `php-api/`.
- Cliente React em `src/services/hostingerApi.ts`.
- `DataContext` com hidratação remota opcional usando `VITE_USE_HOSTINGER_API=true`.

Quando a API Hostinger estiver ativada, o frontend tenta carregar do MySQL/PHP:

- planos;
- categorias;
- conteúdos;
- séries;
- canais ao vivo;
- programação ao vivo.

Se a API estiver desligada ou indisponível, o sistema mantém fallback automático para os dados mockados/localStorage.

---

## Stack recomendada para Hostinger compartilhada

### Frontend

- React
- Vite
- TypeScript
- Tailwind CSS
- Build estático enviado para `public_html`

### Backend Hostinger

- PHP 8+
- PDO MySQL
- MySQL/MariaDB pelo hPanel
- API em arquivos PHP dentro de `public_html/api`

### Banco de dados

- MySQL 8+ ou MariaDB 10.6+
- Charset `utf8mb4`
- Arquivos principais:

```text
database/mysql-schema.sql
database/mysql-seed.sql
```

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
  services/
    hostingerApi.ts
  types.ts

php-api/
  _bootstrap.php
  config.example.php
  health.php
  catalog.php
  live.php
  billing.php

database/
  mysql-schema.sql
  mysql-seed.sql
  schema.sql        # versão PostgreSQL mantida como referência
  seed.sql          # versão PostgreSQL mantida como referência

server/             # alternativa Node/Express para VPS
```

---

## Endpoints PHP para Hostinger

Após copiar `php-api` para `public_html/api`, a base será:

```text
https://seudominio.com/api
```

### Healthcheck

```text
GET /api/health.php
```

### Catálogo

```text
GET /api/catalog.php
GET /api/catalog.php?action=categories
GET /api/catalog.php?action=content&id=content-conexao-f5
GET /api/catalog.php?action=series
GET /api/catalog.php?q=jornalismo
GET /api/catalog.php?type=documentary
```

### Ao vivo e programação

```text
GET /api/live.php
GET /api/live.php?action=schedule
GET /api/live.php?action=schedule&date=2026-05-27
GET /api/live.php?action=schedule&channel_id=channel-f5tv
```

### Planos, cupom e checkout

```text
GET  /api/billing.php?action=plans
POST /api/billing.php?action=validate-coupon
POST /api/billing.php?action=checkout
```

---

## Banco MySQL/MariaDB

O schema MySQL cria tabelas para:

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

## Como configurar na Hostinger

### 1. Criar banco no hPanel

No painel da Hostinger:

```text
Sites → Gerenciar → Bancos de Dados MySQL
```

Crie:

```text
Banco: uXXXXXXX_f5tv
Usuário: uXXXXXXX_f5tv_user
Senha: definida no painel
Host: normalmente localhost
```

### 2. Importar schema e seed

No phpMyAdmin da Hostinger:

1. Selecione o banco criado.
2. Vá em **Importar**.
3. Importe primeiro:

```text
database/mysql-schema.sql
```

4. Depois importe:

```text
database/mysql-seed.sql
```

### 3. Publicar API PHP

Copie a pasta:

```text
php-api/
```

para:

```text
public_html/api/
```

Dentro de `public_html/api`, copie:

```text
config.example.php
```

para:

```text
config.php
```

Edite `config.php` com os dados reais do banco:

```php
return [
    'db_host' => 'localhost',
    'db_name' => 'uXXXXXXX_f5tv',
    'db_user' => 'uXXXXXXX_f5tv_user',
    'db_pass' => 'sua_senha_mysql',
    'cors_origin' => '*',
    'api_key' => 'uma_chave_privada_para_admin',
];
```

### 4. Testar API

```text
https://seudominio.com/api/health.php
https://seudominio.com/api/catalog.php
https://seudominio.com/api/live.php
https://seudominio.com/api/billing.php?action=plans
```

---

## Como publicar o frontend na Hostinger

### 1. Configurar URL da API

Crie `.env.production` localmente com:

```bash
VITE_USE_HOSTINGER_API=true
VITE_API_URL=https://seudominio.com/api
```

Para desenvolvimento local sem API PHP, deixe:

```bash
VITE_USE_HOSTINGER_API=false
```

### 2. Gerar build

```bash
npm install
npm run build
```

### 3. Enviar arquivos

Envie o conteúdo da pasta:

```text
dist/
```

para:

```text
public_html/
```

Se a API estiver em `public_html/api`, mantenha essa pasta junto com o build.

---

## Contas de teste do seed

```text
admin@f5tv.com.br
editor@f5tv.com.br
financeiro@f5tv.com.br
henrikeaps@gmail.com
```

Na API PHP desta fase, o login completo ainda deve ser implementado de forma segura antes de produção. O banco já possui campo `password_hash` para isso.

---

## Scripts npm úteis

```bash
npm run dev        # frontend Vite local
npm run build      # build estático para Hostinger
npm run preview    # preview local do build
npm run lint       # checagem TypeScript
```

Os scripts de PostgreSQL/Node foram mantidos no projeto, mas para Hostinger compartilhada use os arquivos `mysql-schema.sql`, `mysql-seed.sql` e `php-api`.

---

## Próximos passos técnicos

1. Conectar checkout 100% ao endpoint PHP em uma alteração dedicada.
2. Implementar login PHP seguro com:
   - `password_hash`;
   - `password_verify`;
   - sessão/token;
   - recuperação de senha.
3. Criar endpoints PHP administrativos protegidos.
4. Configurar uploads reais usando pasta segura ou storage externo.
5. Integrar pagamento real depois do checkout simulado.

---

## Observação importante

Para Hostinger compartilhada, **PHP + MySQL/MariaDB** é a opção mais compatível. O backend Node/Express e PostgreSQL ficam como alternativa caso o projeto seja migrado futuramente para VPS, Render, Railway, Supabase ou outro ambiente com Node.
