# Plano de Migração: F5 TV Streaming Platform → WordPress

## Visão Geral do Projeto Atual

**Stack Atual:**
- Frontend: React 19 + TypeScript + Vite + Tailwind CSS + React Router
- Backend: Node.js/Express + PostgreSQL (API em `/api`)
- Auth: JWT + localStorage (mock)
- Dados: Mock database extenso (1750+ linhas) com Users, Content, Series, Plans, Subscriptions, etc.

**Principais Áreas:**
1. **Público** (Landing, Planos, Sobre, Contato, Login, Cadastro, Recuperação)
2. **Área do Assinante** (Perfis, Home, Conteúdo, Minha Lista, Ao Vivo, Programação, Dispositivos, Checkout)
3. **Painel Admin** (Dashboard, Usuários, Conteúdos, Séries, Financeiro, Canais, Programação, Mídia, Cupons, Relatórios, Configurações)

---

## Estratégia de Arquitetura WordPress

### 1. Tema WordPress (`f5tv-theme`)
**Responsabilidade:** Apenas o **frontend público** e **templates de página**

**Estrutura:**
```
f5tv-theme/
├── style.css                    # Header do tema
├── functions.php                # Setup do tema, enqueue assets, suporte a blocos
├── theme.json                   # Configuração Global Styles (cores F5, tipografia, spacing)
├── assets/
│   ├── css/
│   │   ├── main.css             # Build do Tailwind (compilado)
│   │   └── editor.css           # Estilos para editor Gutenberg
│   ├── js/
│   │   ├── main.js              # Bundle React compilado (Vite build)
│   │   └── blocks.js            # Blocos Gutenberg customizados
│   └── images/                  # Logos, favicons, placeholders
├── templates/                   # Templates de página (FSE - Full Site Editing)
│   ├── index.html               # Homepage
│   ├── page-landing.html        # Landing page
│   ├── page-planos.html         # Página de planos
│   ├── page-sobre.html
│   ├── page-contato.html
│   ├── page-login.html
│   ├── page-cadastro.html
│   ├── page-recuperar-senha.html
│   ├── single-conteudo.html     # Single de conteúdo (filme/série)
│   ├── archive-conteudo.html    # Listagem de conteúdos
│   ├── taxonomy-genero.html     # Por gênero
│   └── 404.html
├── parts/                       # Template Parts (Header, Footer, Hero, etc.)
│   ├── header.html
│   ├── footer.html
│   ├── hero-section.html
│   ├── content-grid.html
│   └── plans-section.html
├── patterns/                    # Block Patterns reutilizáveis
│   ├── hero-banner.php
│   ├── content-carousel.php
│   ├── plan-card.php
│   ├── channel-grid.php
│   └── live-schedule.php
├── inc/
│   ├── custom-post-types.php    # CPTs: Conteúdo, Série, Temporada, Episódio, Canal, Programação
│   ├── taxonomies.php           # Taxonomias: Gênero, Categoria, Classificação
│   ├── acf-fields.php           # ACF Field Groups (JSON)
│   ├── rest-api.php             # Endpoints REST customizados
│   ├── hooks.php                # Actions/filters
│   └── assets.php               # Enqueue scripts/styles
└── build/                       # Scripts de build (Vite config para tema)
    └── vite.config.theme.ts
```

### 2. Plugin: F5TV Client Area (`f5tv-client-area`)
**Responsabilidade:** **Área do Assinante** (tudo sob `/app/*`)

**Funcionalidades:**
- Autenticação integrada com WordPress (WP Users + User Meta)
- Perfis múltiplos por usuário (Custom Table ou CPT)
- Dashboard do assinante (React App embarcada via Shortcode/Block)
- Player de vídeo (integração com m3u8/MP4/CDN)
- Minha Lista, Continuar Assistindo, Histórico
- Dispositivos conectados
- Checkout/Assinatura (integração WooCommerce ou Stripe direto)
- Ao Vivo / Programação / Canais

