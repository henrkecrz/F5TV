/**
 * F5TV Client Area - Main JS Application
 * Renderiza a Área do Assinante idêntica ao projeto React (AppHomePage.tsx)
 */

(function () {
    'use strict';

    const API_ROOT = (typeof f5tvClientData !== 'undefined' ? f5tvClientData.restUrl : '') || (typeof f5tvThemeData !== 'undefined' ? f5tvThemeData.restUrl : '') || '/wp-json/f5tv/v1/';
    const NONCE = (typeof f5tvClientData !== 'undefined' ? f5tvClientData.nonce : '') || (typeof f5tvThemeData !== 'undefined' ? f5tvThemeData.nonce : '');

    function authHeaders() {
        const headers = { 'Content-Type': 'application/json' };
        if (NONCE) headers['X-WP-Nonce'] = NONCE;
        return headers;
    }

    async function request(path, options = {}) {
        const url = API_ROOT + path.replace(/^\//, '');
        const res = await fetch(url, {
            ...options,
            headers: { ...authHeaders(), ...(options.headers || {}) },
            credentials: 'same-origin',
        });

        const text = await res.text();
        let data = null;
        try { data = JSON.parse(text); } catch (e) { data = text; }

        if (!res.ok) {
            throw new Error((data && data.message) || 'Erro na requisição.');
        }

        return data;
    }

    function setProfileUI(profile) {
        const container = document.getElementById('f5tv-current-profile');
        if (!container || !profile) return;
        container.innerHTML = `
            <div class="f5tv-profile-avatar ${profile.avatarColor || 'bg-f5-red'}" style="width:36px;height:36px;border-radius:0.5rem;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:900;font-size:14px;">
                ${profile.name ? profile.name.charAt(0).toUpperCase() : 'P'}
            </div>
            <span style="font-size:13px;font-weight:700;color:#fff;">${profile.name || 'Perfil'}</span>
        `;
    }

    async function bootDashboard() {
        const dashboard = document.getElementById('f5tv-dashboard-content');
        if (!dashboard) return;

        let profiles = [];
        try {
            profiles = await request('profiles');
        } catch (e) {
            profiles = [];
        }

        const activeProfileId = sessionStorage.getItem('f5tv_active_profile_id') || (profiles[0] ? profiles[0].id : '');
        let activeProfile = profiles.find(p => String(p.id) === String(activeProfileId)) || profiles[0] || null;

        if (activeProfile) {
            sessionStorage.setItem('f5tv_active_profile_id', String(activeProfile.id));
            setProfileUI(activeProfile);
        }

        // Render skeleton / initial containers matching AppHomePage.tsx
        dashboard.innerHTML = `
            <div id="subscriber-app-home" class="flex flex-col gap-8 text-white w-full">
                <!-- 1. Hero Banner Highlight -->
                <section id="f5tv-hero-banner" class="relative h-[55vh] flex items-end p-6 md:p-12 rounded-3xl border border-zinc-900 bg-black overflow-hidden select-none shadow-2xl">
                    <div id="f5tv-hero-bg" class="absolute inset-0 bg-cover bg-center opacity-50" style="background-image: url('https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=1200');"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-[#030315] via-[#030315]/60 to-transparent"></div>
                    
                    <div class="relative z-10 max-w-3xl flex flex-col items-start gap-4">
                        <div class="inline-flex items-center gap-1.5 bg-f5-red text-white font-mono font-black text-[10px] tracking-wider uppercase px-2.5 py-0.5 rounded-md shadow">
                            EM DESTAQUE F5 HOJE
                        </div>
                        <h1 id="f5tv-hero-title" class="text-3xl md:text-5xl font-black tracking-tight leading-none text-white">Carregando Destaque...</h1>
                        <p id="f5tv-hero-desc" class="text-zinc-300 text-xs md:text-sm leading-relaxed line-clamp-3 font-semibold max-w-2xl">Aguarde um momento enquanto carregamos o catálogo exclusivo.</p>
                        <div class="flex flex-wrap gap-3 mt-2">
                            <a id="f5tv-hero-play" href="/assista" class="bg-f5-red hover:bg-f5-red-700 text-white font-bold px-6 py-3 rounded-xl flex items-center gap-2 text-xs uppercase cursor-pointer tracking-wider font-mono transition shadow-lg shadow-f5-red-700/20">
                                &#9654; Assistir Agora
                            </a>
                        </div>
                    </div>
                </section>

                <!-- 2. Continuar Assistindo Row -->
                <section id="row-continue-container" class="w-full flex flex-col gap-4 hidden">
                    <h3 class="text-xs font-mono tracking-widest text-f5-red font-black uppercase flex items-center gap-2">
                        <span>CONTINUAR ASSISTINDO</span>
                    </h3>
                    <div id="f5tv-continue-items" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4"></div>
                </section>

                <!-- 3. Séries Investigativas & Exclusivas -->
                <section id="collection-series-container" class="w-full flex flex-col gap-4">
                    <h3 class="text-xs font-mono tracking-widest text-zinc-400 font-black uppercase flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-f5-red"></span>
                        <span>SÉRIES INVESTIGATIVAS & EXCLUSIVAS</span>
                    </h3>
                    <div id="f5tv-series-items" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"></div>
                </section>

                <!-- 4. Catálogo Completo por Categorias -->
                <section id="collection-catalog-container" class="w-full flex flex-col gap-4">
                    <h3 class="text-xs font-mono tracking-widest text-zinc-400 font-black uppercase flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-f5-red"></span>
                        <span>CATÁLOGO DE CONTEÚDOS</span>
                    </h3>
                    <div id="f5tv-catalog-items" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4"></div>
                </section>
            </div>
        `;

        loadCatalogData(activeProfile ? activeProfile.id : '');
    }

    async function loadCatalogData(profileId) {
        try {
            const catalog = await request('catalog');
            if (!Array.isArray(catalog) || catalog.length === 0) return;

            // 1. Fill Hero Banner
            const featured = catalog.find(c => c.isFeatured) || catalog[0];
            if (featured) {
                const heroBg = document.getElementById('f5tv-hero-bg');
                const heroTitle = document.getElementById('f5tv-hero-title');
                const heroDesc = document.getElementById('f5tv-hero-desc');
                const heroPlay = document.getElementById('f5tv-hero-play');

                if (heroBg && featured.bannerUrl) heroBg.style.backgroundImage = `url('${featured.bannerUrl}')`;
                if (heroTitle) heroTitle.textContent = featured.title;
                if (heroDesc) heroDesc.textContent = featured.shortDescription || featured.title;
                if (heroPlay) heroPlay.href = `/assista?id=${featured.id}`;
            }

            // 2. Fill Series Row
            const seriesContainer = document.getElementById('f5tv-series-items');
            const seriesItems = catalog.filter(c => c.contentType === 'series' || c.category === 'series' || (c.genre && c.genre.toLowerCase().includes('jornalismo')));
            const displaySeries = seriesItems.length > 0 ? seriesItems : catalog.slice(0, 6);

            if (seriesContainer) {
                seriesContainer.innerHTML = displaySeries.map(ser => `
                    <a href="/assista?id=${ser.id}" class="group relative bg-f5-blue-950 border border-zinc-900 rounded-2xl overflow-hidden hover:border-f5-red transition duration-300 shadow-xl flex flex-col">
                        <div class="aspect-[16/9] w-full bg-zinc-900 relative overflow-hidden">
                            <img src="${ser.bannerUrl || ser.coverUrl || ''}" alt="${ser.title}" class="w-full h-full object-cover opacity-70 group-hover:opacity-100 group-hover:scale-103 transition duration-300">
                            <div class="absolute top-3 left-3 bg-f5-red text-[9px] font-mono font-bold text-white px-2 py-0.5 rounded uppercase shadow">
                                SÉRIE F5 TV
                            </div>
                        </div>
                        <div class="p-5 flex flex-col gap-2 justify-between flex-1">
                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-mono font-bold text-f5-red uppercase tracking-wider">${ser.genre || 'Investigativo'}</span>
                                <h4 class="font-bold text-base text-white group-hover:text-f5-red transition">${ser.title}</h4>
                                <p class="text-zinc-400 text-xs line-clamp-2 leading-relaxed font-medium">${ser.shortDescription || ser.title}</p>
                            </div>
                        </div>
                    </a>
                `).join('');
            }

            // 3. Fill General Catalog Grid
            const catalogContainer = document.getElementById('f5tv-catalog-items');
            if (catalogContainer) {
                catalogContainer.innerHTML = catalog.map(item => `
                    <a href="/assista?id=${item.id}" class="group flex flex-col bg-f5-blue-950 border border-zinc-900 rounded-xl overflow-hidden hover:scale-103 transition duration-200 shadow-lg">
                        <div class="aspect-[3/4] w-full bg-zinc-900 relative">
                            <img src="${item.coverUrl || ''}" alt="${item.title}" class="w-full h-full object-cover group-hover:opacity-80 transition">
                            <div class="absolute top-2 right-2 bg-black/80 text-[10px] font-mono font-bold text-zinc-300 px-1.5 py-0.5 rounded border border-white/5">
                                ${item.ageRating || 'Livre'}
                            </div>
                            ${item.isExclusive ? `<div class="absolute bottom-2 left-2 bg-f5-red text-[9px] font-bold text-white px-1.5 py-0.5 rounded uppercase font-mono tracking-wider shadow">Exclusivo</div>` : ''}
                        </div>
                        <div class="p-3 flex flex-col gap-1">
                            <strong class="text-xs text-white line-clamp-1 group-hover:text-f5-red transition">${item.title}</strong>
                            <span class="text-[10px] font-mono text-zinc-500 uppercase">${item.genre || 'Streaming'}</span>
                        </div>
                    </a>
                `).join('');
            }

            // 4. Fill Watch History Row if available
            if (profileId) {
                try {
                    const history = await request(`watch-history?profile_id=${profileId}`);
                    if (Array.isArray(history) && history.length > 0) {
                        const continueSec = document.getElementById('row-continue-container');
                        const continueItems = document.getElementById('f5tv-continue-items');
                        if (continueSec && continueItems) {
                            continueSec.classList.remove('hidden');
                            continueItems.innerHTML = history.slice(0, 4).map(h => {
                                const item = catalog.find(c => String(c.id) === String(h.contentId)) || {};
                                return `
                                    <div class="bg-f5-blue-950 border border-zinc-900 hover:border-zinc-800 rounded-xl overflow-hidden flex flex-col gap-2 p-4">
                                        <div class="flex justify-between items-start gap-2">
                                            <div class="flex flex-col">
                                                <span class="text-[8px] font-mono tracking-wider font-extrabold text-zinc-500 uppercase">VISTO RECENTEMENTE</span>
                                                <h4 class="font-bold text-xs text-white line-clamp-1">${item.title || 'Conteúdo em progresso'}</h4>
                                            </div>
                                            <a href="/assista?id=${h.contentId}" class="p-2 bg-f5-red rounded-full text-white cursor-pointer hover:scale-105 transition flex items-center justify-center">
                                                &#9654;
                                            </a>
                                        </div>
                                        <div class="mt-2 flex flex-col gap-1 text-[10px] text-zinc-400 font-mono font-bold">
                                            <div class="flex justify-between">
                                                <span>Progresso:</span>
                                                <span>${Math.round(h.progress || 0)}%</span>
                                            </div>
                                            <div class="h-1 bg-zinc-900 rounded-lg overflow-hidden">
                                                <div class="h-full bg-f5-red rounded-lg" style="width: ${Math.min(100, h.progress || 0)}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            }).join('');
                        }
                    }
                } catch (e) {}
            }

        } catch (e) {
            console.error('Erro ao carregar dados do assinante:', e);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (document.getElementById('f5tv-client-dashboard')) {
            bootDashboard();
        }
    });
})();
