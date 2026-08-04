<?php
/**
 * Header template for F5TV Theme
 * Inclui o Menu Cascata Opaco de Alta Visibilidade das Seções do Assinante com Ícones Flat SVG.
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">

    <!-- ══ PWA — Web App Manifest ══ -->
    <link rel="manifest" href="<?php echo esc_url(home_url('/manifest.json')); ?>">

    <!-- ══ Theme / Brand colors ══ -->
    <meta name="theme-color" content="#030315">
    <meta name="msapplication-TileColor" content="#030315">
    <meta name="msapplication-TileImage" content="<?php echo esc_url(get_template_directory_uri()); ?>/assets/icons/icon-144.png">

    <!-- ══ iOS / Safari — Add to Home Screen ══ -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="F5 TV">
    <link rel="apple-touch-icon" href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/icons/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/icons/apple-touch-icon-152.png">

    <!-- iOS Splash Screens (portrait) -->
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-startup-image"
          media="screen and (device-width: 430px) and (device-height: 932px) and (-webkit-device-pixel-ratio: 3)"
          href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/icons/icon-512.png">
    <link rel="apple-touch-startup-image"
          media="screen and (device-width: 393px) and (device-height: 852px) and (-webkit-device-pixel-ratio: 3)"
          href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/icons/icon-512.png">
    <link rel="apple-touch-startup-image"
          media="screen and (device-width: 390px) and (device-height: 844px) and (-webkit-device-pixel-ratio: 3)"
          href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/icons/icon-512.png">

    <!-- ══ Favicons ══ -->
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/icons/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/icons/favicon-16.png">
    <link rel="shortcut icon" href="<?php echo esc_url(get_template_directory_uri()); ?>/assets/icons/favicon-32.png">

    <!-- ══ SEO / Social ══ -->
    <meta name="description" content="<?php echo is_singular() ? get_the_excerpt() : 'F5 TV — Séries originais, documentários investigativos, jornalismo 24h e esportes ao vivo. Streaming premium nacional.'; ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="F5 TV">
    <meta property="og:title" content="<?php wp_title('|', true, 'right'); ?> F5 TV">
    <meta property="og:description" content="Streaming premium nacional. Séries, documentários e ao vivo.">
    <meta property="og:image" content="<?php echo esc_url(get_template_directory_uri()); ?>/assets/icons/icon-512.png">
    <meta name="twitter:card" content="summary_large_image">

    <?php wp_head(); ?>
</head>

<body <?php body_class('bg-f5-blue text-white font-sans antialiased'); ?>>
<?php wp_body_open(); ?>

<div id="f5tv-app">
    <header class="sticky top-0 z-40 bg-[#060913]/95 backdrop-blur-md border-b border-zinc-800/80 px-4 sm:px-8 lg:px-0 py-3.5">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-4 lg:px-8">
            
            <!-- Logo F5 TV -->
            <a href="<?php echo esc_url(home_url('/')); ?>" class="flex items-center gap-3 group">
                <img src="<?php echo esc_url(F5TV_ASSETS_URI . '/images/f5tv-logo-neg.png'); ?>" alt="F5 TV" class="f5tv-site-logo" width="180" height="60">
                <?php if (is_front_page() || is_page('planos')): ?>
                    <span class="hidden sm:inline-flex items-center gap-1.5 bg-f5-red/10 text-f5-red px-2 py-0.5 text-[10px] font-mono font-bold rounded uppercase tracking-widest">
                        PREMIUM
                    </span>
                <?php endif; ?>
            </a>

            <!-- Navegação Principal -->
            <nav class="hidden lg:flex items-center gap-5 text-xs font-mono font-bold tracking-widest text-zinc-300 uppercase">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-white transition">Início</a>
                <a href="<?php echo esc_url(home_url('/series/')); ?>" class="hover:text-white transition">Séries</a>
                <a href="<?php echo esc_url(home_url('/ao-vivo/')); ?>" class="hover:text-white transition">Ao Vivo</a>
                <a href="<?php echo esc_url(home_url('/programacao/')); ?>" class="hover:text-white transition">Programação</a>
                <a href="<?php echo esc_url(home_url('/busca/')); ?>" class="hover:text-white transition">Busca</a>
                <a href="<?php echo esc_url(home_url('/planos/')); ?>" class="hover:text-white transition text-f5-red">Planos</a>
            </nav>

            <!-- Lado Direito: Ícone do Personagem / Avatar + Menu Cascata do Assinante -->
            <div class="flex items-center gap-3">
                <button type="button" id="f5tv-mobile-menu-btn" aria-expanded="false" aria-controls="f5tv-mobile-menu" class="lg:hidden relative z-[80] inline-flex items-center justify-center w-10 h-10 rounded-xl border border-zinc-700/80 bg-[#0e1424] text-zinc-200 hover:text-white hover:bg-[#161f36] transition cursor-pointer select-none" aria-label="Abrir menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>

                <a href="<?php echo esc_url(home_url('/planos/')); ?>" class="hidden lg:inline-flex bg-f5-red hover:bg-f5-red-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition uppercase tracking-wider shadow-lg shadow-f5-red-700/20">
                    Disponível em breve
                </a>

                <!-- Dropdown Menu de Personagem / Assinante -->
                <div class="relative group">
                    <button type="button" id="f5tv-user-dropdown-btn" aria-expanded="false" aria-controls="f5tv-user-dropdown" class="flex items-center gap-2.5 bg-[#0e1424] hover:bg-[#161f36] border border-zinc-700/80 p-1.5 pr-3 rounded-full transition cursor-pointer select-none shadow-md">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-f5-red to-red-600 flex items-center justify-center text-white font-black text-xs shadow border border-white/20">
                            <?php if (is_user_logged_in()): ?>
                                <?php echo esc_html(strtoupper(substr(wp_get_current_user()->display_name ?: wp_get_current_user()->user_login, 0, 1))); ?>
                            <?php else: ?>
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                            <?php endif; ?>
                        </div>
                        <span class="text-xs font-mono font-bold text-zinc-200 group-hover:text-white hidden sm:inline">
                            <?php echo is_user_logged_in() ? esc_html(wp_get_current_user()->display_name) : 'Assinante'; ?>
                        </span>
                        <svg class="w-3.5 h-3.5 text-zinc-400 group-hover:text-f5-red transition-transform duration-200 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- Menu Cascata (Dropdown) das Seções do Assinante - Fundo Sólido Opaco para Máxima Leitura -->
                    <div id="f5tv-user-dropdown" class="absolute right-0 top-full mt-2 w-64 bg-[#0a0f1d] border border-zinc-700 rounded-2xl shadow-[0_20px_60px_rgba(0,0,0,0.95)] overflow-hidden opacity-0 invisible pointer-events-none transition-all duration-200 z-[70] transform origin-top-right scale-95">
                        <div class="p-4 border-b border-zinc-800 bg-[#0e1424] flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-f5-red flex items-center justify-center text-white font-black text-sm shadow border border-white/20">
                                <?php if (is_user_logged_in()): ?>
                                    <?php echo esc_html(strtoupper(substr(wp_get_current_user()->user_login, 0, 1))); ?>
                                <?php else: ?>
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                <?php endif; ?>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-white leading-tight">
                                    <?php echo is_user_logged_in() ? esc_html(wp_get_current_user()->display_name) : 'Visitante F5 TV'; ?>
                                </span>
                                <span class="text-[10px] font-mono text-f5-red font-semibold uppercase tracking-wider">
                                    <?php echo is_user_logged_in() ? 'Assinatura Ativa' : 'Área do Assinante'; ?>
                                </span>
                            </div>
                        </div>

                        <nav class="p-2 flex flex-col gap-1 text-xs font-semibold text-zinc-200">
                            <?php if (is_user_logged_in()): ?>
                            <a href="<?php echo esc_url(home_url('/area-do-assinante/')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-f5-red/15 hover:text-white transition group/item">
                                <svg class="w-4 h-4 text-f5-red group-hover/item:scale-110 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                <span>Área do Assinante</span>
                            </a>
                            <a href="<?php echo esc_url(home_url('/series/')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-f5-red/15 hover:text-white transition group/item">
                                <svg class="w-4 h-4 text-f5-red group-hover/item:scale-110 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>
                                <span>Catálogo de Séries</span>
                            </a>
                            <a href="<?php echo esc_url(home_url('/ao-vivo/')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-f5-red/15 hover:text-white transition group/item">
                                <svg class="w-4 h-4 text-f5-red group-hover/item:scale-110 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>Canais Ao Vivo</span>
                            </a>
                            <a href="<?php echo esc_url(home_url('/minha-lista/')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-f5-red/15 hover:text-white transition group/item">
                                <svg class="w-4 h-4 text-f5-red group-hover/item:scale-110 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                <span>Minha Lista</span>
                            </a>
                            <a href="<?php echo esc_url(home_url('/continuar-assistindo/')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-f5-red/15 hover:text-white transition group/item">
                                <svg class="w-4 h-4 text-f5-red group-hover/item:scale-110 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Continuar Assistindo</span>
                            </a>
                            <a href="<?php echo esc_url(home_url('/busca/')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-f5-red/15 hover:text-white transition group/item">
                                <svg class="w-4 h-4 text-f5-red group-hover/item:scale-110 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                <span>Buscar Conteúdo</span>
                            </a>
                            <a href="<?php echo esc_url(home_url('/dispositivos/')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-f5-red/15 hover:text-white transition group/item">
                                <svg class="w-4 h-4 text-f5-red group-hover/item:scale-110 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                <span>Dispositivos Conectados</span>
                            </a>
                            <a href="<?php echo esc_url(home_url('/minha-conta/')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-f5-red/15 hover:text-white transition group/item">
                                <svg class="w-4 h-4 text-f5-red group-hover/item:scale-110 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span>Minha Conta</span>
                            </a>
                            <a href="<?php echo esc_url(home_url('/planos/')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-f5-red/15 hover:text-white transition group/item">
                                <svg class="w-4 h-4 text-f5-red group-hover/item:scale-110 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>Planos & Assinatura</span>
                            </a>

                            <?php endif; ?>

                            <?php if (is_user_logged_in()): ?><div class="my-1.5 border-t border-zinc-800"></div><?php endif; ?>

                            <?php if (is_user_logged_in()): ?>
                                <a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-f5-red hover:bg-f5-red/15 transition font-bold group/item">
                                    <svg class="w-4 h-4 text-f5-red group-hover/item:scale-110 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    <span>Sair da Conta</span>
                                </a>
                            <?php else: ?>
                                <a href="<?php echo esc_url(home_url('/login/')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-f5-red hover:bg-f5-red/15 transition font-bold group/item" aria-label="Fazer login">
                                    <svg class="w-4 h-4 text-f5-red group-hover/item:scale-110 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                    <span>Entrar</span>
                                </a>
                                <a href="<?php echo esc_url(home_url('/cadastro/')); ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-white hover:bg-f5-red/15 transition font-bold group/item" aria-label="Criar conta">
                                    <svg class="w-4 h-4 text-f5-red group-hover/item:scale-110 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    <span>Cadastrar</span>
                                </a>
                            <?php endif; ?>
                        </nav>
                    </div>
                </div>

            </div>
        </div>
    </header>

    <div id="f5tv-mobile-menu" class="hidden lg:hidden fixed inset-x-0 top-[4.5rem] z-[75] bg-[#071a33] border-b border-zinc-700/80 shadow-2xl p-4">
        <nav class="flex flex-col gap-1 text-xs font-mono font-bold tracking-widest text-zinc-200 uppercase">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="px-3 py-3 rounded-lg hover:bg-f5-red/15 hover:text-white transition">Início</a>
            <a href="<?php echo esc_url(home_url('/series/')); ?>" class="px-3 py-3 rounded-lg hover:bg-f5-red/15 hover:text-white transition">Séries</a>
            <a href="<?php echo esc_url(home_url('/ao-vivo/')); ?>" class="px-3 py-3 rounded-lg hover:bg-f5-red/15 hover:text-white transition">Ao Vivo</a>
            <a href="<?php echo esc_url(home_url('/programacao/')); ?>" class="px-3 py-3 rounded-lg hover:bg-f5-red/15 hover:text-white transition">Programação</a>
            <a href="<?php echo esc_url(home_url('/busca/')); ?>" class="px-3 py-3 rounded-lg hover:bg-f5-red/15 hover:text-white transition">Busca</a>
            <a href="<?php echo esc_url(home_url('/planos/')); ?>" class="px-3 py-3 rounded-lg text-f5-red hover:bg-f5-red/15 hover:text-white transition">Planos</a>
        </nav>
    </div>

    <script>
    (function () {
        var menuButton = document.getElementById('f5tv-mobile-menu-btn');
        var mobileMenu = document.getElementById('f5tv-mobile-menu');
        var userButton = document.getElementById('f5tv-user-dropdown-btn');
        var userMenu = document.getElementById('f5tv-user-dropdown');

        function setVisible(element, visible) {
            if (!element) return;
            element.classList.toggle('hidden', !visible);
            element.classList.toggle('opacity-100', visible);
            element.classList.toggle('visible', visible);
            element.classList.toggle('pointer-events-auto', visible);
            element.classList.toggle('scale-100', visible);
            element.classList.toggle('opacity-0', !visible);
            element.classList.toggle('invisible', !visible);
            element.classList.toggle('pointer-events-none', !visible);
            element.classList.toggle('scale-95', !visible);
        }

        if (menuButton && mobileMenu) {
            menuButton.addEventListener('click', function () {
                var visible = mobileMenu.classList.contains('hidden');
                setVisible(mobileMenu, visible);
                menuButton.setAttribute('aria-expanded', visible ? 'true' : 'false');
            });
            mobileMenu.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', function () {
                    setVisible(mobileMenu, false);
                    menuButton.setAttribute('aria-expanded', 'false');
                });
            });
        }

        if (userButton && userMenu) {
            userButton.addEventListener('click', function (event) {
                event.stopPropagation();
                var visible = !userMenu.classList.contains('pointer-events-auto');
                setVisible(userMenu, visible);
                userButton.setAttribute('aria-expanded', visible ? 'true' : 'false');
            });
            userMenu.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', function () {
                    setVisible(userMenu, false);
                    userButton.setAttribute('aria-expanded', 'false');
                });
            });
            document.addEventListener('click', function () {
                setVisible(userMenu, false);
                userButton.setAttribute('aria-expanded', 'false');
            });
        }
    }());
    </script>

    <main id="f5tv-main">