**Estrutura:**
```
f5tv-client-area/
├── f5tv-client-area.php         # Plugin principal
├── includes/
│   ├── class-auth.php           # Autenticação, login, registro, perfis
│   ├── class-subscription.php   # Gestão de planos/assinaturas
│   ├── class-content-access.php # Controle de acesso a conteúdo
│   ├── class-devices.php        # Gestão de dispositivos
│   ├── class-watch-history.php  # Histórico, continue watching
│   ├── class-my-list.php        # Minha lista (favoritos)
│   ├── class-live-tv.php        # Canais, programação, ao vivo
│   ├── class-checkout.php       # Checkout, webhooks Stripe/Asaas
│   └── class-rest-api.php       # Endpoints REST para React App
├── assets/
│   ├── css/
│   │   └── client-area.css      # Estilos isolados (CSS Modules ou scoped)
│   └── js/
│       └── client-app.js        # React App compilada (Vite build)
├── templates/
│   ├── shortcode-dashboard.php  # [f5tv_dashboard]
│   ├── shortcode-player.php     # [f5tv_player id="xxx"]
│   ├── shortcode-live.php       # [f5tv_live_tv]
│   └── shortcode-profile.php    # [f5tv_profile_selector]
├── database/
│   └── install.php              # Criação de tabelas customizadas
└── languages/
    └── f5tv-client-area.pot
```

**Tabelas Customizadas (SQL):**
```sql
-- wp_f5tv_profiles
CREATE TABLE wp_f5tv_profiles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    avatar_color VARCHAR(50) DEFAULT 'bg-f5-red',
    is_kids BOOLEAN DEFAULT FALSE,
    pin_hash VARCHAR(255),           -- PIN opcional para perfis kids
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id)
);

-- wp_f5tv_subscriptions
CREATE TABLE wp_f5tv_subscriptions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    plan_id VARCHAR(50) NOT NULL,
    status ENUM('active','inactive','canceled','past_due','trialing') DEFAULT 'active',
    gateway ENUM('stripe','asaas','mercadopago','woocommerce') DEFAULT 'stripe',
    gateway_subscription_id VARCHAR(255),
    current_period_start DATETIME,
    current_period_end DATETIME,
    cancel_at_period_end BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_gateway_sub (gateway_subscription_id)
);

-- wp_f5tv_watch_history
CREATE TABLE wp_f5tv_watch_history (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    profile_id BIGINT UNSIGNED,
    content_id BIGINT UNSIGNED NOT NULL,  -- Post ID do CPT 'conteudo'
    episode_id BIGINT UNSIGNED,           -- Post ID do CPT 'episodio'
    watched_seconds INT UNSIGNED DEFAULT 0,
    total_seconds INT UNSIGNED,
    progress DECIMAL(5,2) DEFAULT 0,
    completed BOOLEAN DEFAULT FALSE,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_profile (user_id, profile_id),
    INDEX idx_content (content_id),
    UNIQUE KEY uniq_user_content_episode (user_id, profile_id, content_id, episode_id)
);

-- wp_f5tv_my_list
CREATE TABLE wp_f5tv_my_list (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    profile_id BIGINT UNSIGNED,
    content_id BIGINT UNSIGNED NOT NULL,
    added_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_profile (user_id, profile_id),
    UNIQUE KEY uniq_user_content (user_id, profile_id, content_id)
);

-- wp_f5tv_devices
CREATE TABLE wp_f5tv_devices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    device_name VARCHAR(255),
    device_type ENUM('browser','mobile','smart_tv','desktop','tablet'),
    device_fingerprint VARCHAR(255),      -- Hash único do device
    user_agent TEXT,
    ip_address VARCHAR(45),
    location VARCHAR(255),
    last_active DATETIME,
    is_active BOOLEAN DEFAULT TRUE,
    revoked_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_fingerprint (device_fingerprint)
);
```

### 3. Plugin: F5TV Admin Panel (`f5tv-admin-panel`)
**Responsabilidade:** **Painel Administrativo Streaming** (estende WP Admin)

**Abordagem:** **NÃO recriar todo o WP Admin**. Usar:
- **Custom Post Types** para dados (Conteúdo, Série, Canal, etc.)
- **ACF Pro** para campos complexos (repeater, gallery, relationship)
- **Páginas de Admin Customizadas** apenas para funcionalidades específicas de streaming:
  - Dashboard com métricas (assinantes, receita, visualizações)
  - Gestão de Assinantes (filtros por status, plano, inadimplência)
  - Financeiro (relatórios, chargebacks, MRR, churn)
  - Programação Ao Vivo (grid visual, drag-drop)
  - Cupons/Descontos
  - Configurações de Player/CDN/Transcodificação

