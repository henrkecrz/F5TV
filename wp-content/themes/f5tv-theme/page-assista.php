<?php
/**
 * Template Name: Player Assista
 * Player imersivo fullscreen — visual moderno sem branding externo.
 * Suporta Vimeo (SDK controls=0), YouTube (IFrame API controls=0) e MP4 nativo.
 */

get_header();

$content_id   = isset($_GET['id']) ? intval($_GET['id']) : 0;
$episode_id   = isset($_GET['episodeId']) ? intval($_GET['episodeId']) : 0;
$is_trailer   = isset($_GET['trailer']) && $_GET['trailer'] === 'true';
$content_post = $content_id ? get_post($content_id) : null;

if ($content_post) {
    $video_url   = f5tv_get_field('video_url', $content_id) ?: 'https://vimeo.com/76979871';
    $trailer_url = f5tv_get_field('trailer_url', $content_id);
    $cover_url   = f5tv_get_field('cover_url', $content_id) ?: get_the_post_thumbnail_url($content_id, 'large') ?: '';
    $banner_url  = f5tv_get_field('banner_url', $content_id) ?: $cover_url;
    $title       = get_the_title($content_post);
    $subtitle    = '';
    $poster_url  = $cover_url ?: $banner_url;

    if ($is_trailer && $trailer_url) {
        $video_url = $trailer_url;
        $subtitle  = 'Teaser / Trailer Oficial';
    } elseif ($episode_id) {
        $ep_post = get_post($episode_id);
        if ($ep_post) {
            $ep_video = f5tv_get_field('video_url', $episode_id);
            $ep_thumb = f5tv_get_field('thumbnail_url', $episode_id) ?: get_the_post_thumbnail_url($episode_id, 'large');
            $ep_num   = f5tv_get_field('number', $episode_id) ?: 1;
            if ($ep_video) $video_url = $ep_video;
            if ($ep_thumb) $poster_url = $ep_thumb;
            $subtitle = 'Episódio ' . $ep_num . ': ' . get_the_title($ep_post);
        }
    }

    // Próximo episódio
    $next_ep_url = null;
    if ($episode_id) {
        $cur_ep_num    = (int)(f5tv_get_field('number', $episode_id) ?: 1);
        $cur_season_id = f5tv_get_field('season_id', $episode_id);
        if ($cur_season_id) {
            $next_eps = get_posts([
                'post_type' => 'f5tv_episodio', 'posts_per_page' => 1,
                'meta_query' => [
                    ['key' => 'season_id', 'value' => $cur_season_id],
                    ['key' => 'number', 'value' => $cur_ep_num + 1, 'type' => 'NUMERIC'],
                ],
            ]);
            if (!empty($next_eps)) {
                $next_ep_url = home_url('/assista?id=' . $content_id . '&episodeId=' . $next_eps[0]->ID);
            }
        }
    }

    $back_url = get_permalink($content_id) ?: home_url('/catalogo/');
} else {
    $video_url   = 'https://vimeo.com/76979871';
    $title       = 'Conteúdo Indisponível';
    $subtitle    = '';
    $poster_url  = '';
    $back_url    = home_url('/catalogo/');
    $next_ep_url = null;
}

// Detectar tipo de vídeo
$is_vimeo   = preg_match('/vimeo\.com\/(\d+)/i', $video_url, $vimeo_match);
$is_youtube = preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/i', $video_url, $yt_match);
$is_mp4     = !$is_vimeo && !$is_youtube;

$vimeo_id = $is_vimeo ? $vimeo_match[1] : '';
$yt_id    = $is_youtube ? $yt_match[1] : '';
?>

