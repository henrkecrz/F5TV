# Deploy na Hostinger (React/Vite)

Esse erro acontece quando você envia o codigo-fonte do React/Vite para a Hostinger em vez do build final.

A hospedagem comum nao executa `.tsx` direto. Se o `index.html` referencia `/src/main.tsx`, o deploy esta incorreto.

## 1) Configurar variaveis de producao

Crie (ou atualize) o arquivo `.env.production` na raiz:

```env
VITE_USE_HOSTINGER_API=true
VITE_API_URL=https://ivory-termite-106792.hostingersite.com/api
```

## 2) Gerar o build

No projeto local:

```bash
npm install
npm run build
```

Isso cria a pasta `dist/` com arquivos prontos para navegador, por exemplo:

```text
dist/
  index.html
  assets/
    index-xxxxx.js
    index-xxxxx.css
```

## 3) O que enviar para `public_html/`

Envie somente o conteudo de `dist/` (nao envie `src/`, `package.json`, etc.).

Estrutura final esperada:

```text
public_html/
  index.html
  assets/
    index-xxxxx.js
    index-xxxxx.css
  api/
    _bootstrap.php
    config.php
    config.example.php
    health.php
    catalog.php
    live.php
    billing.php
```

## 4) O que remover de `public_html/` (se existir)

```text
src/
server/
database/
php-api/
package.json
package-lock.json
vite.config.ts
tsconfig.json
main.tsx
```

Mantenha apenas:

```text
index.html
assets/
api/
```

## 5) Como validar

No `index.html` publicado, **nao** pode existir:

```html
<script type="module" src="/src/main.tsx">
```

Deve existir algo como:

```html
<script type="module" crossorigin src="/assets/index-abc123.js">
<link rel="stylesheet" crossorigin href="/assets/index-abc123.css">
```

## 6) Se der erro ao atualizar rota (SPA)

Crie/edite `public_html/.htaccess` com:

```apache
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteBase /

  RewriteCond %{REQUEST_URI} !^/api/
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule . /index.html [L]
</IfModule>

AddType application/javascript .js
AddType text/css .css
```

## Resumo

O erro ocorre quando o deploy foi feito em modo desenvolvimento. Gere `npm run build` e publique apenas o conteudo de `dist/`, mantendo `api/` no `public_html/`.