**Estrutura:**
```
f5tv-admin-panel/
├── f5tv-admin-panel.php
├── includes/
│   ├── class-admin-menu.php       # Menus/submenus no WP Admin
│   ├── class-dashboard.php        # Dashboard widgets + página principal
│   ├── class-subscribers.php      # Lista/gestão de assinantes
│   ├── class-finance.php          # Relatórios financeiros, MRR, churn
│   ├── class-live-schedule.php    # Programação ao vivo (grid calendar)
│   ├── class-coupons.php          # Cupons de desconto
│   ├── class-settings.php         # Settings API (Player, CDN, Email, etc.)
│   ├── class-reports.php          # Relatórios exportáveis (CSV/PDF)
│   └── class-ajax-handlers.php    # AJAX handlers para tabelas/datagrids
├── assets/
│   ├── css/
│   │   └── admin.css              # Estilos admin (WP Admin CSS variables)
│   └── js/
│       ├── dashboard.js           # Gráficos (Chart.js)
│       ├── live-schedule.js       # Drag-drop calendar (SortableJS)
│       ├── subscribers-table.js   # DataTables/AG Grid
│       └── finance-charts.js
├── templates/
│   ├── dashboard.php
│   ├── subscribers.php
│   ├── finance.php
│   ├── live-schedule.php
│   ├── coupons.php
│   ├── reports.php
│   └── settings.php
└── languages/
```

---

## Mapeamento de Dados: React/Node → WordPress

| Entidade Atual | WordPress | Notas |
|---|---|---|
| `Content` (filme/série/doc) | **CPT `f5tv_conteudo`** | ACF: banner, trailer, vídeo, elenco, diretores, classificação, tags |
| `Series` | **CPT `f5tv_serie`** | Relacionamento 1:N com Temporadas |
| `Season` | **CPT `f5tv_temporada`** | Parent: Série |
| `Episode` | **CPT `f5tv_episodio`** | Parent: Temporada, ACF: vídeo, duração, thumbnail |
| `Category` | **Taxonomia `f5tv_categoria`** | Hierárquica |
| `Genre` | **Taxonomia `f5tv_genero`** | Não hierárquica |
| `Channel` | **CPT `f5tv_canal`** | ACF: stream_url, logo, categoria, status |
| `LiveSchedule` | **CPT `f5tv_programacao`** | ACF: canal (rel), data, horário, status, imagem |
| `Plan` | **CPT `f5tv_plano`** OU **WooCommerce Subscription Plans** | Recomendo WooCommerce Subscriptions para billing robusto |
| `User` (subscriber) | **WP User** + `wp_f5tv_profiles` | Role: `subscriber` + meta `f5tv_plan` |
| `User` (admin/editor/finance) | **WP User** | Roles WP: `administrator`, `editor`, `f5tv_finance` (custom) |
| `Subscription` | **WC_Subscription** + `wp_f5tv_subscriptions` | Sync bidirecional |
| `Payment` | **WC_Order** (WooCommerce) | Webhook Stripe/Asaas → WC Order |
| `WatchHistory` | `wp_f5tv_watch_history` | Tabela customizada |
| `Favorite` | `wp_f5tv_my_list` | Tabela customizada |
| `Device` | `wp_f5tv_devices` | Tabela customizada |
| `Coupon` | **WC_Coupon** + meta customizada | Ou tabela própria se lógica complexa |
| `Review` | **Comentários** (coment_type=review) + meta rating | Ou CPT `f5tv_avaliacao` |
| `Notification` | `wp_f5tv_notifications` | Tabela customizada |

---

## Fluxo de Autenticação Unificado

```
┌─────────────────────────────────────────────────────────────┐
                    WORDPRESS CORE AUTH
└─────────────────────────────────────────────────────────────┘
                          │
          ┌───────────────┼───────────────┐
          ▼               ▼               ▼
    ┌──────────┐    ┌────────────┐  ┌───────────┐
    │  Tema    │    │Client Area │  │Admin Panel│
    │ (Público)│    │  (Plugin)  │  │  (Plugin) │
    └──────────┘    └────────────┘  └───────────┘
          │               │               │
          │    ┌──────────┴──────────┐   │
          │    │  Tabelas Custom     │   │
          │    │  wp_f5tv_*          │   │
          │    └─────────────────────┘   │
          │               │               │
          └───────────────┼───────────────┘
                          ▼
              ┌───────────────────────┐
              │  WooCommerce          │
              │  Subscriptions        │
              │  (Billing/Planos)     │
              └───────────────────────┘
```