<div id="f5-player-root"
     class="fixed inset-0 bg-black z-50 overflow-hidden text-white font-sans select-none"
     style="font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;">

    <!-- Poster blurred BG -->
    <?php if ($poster_url): ?>
    <div id="f5-poster-bg"
         style="position:absolute;inset:0;background:url('<?php echo esc_url($poster_url); ?>') center/cover no-repeat;opacity:.18;filter:blur(20px);transform:scale(1.08);z-index:0;"></div>
    <?php endif; ?>
    <div style="position:absolute;inset:0;background:rgba(0,0,0,.5);z-index:1;"></div>

    <!-- ── VIDEO LAYER ── -->
    <div id="f5-video-layer" style="position:absolute;inset:0;z-index:2;display:flex;align-items:center;justify-content:center;">

        <?php if ($is_vimeo): ?>
        <!-- Vimeo: controls=0 + SDK para controlo total -->
        <div id="f5-vimeo-wrapper" style="position:absolute;inset:0;">
            <iframe id="f5-vimeo-iframe"
                    src="https://player.vimeo.com/video/<?php echo esc_attr($vimeo_id); ?>?autoplay=1&controls=0&title=0&byline=0&portrait=0&badge=0&autopause=0&transparent=0&background=0&color=dc2626&muted=0"
                    style="position:absolute;inset:0;width:100%;height:100%;border:0;"
                    allow="autoplay; fullscreen; picture-in-picture"
                    allowfullscreen>
            </iframe>
        </div>

        <?php elseif ($is_youtube): ?>
        <!-- YouTube: controls=0 + IFrame API -->
        <div id="f5-yt-wrapper" style="position:absolute;inset:0;pointer-events:none;">
            <div id="f5-yt-player" style="position:absolute;inset:0;width:100%;height:100%;"></div>
        </div>

        <?php else: ?>
        <!-- MP4 nativo -->
        <video id="f5-html5-video"
               src="<?php echo esc_url($video_url); ?>"
               poster="<?php echo esc_url($poster_url); ?>"
               style="position:absolute;inset:0;width:100%;height:100%;object-fit:contain;"
               playsinline preload="metadata">
        </video>
        <?php endif; ?>

        <!-- Click overlay para play/pause (cobre o iframe) -->
        <div id="f5-click-overlay" style="position:absolute;inset:0;z-index:5;cursor:pointer;"></div>
    </div>

    <!-- ── CENTER PLAY BUTTON ── -->
    <div id="f5-center-btn"
         style="position:absolute;z-index:20;display:flex;align-items:center;justify-content:center;width:80px;height:80px;background:rgba(220,38,38,.92);border-radius:50%;box-shadow:0 8px 40px rgba(0,0,0,.6);transition:transform .15s,opacity .25s;cursor:pointer;"
         title="Reproduzir">
        <svg id="f5-center-icon" width="34" height="34" viewBox="0 0 24 24" fill="white">
            <path d="M8 5v14l11-7z"/>
        </svg>
    </div>

    <!-- ── TOP BAR ── -->
    <div id="f5-top-bar"
         style="position:absolute;top:0;left:0;right:0;z-index:30;padding:20px 28px;display:flex;align-items:center;justify-content:space-between;background:linear-gradient(to bottom,rgba(0,0,0,.88) 0%,transparent 100%);transition:opacity .3s;">
        <div style="display:flex;flex-direction:column;gap:2px;">
            <span style="font-size:11px;font-family:monospace;letter-spacing:.15em;color:#dc2626;font-weight:700;text-transform:uppercase;">F5 TV PLAYER</span>
            <h2 style="font-size:clamp(14px,2vw,22px);font-weight:800;letter-spacing:-.02em;margin:0;line-height:1.2;"><?php echo esc_html($title); ?></h2>
            <?php if ($subtitle): ?>
            <p style="font-size:13px;color:#a1a1aa;margin:0;"><?php echo esc_html($subtitle); ?></p>
            <?php endif; ?>
        </div>
        <a href="<?php echo esc_url($back_url); ?>" id="f5-close-btn"
           style="width:44px;height:44px;border-radius:50%;background:rgba(10,10,48,.7);border:1px solid rgba(255,255,255,.12);display:flex;align-items:center;justify-content:center;color:#d4d4d8;text-decoration:none;transition:background .2s,color .2s;"
           title="Fechar player">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </a>
    </div>

    <!-- ── BOTTOM CONTROLS (YouTube-style) ── -->
    <div id="f5-bottom-bar"
         style="position:absolute;bottom:0;left:0;right:0;z-index:30;padding:0 20px 16px;background:linear-gradient(to top,rgba(0,0,0,.95) 0%,rgba(0,0,0,.6) 60%,transparent 100%);transition:opacity .3s;">

        <!-- Progress bar -->
        <div id="f5-progress-area" style="position:relative;height:16px;display:flex;align-items:center;cursor:pointer;margin-bottom:6px;" title="Linha do tempo">
            <!-- Track -->
            <div style="position:absolute;left:0;right:0;height:3px;background:rgba(255,255,255,.2);border-radius:2px;transition:height .15s;" id="f5-track"></div>
            <!-- Buffer -->
            <div id="f5-buffer-bar" style="position:absolute;left:0;height:3px;background:rgba(255,255,255,.35);border-radius:2px;width:0%;transition:width .5s linear;"></div>
            <!-- Progress -->
            <div id="f5-progress-fill" style="position:absolute;left:0;height:3px;background:#dc2626;border-radius:2px;width:0%;"></div>
            <!-- Thumb -->
            <div id="f5-progress-thumb" style="position:absolute;width:13px;height:13px;background:#dc2626;border-radius:50%;left:0%;transform:translateX(-50%);opacity:0;transition:opacity .15s;box-shadow:0 0 6px rgba(220,38,38,.8);"></div>
            <!-- Invisible scrubber -->
            <input id="f5-scrubber" type="range" min="0" max="100" step="0.05" value="0"
                   style="position:absolute;inset:0;width:100%;opacity:0;cursor:pointer;margin:0;" aria-label="Linha do tempo">
        </div>

        <!-- Controls row -->
        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">

            <!-- LEFT -->
            <div style="display:flex;align-items:center;gap:4px;">
                <!-- Play/Pause -->
                <button id="f5-play-btn" class="f5-ctrl-btn" title="Reproduzir (k)">
                    <svg id="f5-play-icon" width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                </button>

                <!-- Next episode -->
                <?php if ($next_ep_url): ?>
                <a href="<?php echo esc_url($next_ep_url); ?>" class="f5-ctrl-btn" title="Próximo episódio">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z"/></svg>
                </a>
                <?php endif; ?>

                <!-- Volume group -->
                <div id="f5-vol-group" style="display:flex;align-items:center;gap:4px;">
                    <button id="f5-mute-btn" class="f5-ctrl-btn" title="Mudo (m)">
                        <svg id="f5-vol-icon" width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                            <path id="f5-vol-path" d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>
                        </svg>
                    </button>
                    <div id="f5-vol-slider-wrap" style="width:0;overflow:hidden;transition:width .2s;display:flex;align-items:center;">
                        <input id="f5-vol-slider" type="range" min="0" max="1" step="0.02" value="0.85"
                               style="width:80px;height:3px;accent-color:#dc2626;cursor:pointer;background:rgba(255,255,255,.2);border-radius:2px;"
                               aria-label="Volume">
                    </div>
                </div>

                <!-- Time -->
                <div style="display:flex;align-items:center;gap:4px;font-size:13px;font-family:monospace;color:#d4d4d8;padding-left:6px;white-space:nowrap;">
                    <span id="f5-cur-time">0:00</span>
                    <span style="color:#71717a;">/</span>
                    <span id="f5-dur">0:00</span>
                </div>
            </div>

            <!-- RIGHT -->
            <div style="display:flex;align-items:center;gap:2px;">
                <!-- Quality badge -->
                <button class="f5-ctrl-btn" style="font-size:11px;font-family:monospace;font-weight:700;letter-spacing:.05em;padding:3px 7px;border:1px solid rgba(255,255,255,.25);border-radius:3px;line-height:1.4;" title="Qualidade">
                    HD
                </button>

                <!-- Settings -->
                <button class="f5-ctrl-btn" title="Configurações">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19.14,12.94c0.04-0.3,0.06-0.61,0.06-0.94c0-0.32-0.02-0.64-0.07-0.94l2.03-1.58c0.18-0.14,0.23-0.41,0.12-0.61 l-1.92-3.32c-0.12-0.22-0.37-0.29-0.59-0.22l-2.39,0.96c-0.5-0.38-1.03-0.7-1.62-0.94L14.4,2.81c-0.04-0.24-0.24-0.41-0.48-0.41 h-3.84c-0.24,0-0.43,0.17-0.47,0.41L9.25,5.35C8.66,5.59,8.12,5.92,7.63,6.29L5.24,5.33c-0.22-0.08-0.47,0-0.59,0.22L2.74,8.87 C2.62,9.08,2.66,9.34,2.86,9.48l2.03,1.58C4.84,11.36,4.8,11.69,4.8,12s0.02,0.64,0.07,0.94l-2.03,1.58 c-0.18,0.14-0.23,0.41-0.12,0.61l1.92,3.32c0.12,0.22,0.37,0.29,0.59,0.22l2.39-0.96c0.5,0.38,1.03,0.7,1.62,0.94l0.36,2.54 c0.05,0.24,0.24,0.41,0.48,0.41h3.84c0.24,0,0.44-0.17,0.47-0.41l0.36-2.54c0.59-0.24,1.13-0.56,1.62-0.94l2.39,0.96 c0.22,0.08,0.47,0,0.59-0.22l1.92-3.32c0.12-0.22,0.07-0.47-0.12-0.61L19.14,12.94z M12,15.6c-1.98,0-3.6-1.62-3.6-3.6 s1.62-3.6,3.6-3.6s3.6,1.62,3.6,3.6S13.98,15.6,12,15.6z"/>
                    </svg>
                </button>

                <!-- Fullscreen -->
                <button id="f5-fs-btn" class="f5-ctrl-btn" title="Tela cheia (f)">
                    <svg id="f5-fs-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path id="f5-fs-path" d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- ── LOADING SPINNER ── -->
    <div id="f5-spinner" style="position:absolute;z-index:25;pointer-events:none;">
        <div style="width:52px;height:52px;border:3px solid rgba(255,255,255,.12);border-top-color:#dc2626;border-radius:50%;animation:f5spin .8s linear infinite;"></div>
    </div>

    <!-- ── NO CONTENT STATE ── -->
    <?php if (!$content_post): ?>
    <div style="position:absolute;inset:0;z-index:40;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;text-align:center;padding:32px;">
        <svg width="52" height="52" fill="none" stroke="#dc2626" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <h2 style="font-size:18px;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:#dc2626;margin:0;">Mídia Indisponível</h2>
        <p style="color:#71717a;font-size:13px;font-family:monospace;max-width:280px;margin:0;">Conteúdo não encontrado ou sem vídeo cadastrado.</p>
        <a href="<?php echo esc_url(home_url('/catalogo/')); ?>"
           style="margin-top:8px;padding:8px 20px;background:rgba(10,10,48,.8);border:1px solid rgba(255,255,255,.15);border-radius:6px;font-family:monospace;font-size:12px;text-transform:uppercase;color:#fff;text-decoration:none;transition:background .2s;">
            ← Voltar ao Catálogo
        </a>
    </div>
    <?php endif; ?>
