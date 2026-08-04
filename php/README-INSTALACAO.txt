═══════════════════════════════════════════════════════
  F5 TV — GUIA DE INSTALAÇÃO EM HOSPEDAGEM WORDPRESS
═══════════════════════════════════════════════════════

FICHEIROS DISPONÍVEIS
─────────────────────
  f5tv-theme.zip     (0.83 MB)  → Tema principal
  f5tv-setup.zip     (0.01 MB)  → Wizard de instalação automático
  f5tv-plugins.zip   (0.08 MB)  → Plugins custom (via FTP)
  elementor.zip      (22.7 MB)  → Plugin Elementor
  manifest.json                 → PWA — copiar para raiz do servidor
  sw.js                         → PWA Service Worker — copiar para raiz


══════════════════════════════════════════════════════
  PASSO 1 — INSTALAR O ELEMENTOR
══════════════════════════════════════════════════════

  Painel WP → Plugins → Adicionar Novo → Enviar Plugin
  → Selecionar: elementor.zip → Instalar → Ativar


══════════════════════════════════════════════════════
  PASSO 2 — INSTALAR O TEMA
══════════════════════════════════════════════════════

  Painel WP → Aparência → Temas → Adicionar Novo → Enviar Tema
  → Selecionar: f5tv-theme.zip → Instalar → Ativar

  ⚠ Instale apenas f5tv-theme.zip aqui.
    Este ZIP contém somente o tema (como o WP exige).


══════════════════════════════════════════════════════
  PASSO 3 — INSTALAR PLUGINS CUSTOM VIA FTP
══════════════════════════════════════════════════════

  Descompactar f5tv-plugins.zip e enviar as 3 pastas para:
    /wp-content/plugins/f5tv-setup/
    /wp-content/plugins/f5tv-admin-panel/
    /wp-content/plugins/f5tv-client-area/

  Depois: Painel WP → Plugins → Ativar os 3 plugins.


══════════════════════════════════════════════════════
  PASSO 4 — EXECUTAR O SETUP WIZARD (automático)
══════════════════════════════════════════════════════

  Ao ativar o plugin "F5TV — Setup Wizard", o painel
  redireciona automaticamente para o assistente que faz:

  ┌─────────────────────────────────────────────────┐
  │  Passo 1 → Verificação do servidor (PHP, WP)    │
  │  Passo 2 → Confirmação do tema + permalinks     │
  │  Passo 3 → Instalar/ativar todos os plugins     │
  │  Passo 4 → Criar 16 páginas com templates certos│
  │  Passo 5 → Copiar manifest.json + sw.js (PWA)   │
  │  Passo 6 → Concluir e fechar wizard             │
  └─────────────────────────────────────────────────┘

  No final, o wizard desativa-se automaticamente.


══════════════════════════════════════════════════════
  PASSO 5 — PWA (se o wizard não conseguiu copiar)
══════════════════════════════════════════════════════

  Via FTP, copiar para a RAIZ do servidor (public_html/):
    manifest.json
    sw.js


══════════════════════════════════════════════════════
  ALTERNATIVA: INSTALAR SETUP WIZARD VIA WP ADMIN
══════════════════════════════════════════════════════

  Se não tiver acesso FTP:
  Plugins → Adicionar Novo → Enviar Plugin
  → Selecionar: f5tv-setup.zip → Instalar → Ativar
  → Redireciona automaticamente para o wizard


══════════════════════════════════════════════════════
  NOTAS
══════════════════════════════════════════════════════

  • PHP 8.1+ obrigatório
  • WordPress 6.4+ obrigatório
  • O tema usa Tailwind CSS compilado (não requer build)
  • Não requer ACF (Advanced Custom Fields)
  • Login fica em /login (wp-login.php redireciona)
  • Acesso admin sempre via /wp-admin

  Email: contato@f5tv.com.br

═══════════════════════════════════════════════════════