**Pontos-chave:**
- **Single Source of Truth:** WP Users table
- **Perfis:** Tabela customizada `wp_f5tv_profiles` (1 user = N perfis)
- **Planos/Assinaturas:** WooCommerce Subscriptions (gateway Stripe/Asaas/MercadoPago)
- **Sincronização:** `f5tv-client-area` escuta hooks WC (`woocommerce_subscription_status_updated`) e atualiza `wp_f5tv_subscriptions`
- **Acesso a conteúdo:** Verifica `current_user_can('f5tv_access_content', $content_id)` → checa assinatura ativa + plano compatível

---

## Cronograma de Implementação

### Fase 1: Fundação (Semanas 1-2)
- [ ] Setup do tema `f5tv-theme` com `theme.json` (cores F5, tipografia, spacing)
- [ ] Configurar Vite para build duplo: tema + plugins
- [ ] Registrar CPTs e Taxonomias (`inc/custom-post-types.php`)
- [ ] Criar Field Groups ACF (exportar JSON para `inc/acf-fields.php`)
- [ ] Migrar dados do mock para WP (script de seed)
- [ ] Template Parts: Header, Footer, Hero, Content Grid

### Fase 2: Tema - Páginas Públicas (Semanas 3-4)
- [ ] Homepage (FSE template + patterns)
- [ ] Landing Page
- [ ] Página de Planos (integração WC Subscriptions)
- [ ] Single Conteúdo (filme/série/episódio)
- [ ] Archive/Taxonomy templates
- [ ] Login/Cadastro/Recuperar Senha (WP forms + custom templates)
- [ ] CSS/JS otimizado (Tailwind purge, code splitting)

### Fase 3: Plugin Client Area - Core (Semanas 5-7)
- [ ] Tabelas customizadas (install.php)
- [ ] Autenticação: Login, Registro, Perfis, Logout
- [ ] Integração WooCommerce Subscriptions (webhooks, sincronização)
- [ ] Shortcodes principais: `[f5tv_dashboard]`, `[f5tv_player]`, `[f5tv_profile_selector]`
- [ ] React App buildada e enfileirada apenas nas páginas do shortcode

### Fase 4: Plugin Client Area - Features (Semanas 8-10)
- [ ] Player de vídeo (Video.js / Plyr / custom) com HLS/DASH
- [ ] Minha Lista / Favoritos (AJAX + REST)
- [ ] Continue Watching / Histórico
- [ ] Dispositivos (registro, listagem, revogação)
- [ ] Ao Vivo / Programação / Canais (EPG grid)
- [ ] Checkout/Assinatura (upgrade/downgrade/cancel)
- [ ] Área "Minha Conta" (dados, senha, email, PIN kids)

### Fase 5: Plugin Admin Panel (Semanas 11-13)
- [ ] Menus WP Admin + Capabilities (`f5tv_manage_content`, `f5tv_view_finance`, etc.)
- [ ] Dashboard com métricas (Chart.js)
- [ ] Gestão de Assinantes (filtros avançados, export CSV)
- [ ] Financeiro (MRR, Churn, LTV, Inadimplência, Chargebacks)
- [ ] Programação Ao Vivo (grid drag-drop, conflitos)
- [ ] Cupons (criar, editar, relatórios de uso)
- [ ] Settings API (Player, CDN, Email templates, Webhooks)

### Fase 6: Integração & Polimento (Semanas 14-16)
- [ ] Testes E2E (Playwright)
- [ ] Performance (lazy load, caching, CDN)
- [ ] Acessibilidade (WCAG AA)
- [ ] SEO (Yoast/RankMath compat, schema.org VideoObject)
- [ ] Documentação + Handoff
- [ ] Deploy staging → produção

---

## Decisões Técnicas Críticas

### 1. **React no WordPress: Como embarcar?**
**Opção A (Recomendada):** Build separado por plugin/tema → enfileirar apenas onde necessário
- Vite `build.lib` mode para cada entry point
- `f5tv-theme/assets/js/main.js` → apenas blocos/patterns interativos leves
- `f5tv-client-area/assets/js/client-app.js` → SPA completa (carrega só na página do shortcode)
- `f5tv-admin-panel/assets/js/*.js` → widgets admin específicos

### 2. **Tailwind CSS no WordPress**
- Compilar via Vite/PostCSS no build
- `theme.json` define design tokens (cores, spacing, tipografia)
- `safelist` no `tailwind.config.js` para classes dinâmicas (ex: `bg-f5-red`, `text-f5-blue-900`)
- Zero runtime: apenas CSS estático compilado