</div>

<style>
@keyframes f5spin { to { transform: rotate(360deg); } }

.f5-ctrl-btn {
    display: flex; align-items: center; justify-content: center;
    width: 40px; height: 40px; border-radius: 4px;
    background: transparent; border: none; color: #d4d4d8;
    cursor: pointer; transition: color .15s, background .15s;
    padding: 0; flex-shrink: 0;
}
.f5-ctrl-btn:hover { color: #fff; background: rgba(255,255,255,.08); }

#f5-progress-area:hover #f5-track      { height: 5px; }
#f5-progress-area:hover #f5-buffer-bar { height: 5px; }
#f5-progress-area:hover #f5-progress-fill { height: 5px; }
#f5-progress-area:hover #f5-progress-thumb { opacity: 1; }

#f5-vol-group:hover #f5-vol-slider-wrap { width: 88px; }

#f5-close-btn:hover { background: rgba(220,38,38,.25) !important; color: #fff !important; }
</style>

<script>
(function () {
    /* ── utils ── */
    const fmt = s => {
        if (!isFinite(s) || isNaN(s)) return '0:00';
        const m = Math.floor(s / 60), sec = Math.floor(s % 60);
        return m + ':' + String(sec).padStart(2, '0');
    };

    const $ = id => document.getElementById(id);

    /* ── element refs ── */
    const root       = $('f5-player-root');
    const topBar     = $('f5-top-bar');
    const bottomBar  = $('f5-bottom-bar');
    const centerBtn  = $('f5-center-btn');
    const centerIcon = $('f5-center-icon');
    const clickOvl   = $('f5-click-overlay');
    const playBtn    = $('f5-play-btn');
    const playIcon   = $('f5-play-icon');
    const muteBtn    = $('f5-mute-btn');
    const volIcon    = $('f5-vol-icon');
    const volPath    = $('f5-vol-path');
    const volSlider  = $('f5-vol-slider');
    const scrubber   = $('f5-scrubber');
    const progFill   = $('f5-progress-fill');
    const progThumb  = $('f5-progress-thumb');
    const bufBar     = $('f5-buffer-bar');
    const curTimeEl  = $('f5-cur-time');
    const durEl      = $('f5-dur');
    const fsBtn      = $('f5-fs-btn');
    const fsPath     = $('f5-fs-path');
    const spinner    = $('f5-spinner');

    /* ── state ── */
    let isPlaying  = false;
    let duration   = 0;
    let volume     = 0.85;
    let isMuted    = false;
    let hideTimer  = null;
    const HIDE_MS  = 3500;

    /* ── SVG paths ── */
    const PLAY_D    = 'M8 5v14l11-7z';
    const PAUSE_D   = 'M6 19h4V5H6v14zm8-14v14h4V5h-4z';
    const FS_EXP    = 'M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z';
    const FS_COMP   = 'M5 16h3v3h2v-5H5v2zm3-8H5v2h5V5H8v3zm6 11h2v-3h3v-2h-5v5zm2-11V5h-2v5h5V8h-3z';
    const VOL_ON    = 'M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z';
    const VOL_OFF   = 'M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z';

    /* ── controls visibility ── */
    function showUI(force) {
        topBar.style.opacity    = '1';
        bottomBar.style.opacity = '1';
        topBar.style.pointerEvents    = 'auto';
        bottomBar.style.pointerEvents = 'auto';
        if (!isPlaying || force) {
            centerBtn.style.opacity       = '1';
            centerBtn.style.pointerEvents = 'auto';
        }
        clearTimeout(hideTimer);
        if (isPlaying) hideTimer = setTimeout(hideUI, HIDE_MS);
    }

    function hideUI() {
        if (!isPlaying) return;
        topBar.style.opacity    = '0';
        bottomBar.style.opacity = '0';
        topBar.style.pointerEvents    = 'none';
        bottomBar.style.pointerEvents = 'none';
        centerBtn.style.opacity       = '0';
        centerBtn.style.pointerEvents = 'none';
    }

    root.addEventListener('mousemove', () => showUI());
    root.addEventListener('touchstart', () => showUI(), { passive: true });

    /* ── progress update ── */
    function setProgress(cur, dur_) {
        if (dur_ && dur_ > 0) {
            const pct = (cur / dur_) * 100;
            progFill.style.width  = pct + '%';
            progThumb.style.left  = pct + '%';
            scrubber.value        = pct;
        }
        curTimeEl.textContent = fmt(cur);
        if (dur_ && dur_ > 0) durEl.textContent = fmt(dur_);
    }

    function setPlayState(playing) {
        isPlaying = playing;
        playIcon.querySelector('path')?.setAttribute('d', playing ? PAUSE_D : PLAY_D);
        centerIcon.setAttribute('d', playing ? '' : PLAY_D);
        centerBtn.style.opacity       = playing ? '0' : '1';
        centerBtn.style.pointerEvents = playing ? 'none' : 'auto';
        spinner.style.display = 'none';
        if (playing) hideTimer = setTimeout(hideUI, HIDE_MS);
    }

    /* ── volume ── */
    function applyVolume(v, muted) {
        volume  = v;
        isMuted = muted;
        const show = muted || v === 0;
        volPath.setAttribute('d', show ? VOL_OFF : VOL_ON);
        if (!muted) volSlider.value = v;
    }

    /* ── fullscreen ── */
    function toggleFs() {
        if (!document.fullscreenElement) {
            root.requestFullscreen?.().catch(() => {});
            fsPath.setAttribute('d', FS_COMP);
        } else {
            document.exitFullscreen?.();
            fsPath.setAttribute('d', FS_EXP);
        }
    }

    document.addEventListener('fullscreenchange', () => {
        fsPath.setAttribute('d', document.fullscreenElement ? FS_COMP : FS_EXP);
    });

    /* ════════════════════════════════════════
       VIMEO
    ════════════════════════════════════════ */
    <?php if ($is_vimeo): ?>
    let player = null;

    function initVimeo() {
        if (typeof Vimeo === 'undefined' || !Vimeo.Player) {
            setTimeout(initVimeo, 200);
            return;
        }
        const iframe = $('f5-vimeo-iframe');
        player = new Vimeo.Player(iframe);

        player.ready().then(() => {
            player.setVolume(volume);
            player.getDuration().then(d => { duration = d; durEl.textContent = fmt(d); });
            spinner.style.display = 'none';
        });

        player.on('play',   () => setPlayState(true));
        player.on('pause',  () => setPlayState(false));
        player.on('ended',  () => setPlayState(false));
        player.on('bufferstart', () => { spinner.style.display = 'flex'; });
        player.on('bufferend',   () => { spinner.style.display = 'none'; });

        player.on('timeupdate', data => {
            setProgress(data.seconds, data.duration || duration);
        });

        player.on('loaded', () => {
            player.getDuration().then(d => { duration = d; durEl.textContent = fmt(d); });
        });
    }

    // Toggle play/pause
    function togglePlay() {
        if (!player) return;
        player.getPaused().then(p => { p ? player.play() : player.pause(); });
    }

    clickOvl.addEventListener('click', togglePlay);
    centerBtn.addEventListener('click', togglePlay);
    playBtn.addEventListener('click', togglePlay);

    // Scrubber
    scrubber.addEventListener('input', e => {
        if (!player || !duration) return;
        const t = (parseFloat(e.target.value) / 100) * duration;
        player.setCurrentTime(t);
        progFill.style.width = e.target.value + '%';
        progThumb.style.left = e.target.value + '%';
    });

    // Volume
    muteBtn.addEventListener('click', () => {
        isMuted = !isMuted;
        player?.setVolume(isMuted ? 0 : volume);
        applyVolume(volume, isMuted);
    });

    volSlider.addEventListener('input', e => {
        const v = parseFloat(e.target.value);
        player?.setVolume(v);
        applyVolume(v, v === 0);
    });

    // Fullscreen
    fsBtn.addEventListener('click', toggleFs);

    // Load SDK
    const sdk = document.createElement('script');
    sdk.src = 'https://player.vimeo.com/api/player.js';
    sdk.onload = initVimeo;
    document.head.appendChild(sdk);

    /* ════════════════════════════════════════
       YOUTUBE
    ════════════════════════════════════════ */
    <?php elseif ($is_youtube): ?>
    let ytPlayer = null;
    let ytReady  = false;
    let ytInterval = null;

    window.onYouTubeIframeAPIReady = function () {
        ytPlayer = new YT.Player('f5-yt-player', {
            videoId: '<?php echo esc_js($yt_id); ?>',
            playerVars: {
                autoplay: 1, controls: 0, rel: 0, modestbranding: 1,
                iv_load_policy: 3, disablekb: 1, fs: 0,
            },
            events: {
                onReady: e => {
                    ytReady = true;
                    duration = e.target.getDuration();
                    durEl.textContent = fmt(duration);
                    e.target.setVolume(volume * 100);
                    spinner.style.display = 'none';
                    // Poll for time
                    ytInterval = setInterval(() => {
                        if (!ytPlayer) return;
                        const cur = ytPlayer.getCurrentTime?.() || 0;
                        const dur = ytPlayer.getDuration?.() || 0;
                        setProgress(cur, dur);
                    }, 500);
                },
                onStateChange: e => {
                    if (e.data === YT.PlayerState.PLAYING)  setPlayState(true);
                    if (e.data === YT.PlayerState.PAUSED)   setPlayState(false);
                    if (e.data === YT.PlayerState.ENDED)    setPlayState(false);
                    if (e.data === YT.PlayerState.BUFFERING) spinner.style.display = 'flex';
                    else spinner.style.display = 'none';
                },
            }
        });
    };

    function togglePlay() {
        if (!ytPlayer || !ytReady) return;
        const s = ytPlayer.getPlayerState();
        s === 1 ? ytPlayer.pauseVideo() : ytPlayer.playVideo();
    }

    clickOvl.addEventListener('click', togglePlay);
    centerBtn.addEventListener('click', togglePlay);
    playBtn.addEventListener('click', togglePlay);

    scrubber.addEventListener('input', e => {
        if (!ytPlayer || !duration) return;
        const t = (parseFloat(e.target.value) / 100) * duration;
        ytPlayer.seekTo(t, true);
        progFill.style.width = e.target.value + '%';
        progThumb.style.left = e.target.value + '%';
    });

    muteBtn.addEventListener('click', () => {
        isMuted = !isMuted;
        isMuted ? ytPlayer?.mute() : ytPlayer?.unMute();
        applyVolume(volume, isMuted);
    });

    volSlider.addEventListener('input', e => {
        const v = parseFloat(e.target.value);
        ytPlayer?.setVolume(v * 100);
        applyVolume(v, v === 0);
    });

    fsBtn.addEventListener('click', toggleFs);

    const ytScript = document.createElement('script');
    ytScript.src = 'https://www.youtube.com/iframe_api';
    document.head.appendChild(ytScript);

    // Pointer events on wrapper only when needed
    $('f5-yt-wrapper').style.pointerEvents = 'none';

    /* ════════════════════════════════════════
       MP4 NATIVO
    ════════════════════════════════════════ */
    <?php else: ?>
    const video = $('f5-html5-video');
    if (video) {
        video.volume = volume;

        function togglePlay() {
            video.paused ? video.play().catch(()=>{}) : video.pause();
        }

        clickOvl.addEventListener('click', togglePlay);
        centerBtn.addEventListener('click', togglePlay);
        playBtn.addEventListener('click', togglePlay);

        video.addEventListener('play',    () => setPlayState(true));
        video.addEventListener('pause',   () => setPlayState(false));
        video.addEventListener('ended',   () => setPlayState(false));
        video.addEventListener('waiting', () => { spinner.style.display = 'flex'; });
        video.addEventListener('playing', () => { spinner.style.display = 'none'; });

        video.addEventListener('loadedmetadata', () => {
            duration = video.duration;
            durEl.textContent = fmt(duration);
        });

        video.addEventListener('timeupdate', () => {
            setProgress(video.currentTime, video.duration);
            // Buffer
            if (video.buffered.length > 0) {
                const b = (video.buffered.end(video.buffered.length - 1) / video.duration) * 100;
                bufBar.style.width = b + '%';
            }
            // Watch history
            const cur = Math.floor(video.currentTime);
            if (cur > 0 && cur % 15 === 0) {
                fetch('/wp-json/f5tv/v1/watch-history', {
                    method: 'POST',
                    headers: {'Content-Type':'application/json'},
                    body: JSON.stringify({
                        content_id: <?php echo json_encode($content_id); ?>,
                        watched_seconds: cur,
                        total_seconds: Math.floor(video.duration) || 1,
                        progress: Math.min(100, (cur / (video.duration || 1)) * 100)
                    })
                }).catch(()=>{});
            }
        });

        scrubber.addEventListener('input', e => {
            const pct = parseFloat(e.target.value);
            if (duration) video.currentTime = (pct / 100) * duration;
            progFill.style.width = pct + '%';
            progThumb.style.left = pct + '%';
        });

        muteBtn.addEventListener('click', () => {
            isMuted = !isMuted;
            video.muted = isMuted;
            applyVolume(video.volume, isMuted);
        });

        volSlider.addEventListener('input', e => {
            const v = parseFloat(e.target.value);
            video.volume = v;
            video.muted = v === 0;
            applyVolume(v, v === 0);
        });

        fsBtn.addEventListener('click', toggleFs);
        video.play().catch(() => {});
    }
    <?php endif; ?>

    /* ── Keyboard shortcuts (universal) ── */
    document.addEventListener('keydown', e => {
        if (['INPUT','TEXTAREA'].includes(e.target.tagName)) return;
        showUI(true);

        <?php if ($is_vimeo): ?>
        switch (e.code) {
            case 'Space': case 'KeyK': e.preventDefault(); togglePlay(); break;
            case 'ArrowLeft':  e.preventDefault(); player?.getCurrentTime().then(t => player.setCurrentTime(Math.max(0, t - 10))); break;
            case 'ArrowRight': e.preventDefault(); player?.getCurrentTime().then(t => player.setCurrentTime(t + 10)); break;
            case 'ArrowUp':    e.preventDefault(); player?.getVolume().then(v => { const nv = Math.min(1, v + 0.1); player.setVolume(nv); volSlider.value = nv; applyVolume(nv, false); }); break;
            case 'ArrowDown':  e.preventDefault(); player?.getVolume().then(v => { const nv = Math.max(0, v - 0.1); player.setVolume(nv); volSlider.value = nv; applyVolume(nv, nv===0); }); break;
            case 'KeyM': muteBtn.click(); break;
            case 'KeyF': toggleFs(); break;
        }
        <?php elseif ($is_youtube): ?>
        switch (e.code) {
            case 'Space': case 'KeyK': e.preventDefault(); togglePlay(); break;
            case 'ArrowLeft':  e.preventDefault(); ytPlayer?.seekTo((ytPlayer.getCurrentTime() || 0) - 10, true); break;
            case 'ArrowRight': e.preventDefault(); ytPlayer?.seekTo((ytPlayer.getCurrentTime() || 0) + 10, true); break;
            case 'KeyM': muteBtn.click(); break;
            case 'KeyF': toggleFs(); break;
        }
        <?php else: ?>
        const vid = $('f5-html5-video');
        if (!vid) return;
        switch (e.code) {
            case 'Space': case 'KeyK': e.preventDefault(); vid.paused ? vid.play() : vid.pause(); break;
            case 'ArrowLeft':  e.preventDefault(); vid.currentTime = Math.max(0, vid.currentTime - 10); break;
            case 'ArrowRight': e.preventDefault(); vid.currentTime = Math.min(vid.duration || 0, vid.currentTime + 10); break;
            case 'ArrowUp':    e.preventDefault(); vid.volume = Math.min(1, vid.volume + 0.1); volSlider.value = vid.volume; applyVolume(vid.volume, false); break;
            case 'ArrowDown':  e.preventDefault(); vid.volume = Math.max(0, vid.volume - 0.1); volSlider.value = vid.volume; applyVolume(vid.volume, vid.volume===0); break;
            case 'KeyM': muteBtn.click(); break;
            case 'KeyF': toggleFs(); break;
        }
        <?php endif; ?>
    });

    /* ── Initial UI state ── */
    showUI(true);
    spinner.style.display = 'flex';
    spinner.style.alignItems = 'center';
    spinner.style.justifyContent = 'center';
})();
</script>

<?php get_footer(); ?>