### 3. **Player de Vídeo**
- **Video.js** + `videojs-contrib-hls` + `videojs-contrib-dash`
- Suporte a: HLS (.m3u8), DASH (.mpd), MP4 progressivo
- DRM: Widevine/PlayReady via `videojs-contrib-eme` (se necessário)
- Integração com CDN (Bunny, Cloudflare, AWS CloudFront)
- Watermark dinâmico (user ID + timestamp) via canvas overlay

### 4. **Billing: WooCommerce Subscriptions vs Custom**
**Recomendo WooCommerce Subscriptions porque:**
- Gateways prontos: Stripe, Asaas, MercadoPago, PayPal
- Webhooks, retry logic, dunning, emails automáticos
- Relatórios nativos (MRR, Churn, LTV)
- Checkout hospedado ou embedado
- Ecossistema de plugins (cupons, upgrades, gift cards)
- **Custo:** $279/ano (WC Subscriptions) + gateway fees

**Alternativa Custom:** Apenas se necessitar controle total e tiver equipe para manter.

### 5. **Busca e Filtros**
- **ElasticPress** (ElasticSearch) para busca full-text + filtros facetados
- Ou **SearchWP** se budget menor
- Fallback: WP_Query com índices customizados

### 6. **Cache e Performance**
- **WP Rocket** ou **Nginx FastCGI Cache** + **Redis Object Cache**
- CDN: Cloudflare (APO) ou Bunny.net
- Imagens: WebP/AVIF via **ShortPixel** ou **EWWW Image Optimizer**
- Critical CSS inline, defer non-critical JS

---

## Riscos e Mitigações

| Risco | Probabilidade | Impacto | Mitigação |
|---|---|---|---|
| Complexidade do React App no WP | Alta | Alto | Isolar em plugin, build separado, lazy-load via IntersectionObserver |
| Performance do Player | Média | Alto | Testar com Video.js, CDN configurado, prefetch de segmentos HLS |
| Migração de dados (mock → WP) | Média | Médio | Scripts de seed idempotentes, dry-run em staging |
| WooCommerce Subscriptions lock-in | Baixa | Médio | Abstrair gateway via interface `F5TV_Billing_Gateway` |
| Conflito de plugins/JS | Média | Médio | Namespacing rigoroso, CSS Modules, `wp_enqueue_script` deps corretas |
| Segurança (vídeo, dados usuário) | Alta | Crítico | Tokens assinados (JWT) para URLs de vídeo, rate-limit, CORS restrito |

---

## Stack de Deploy Recomendado

```
┌────────────────────────────────────────────────────────────┐
                    PRODUÇÃO
├────────────────────────────────────────────────────────────┤
  ☁️  AWS / DigitalOcean / Hetzner (VPS ou Kubernetes)
  ├── Nginx (SSL, Cache, Rate Limit, WAF)
  ├── PHP-FPM 8.3+ (OPcache, JIT)
  ├── MySQL 8.0 / MariaDB 10.11 (InnoDB, replication)
  ├── Redis (Object Cache, Sessions, Rate Limit)
  ├── ElasticSearch (ElasticPress) - opcional
  └── WP-CLI + GitHub Actions (CI/CD)
├────────────────────────────────────────────────────────────┤
  📦  WordPress Core (Git submodule ou Composer)
  🎨  f5tv-theme (Git submodule)
  🔌  f5tv-client-area (Git submodule)
  🔌  f5tv-admin-panel (Git submodule)
  🔌  ACF Pro (composer/private repo)
  🔌  WooCommerce + Subscriptions (composer)
  🔌  ElasticPress / SearchWP
  🔌  WP Rocket / Nginx Cache
└────────────────────────────────────────────────────────────┘
```

---

## Status de Implementação

| Fase | Status |
|------|--------|
| Fase 1: Fundação | Concluída |
| Fase 2: Tema - Páginas Públicas | Concluída |
| Fase 3: Plugin Client Area - Core | Concluída |
| Fase 4: Plugin Client Area - Features | Concluída |
| Fase 5: Plugin Admin Panel | Concluída |
| Fase 6: Integração & Polimento | Em andamento (pré-requisitos prontos) |

## Arquivos Entregues

### Tema `f5tv-theme`
- `style.css` — Header do tema e estilos base
- `theme.json` — Design tokens completos (cores, tipografia, spacing, sombras)
- `functions.php` — Setup, enqueue, módulos, body classes
- `inc/custom-post-types.php` — CPTs: Conteúdo, Série, Temporada, Episódio, Canal, Programação
- `inc/taxonomies.php` — Taxonomias: Categoria e Gênero
- `inc/acf-fields.php` — Field Groups ACF para todos os CPTs
- `inc/rest-api.php` — Endpoints REST: catalog, live, billing
- `inc/hooks.php` — Capability customizada + proteção de conteúdo
- `inc/block-patterns.php` — Padrões: Hero Banner, Plan Card
- `inc/template-tags.php` — Helpers: capa, banner, badges
- `templates/` — index, landing, planos, sobre, contato, login, cadastro, recuperar-senha, 404, single-conteudo, archive-conteudo, taxonomy-genero, taxonomy-categoria
- `parts/` — header, footer, hero-section, plans-section
- `assets/css/main.css`, `assets/css/editor.css`, `assets/js/main.js`, `assets/js/blocks.js`
- Pacote: `php/f5tv-theme.zip`

### Plugin `f5tv-client-area`
- `f5tv-client-area.php` — Plugin principal, ativação, shortcodes
- `database/install.php` — Criação das 5 tabelas customizadas
- `includes/class-auth.php` — Login, registro, perfis, logout (REST)
- `includes/class-subscription.php` — Sincronização com WooCommerce Subscriptions
- `includes/class-rest-api.php` — Endpoints: watch-history, my-list, devices, subscription, content/access
- `includes/class-content-access.php` — Redirect e controle de acesso
- `includes/class-watch-history.php` — Histórico + Continue Watching
- `includes/class-my-list.php` — Favoritos / Minha Lista
- `includes/class-devices.php` — Gestão de dispositivos conectados
- `includes/class-live-tv.php` — Canais e programação ao vivo
- `includes/class-checkout.php` — Planos, checkout WC, criação de assinatura
- `templates/shortcode-dashboard.php`, `shortcode-player.php`, `shortcode-profile.php`
- `assets/css/client-area.css`, `assets/js/client-app.js`
- Pacote: `php/f5tv-client-area.zip`

### Plugin `f5tv-admin-panel`
- `f5tv-admin-panel.php` — Menus admin, carregamento de classes
- `includes/class-dashboard.php` — Widgets de dashboard (assinantes, receita, overview)
- `includes/class-subscribers.php` — Listagem, filtros, exportação
- `includes/class-finance.php` — MRR, churn, status de assinaturas
- `includes/class-settings.php` — CDN, Player, Email, Webhooks
- `includes/class-coupons.php` — Integração com WooCommerce Coupons
- `includes/class-live-schedule.php` — Grade de programação por canal
- Pacote: `php/f5tv-admin-panel.zip`

## Próximos Passos Recomendados

1. **Testes em staging**: Instalar os 3 pacotes em um WP 6.4+ com WooCommerce + ACF Pro
2. **Build de assets**: Configurar Vite para gerar `main.css`, `main.js`, `editor.css`, `blocks.js` do tema e `client-app.js` do plugin
3. **Migração de dados**: Criar script WP-CLI para importar dados do mock React para os CPTs e tabelas customizadas
4. **Testes E2E**: Playwright para fluxos de login, assinatura, playback
5. **Deploy**: Nginx + PHP-FPM 8.3 + Redis + MySQL/MariaDB

---

## Estimativa de Esforço

| Componente | Semanas | Devs |
|---|---|---|
| Tema (Público) | 4 | 1-2 |
| Client Area Plugin | 6 | 2 |
| Admin Panel Plugin | 3 | 1-2 |
| Integração/Testing/Deploy | 3 | 1-2 |
| **Total** | **~16 semanas** | **2-3 devs** |

---

## Observações Finais

- **Não recriar o WP Admin** — estender com CPTs, ACF e páginas customizadas pontuais
- **WooCommerce Subscriptions** para billing — não reinventar a roda
- **React apenas onde necessário** — Client Area (SPA) + Admin widgets + Blocos Gutenberg
- **Tailwind via build** — zero runtime, design tokens no `theme.json`
- **Segurança first** — tokens de vídeo assinados, rate limit, CORS, sanitização

---

*Documento criado em: 2026-08-03*
*Baseado na análise do projeto F5 TV Streaming Platform (React/TypeScript/Node.js)*