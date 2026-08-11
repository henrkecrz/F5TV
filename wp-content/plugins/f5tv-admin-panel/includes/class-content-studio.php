<?php
/**
 * F5TV Admin Panel - Content Studio Manager
 * Central de Produções & Editor Completo de Séries por Temporada e Episódios no Design System F5 TV
 */

if (!defined('ABSPATH')) {
    exit;
}

class F5TV_Admin_Content_Studio
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'register_submenu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_media_assets']);
        add_action('admin_post_f5tv_save_studio_content', [$this, 'save_studio_content']);
        add_action('admin_post_f5tv_save_episode', [$this, 'save_episode']);
        add_action('admin_post_f5tv_add_season', [$this, 'add_season']);
        add_action('admin_post_f5tv_delete_content', [$this, 'delete_content']);
        add_action('admin_post_f5tv_create_content', [$this, 'create_content']);
    }

    public function enqueue_media_assets(): void
    {
        if (isset($_GET['page']) && $_GET['page'] === 'f5tv-content-studio') {
            wp_enqueue_media();
            $this->inject_vimeotheque_picker_assets();
        }
    }

    /**
     * Injeta CSS e JS do modal seletor Vimeotheque
     */
    private function inject_vimeotheque_picker_assets(): void
    {
        $rest_url = esc_js(rest_url('vimeotheque/v1'));
        $nonce    = wp_create_nonce('wp_rest');
        $vimeotheque_active = class_exists('Vimeotheque\Plugin') ? 'true' : 'false';

        add_action('admin_footer', function () use ($rest_url, $nonce, $vimeotheque_active) { ?>
<!-- F5TV Vimeotheque Picker Modal -->
<style>
#f5-vimeo-modal-overlay {
    display: none; position: fixed; inset: 0; z-index: 999999;
    background: rgba(3,4,10,0.92); backdrop-filter: blur(8px);
    align-items: center; justify-content: center;
}
#f5-vimeo-modal-overlay.active { display: flex; }
#f5-vimeo-modal {
    background: #0c101d; border: 1px solid #1f293d; border-radius: 1.25rem;
    width: 90vw; max-width: 860px; max-height: 88vh; display: flex;
    flex-direction: column; overflow: hidden;
    box-shadow: 0 30px 80px rgba(0,0,0,0.8), 0 0 0 1px rgba(229,9,20,0.2);
    animation: f5ModalIn 0.25s cubic-bezier(0.4,0,0.2,1);
}
@keyframes f5ModalIn {
    from { opacity:0; transform: scale(0.94) translateY(16px); }
    to   { opacity:1; transform: scale(1) translateY(0); }
}
#f5-vimeo-modal-header {
    display: flex; align-items: center; gap: 1rem; padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #1f293d; flex-shrink: 0;
}
#f5-vimeo-modal-header h2 {
    font-size: 1.05rem; font-weight: 900; color: #fff; margin: 0; flex: 1;
    display: flex; align-items: center; gap: 0.5rem;
}
#f5-vimeo-modal-header h2 span.badge {
    font-size: 0.65rem; font-family: monospace; background: #e50914;
    color: #fff; padding: 2px 8px; border-radius: 4px; font-weight: 800;
}
#f5-vimeo-modal-close {
    background: #1f293d; border: none; color: #9ca3af; cursor: pointer;
    width: 32px; height: 32px; border-radius: 8px; font-size: 1rem;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.15s;
}
#f5-vimeo-modal-close:hover { background: #e50914; color: #fff; }
#f5-vimeo-modal-tabs {
    display: flex; gap: 0; border-bottom: 1px solid #1f293d; flex-shrink: 0;
}
.f5vm-tab {
    padding: 0.75rem 1.25rem; font-size: 0.8rem; font-weight: 700;
    color: #6b7280; cursor: pointer; border-bottom: 2px solid transparent;
    transition: all 0.15s; background: none; border-top: none;
    border-left: none; border-right: none; user-select: none;
}
.f5vm-tab.active { color: #e50914; border-bottom-color: #e50914; }
.f5vm-tab:hover:not(.active) { color: #fff; }
#f5-vimeo-modal-search-bar {
    display: flex; gap: 0.75rem; padding: 1rem 1.5rem;
    border-bottom: 1px solid #1f293d; flex-shrink: 0;
}
#f5-vimeo-modal-search-bar input {
    flex: 1; background: #060913; border: 1px solid #1f293d;
    color: #f4f4f5; border-radius: 0.5rem; padding: 0.6rem 0.9rem;
    font-size: 0.85rem; outline: none; transition: border-color 0.15s;
}
#f5-vimeo-modal-search-bar input:focus { border-color: #e50914; }
#f5-vimeo-modal-search-bar button {
    background: #e50914; border: none; color: #fff; font-weight: 800;
    font-size: 0.8rem; padding: 0 1.25rem; border-radius: 0.5rem;
    cursor: pointer; transition: background 0.15s; white-space: nowrap;
}
#f5-vimeo-modal-search-bar button:hover { background: #c50812; }
#f5-vimeo-modal-body {
    flex: 1; overflow-y: auto; padding: 1rem 1.5rem;
}
#f5-vimeo-modal-body::-webkit-scrollbar { width: 5px; }
#f5-vimeo-modal-body::-webkit-scrollbar-track { background: transparent; }
#f5-vimeo-modal-body::-webkit-scrollbar-thumb { background: #1f293d; border-radius: 4px; }
.f5vm-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(155px,1fr));
    gap: 0.85rem;
}
.f5vm-card {
    background: #060913; border: 1px solid #1f293d; border-radius: 0.65rem;
    overflow: hidden; cursor: pointer; transition: all 0.2s;
    display: flex; flex-direction: column;
}
.f5vm-card:hover { border-color: #e50914; transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(229,9,20,0.25); }
.f5vm-card.selected { border-color: #e50914; box-shadow: 0 0 0 2px #e50914; }
.f5vm-thumb {
    width: 100%; aspect-ratio: 16/9; object-fit: cover;
    background: #0e1424;
}
.f5vm-thumb-placeholder {
    width: 100%; aspect-ratio: 16/9; background: linear-gradient(135deg, #0e1424, #1f293d);
    display: flex; align-items: center; justify-content: center; font-size: 2rem;
}
.f5vm-card-info { padding: 0.6rem 0.7rem; }
.f5vm-card-title {
    font-size: 0.72rem; font-weight: 700; color: #f4f4f5;
    line-height: 1.3; margin-bottom: 0.3rem;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.f5vm-card-meta { font-size: 0.65rem; color: #6b7280; font-family: monospace; }
.f5vm-card-url {
    font-size: 0.6rem; color: #38bdf8; font-family: monospace; margin-top: 0.2rem;
    word-break: break-all; display: -webkit-box; -webkit-line-clamp: 1;
    -webkit-box-orient: vertical; overflow: hidden;
}
#f5vm-status { padding: 2.5rem; text-align: center; color: #6b7280; font-size: 0.85rem; }
#f5vm-loading { display: none; padding: 2rem; text-align: center; }
.f5vm-spinner {
    width: 32px; height: 32px; border: 3px solid #1f293d;
    border-top-color: #e50914; border-radius: 50%;
    animation: f5spin 0.7s linear infinite; margin: 0 auto 0.75rem;
}
@keyframes f5spin { to { transform: rotate(360deg); } }
#f5-vimeo-modal-footer {
    display: flex; align-items: center; gap: 0.75rem; padding: 1rem 1.5rem;
    border-top: 1px solid #1f293d; flex-shrink: 0;
}
#f5vm-selected-info { flex: 1; font-size: 0.78rem; color: #9ca3af; font-family: monospace; }
#f5vm-select-btn {
    background: #e50914; border: none; color: #fff; font-weight: 800;
    font-size: 0.8rem; padding: 0.6rem 1.5rem; border-radius: 0.5rem;
    cursor: pointer; transition: background 0.15s; opacity: 0.5; pointer-events: none;
}
#f5vm-select-btn.enabled { opacity: 1; pointer-events: all; }
#f5vm-select-btn:hover { background: #c50812; }
#f5vm-cancel-btn {
    background: #1f293d; border: none; color: #9ca3af; font-weight: 700;
    font-size: 0.8rem; padding: 0.6rem 1.25rem; border-radius: 0.5rem;
    cursor: pointer; transition: all 0.15s;
}
#f5vm-cancel-btn:hover { background: #374151; color: #fff; }
.f5vm-no-plugin-notice {
    padding: 1.5rem; background: rgba(229,9,20,0.08);
    border: 1px solid rgba(229,9,20,0.25); border-radius: 0.65rem;
    font-size: 0.82rem; color: #f87171; text-align: center; margin-bottom: 1rem;
}
</style>

<div id="f5-vimeo-modal-overlay">
    <div id="f5-vimeo-modal">
        <div id="f5-vimeo-modal-header">
            <h2>🎬 Selecionar Vídeo <span class="badge">VIMEOTHEQUE</span></h2>
            <button id="f5-vimeo-modal-close" title="Fechar">✕</button>
        </div>

        <div id="f5-vimeo-modal-tabs">
            <button class="f5vm-tab active" data-tab="imported">📦 Vídeos Importados</button>
            <button class="f5vm-tab" data-tab="search">🔍 Buscar no Vimeo</button>
            <button class="f5vm-tab" data-tab="url">🔗 URL Manual</button>
        </div>

        <div id="f5-vimeo-modal-search-bar">
            <input type="text" id="f5vm-search-input" placeholder="Buscar vídeos..." />
            <button id="f5vm-search-btn">🔍 Buscar</button>
        </div>

        <div id="f5-vimeo-modal-body">
            <div id="f5vm-loading">
                <div class="f5vm-spinner"></div>
                <div style="color:#9ca3af;font-size:0.8rem;">Carregando vídeos...</div>
            </div>
            <div id="f5vm-status">Carregando vídeos importados...</div>
            <div id="f5vm-grid" class="f5vm-grid" style="display:none;"></div>

            <!-- Tab URL Manual -->
            <div id="f5vm-tab-url" style="display:none; padding-top: 0.5rem;">
                <label style="font-size:0.82rem;color:#9ca3af;font-weight:700;display:block;margin-bottom:0.5rem;">Cole a URL do vídeo (Vimeo, MP4, HLS, YouTube):</label>
                <input type="text" id="f5vm-manual-url"
                    style="width:100%;background:#060913;border:1px solid #1f293d;color:#f4f4f5;border-radius:0.5rem;padding:0.7rem 0.9rem;font-size:0.85rem;font-family:monospace;outline:none;box-sizing:border-box;"
                    placeholder="https://vimeo.com/123456789 ou https://.../stream.m3u8" />
                <div style="margin-top:0.75rem;font-size:0.75rem;color:#6b7280;">
                    Formatos aceitos: <code style="color:#38bdf8;">vimeo.com/ID</code> · <code style="color:#38bdf8;">.mp4</code> · <code style="color:#38bdf8;">.m3u8 (HLS)</code> · <code style="color:#38bdf8;">youtube.com/watch</code>
                </div>
            </div>
        </div>

        <div id="f5-vimeo-modal-footer">
            <div id="f5vm-selected-info">Nenhum vídeo selecionado</div>
            <button id="f5vm-cancel-btn">Cancelar</button>
            <button id="f5vm-select-btn">✅ Usar Este Vídeo</button>
        </div>
    </div>
</div>

<script>
(function(){
    'use strict';
    var REST_BASE   = '<?php echo $rest_url; ?>';
    var NONCE       = '<?php echo $nonce; ?>';
    var VIMEOTHEQUE = <?php echo $vimeotheque_active; ?>;

    var modal        = document.getElementById('f5-vimeo-modal-overlay');
    var closeBtn     = document.getElementById('f5-vimeo-modal-close');
    var cancelBtn    = document.getElementById('f5vm-cancel-btn');
    var selectBtn    = document.getElementById('f5vm-select-btn');
    var searchBar    = document.getElementById('f5-vimeo-modal-search-bar');
    var searchInput  = document.getElementById('f5vm-search-input');
    var searchBtn    = document.getElementById('f5vm-search-btn');
    var grid         = document.getElementById('f5vm-grid');
    var loading      = document.getElementById('f5vm-loading');
    var status       = document.getElementById('f5vm-status');
    var selectedInfo = document.getElementById('f5vm-selected-info');
    var tabUrl       = document.getElementById('f5vm-tab-url');
    var manualUrl    = document.getElementById('f5vm-manual-url');

    var currentTab    = 'imported';
    var selectedUrl   = '';
    var targetInput   = null;   // the <input> that opened the modal

    /* ─── Open ─── */
    document.addEventListener('click', function(e){
        var btn = e.target.closest('[data-f5vm-open]');
        if (!btn) return;
        targetInput = document.getElementById(btn.dataset.f5vmOpen);
        selectedUrl = '';
        selectedInfo.textContent = 'Nenhum vídeo selecionado';
        selectBtn.classList.remove('enabled');
        switchTab('imported');
        modal.classList.add('active');
        loadImported();
    });

    /* ─── Close ─── */
    function closeModal(){ modal.classList.remove('active'); }
    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', function(e){ if(e.target===modal) closeModal(); });

    /* ─── Tabs ─── */
    document.querySelectorAll('.f5vm-tab').forEach(function(t){
        t.addEventListener('click', function(){ switchTab(t.dataset.tab); });
    });

    function switchTab(tab){
        currentTab = tab;
        document.querySelectorAll('.f5vm-tab').forEach(function(t){
            t.classList.toggle('active', t.dataset.tab === tab);
        });

        grid.style.display       = 'none';
        status.style.display     = 'none';
        tabUrl.style.display     = 'none';
        searchBar.style.display  = '';

        if(tab === 'imported'){
            searchInput.placeholder = 'Filtrar por título...';
            loadImported();
        } else if(tab === 'search'){
            searchInput.placeholder = 'Buscar vídeos no Vimeo...';
            grid.innerHTML = '';
            showStatus(VIMEOTHEQUE ? 'Digite um termo e clique em Buscar.' : 'Vimeotheque não está configurado com API token.');
        } else if(tab === 'url'){
            searchBar.style.display = 'none';
            tabUrl.style.display    = '';
            // watch manual input
            manualUrl.addEventListener('input', function(){
                if(manualUrl.value.trim()){
                    selectedUrl = manualUrl.value.trim();
                    selectedInfo.textContent = '🔗 URL: ' + selectedUrl.substring(0,60);
                    selectBtn.classList.add('enabled');
                } else {
                    selectedUrl = '';
                    selectedInfo.textContent = 'Nenhum vídeo selecionado';
                    selectBtn.classList.remove('enabled');
                }
            });
        }
    }

    /* ─── Search button ─── */
    searchBtn.addEventListener('click', function(){
        if(currentTab === 'imported') loadImported(searchInput.value);
        else if(currentTab === 'search') searchVimeo(searchInput.value);
    });
    searchInput.addEventListener('keydown', function(e){ if(e.key==='Enter') searchBtn.click(); });

    /* ─── Load imported Vimeotheque posts ─── */
    function loadImported(filter){
        showLoading();
        var url = REST_BASE + '/get_posts?post_type=cvm_video&per_page=48' +
                  (filter ? '&search=' + encodeURIComponent(filter) : '');
        apiFetch(url, function(data, err){
            if(err || !Array.isArray(data) || data.length === 0){
                showStatus('Nenhum vídeo importado encontrado.' +
                    (VIMEOTHEQUE ? ' Use a aba "Buscar no Vimeo" para importar.' : ' O plugin Vimeotheque não está configurado.'));
                return;
            }
            renderGrid(data.map(function(p){
                var thumb = (p.vimeo_video && p.vimeo_video.thumbnail) ? p.vimeo_video.thumbnail.link : '';
                var dur   = (p.vimeo_video && p.vimeo_video._duration) ? p.vimeo_video._duration : '';
                var title = (p.title && p.title.rendered) ? p.title.rendered : (p.title || 'Sem título');
                var link  = p.link || '';
                return { thumb: thumb, title: title, meta: dur, url: link, vimeoId: '' };
            }));
        });
    }

    /* ─── Search Vimeo API ─── */
    function searchVimeo(q){
        if(!q){ showStatus('Digite um termo para buscar.'); return; }
        showLoading();
        var url = REST_BASE + '/api-query/search/?query=' + encodeURIComponent(q);
        apiFetch(url, function(data, err){
            if(err){ showStatus('Erro ao buscar: ' + (err.message || err)); return; }
            var videos = data.data || data;
            if(!videos || videos.length === 0){ showStatus('Nenhum resultado encontrado.'); return; }
            renderGrid(videos.map(function(v){
                var thumb = v.pictures && v.pictures.sizes && v.pictures.sizes.length ?
                            v.pictures.sizes[Math.min(2, v.pictures.sizes.length-1)].link : '';
                var dur   = v.duration ? formatDuration(v.duration) : '';
                return {
                    thumb:   thumb,
                    title:   v.name || v.description || 'Sem título',
                    meta:    dur,
                    url:     'https://vimeo.com/' + v.uri.replace('/videos/',''),
                    vimeoId: v.uri ? v.uri.replace('/videos/','') : ''
                };
            }));
        });
    }

    /* ─── Render grid of video cards ─── */
    function renderGrid(items){
        hideLoading();
        status.style.display = 'none';
        grid.style.display   = '';
        grid.innerHTML = '';
        items.forEach(function(item){
            var card = document.createElement('div');
            card.className = 'f5vm-card';
            card.innerHTML =
                (item.thumb
                    ? '<img class="f5vm-thumb" src="' + escAttr(item.thumb) + '" loading="lazy">'
                    : '<div class="f5vm-thumb-placeholder">🎬</div>') +
                '<div class="f5vm-card-info">' +
                    '<div class="f5vm-card-title">' + escHtml(item.title) + '</div>' +
                    (item.meta ? '<div class="f5vm-card-meta">⏱ ' + escHtml(item.meta) + '</div>' : '') +
                    (item.url ? '<div class="f5vm-card-url">' + escHtml(item.url) + '</div>' : '') +
                '</div>';

            card.addEventListener('click', function(){
                document.querySelectorAll('.f5vm-card').forEach(function(c){ c.classList.remove('selected'); });
                card.classList.add('selected');
                selectedUrl = item.url;
                selectedInfo.textContent = '🎬 ' + item.title.substring(0,55) + (item.meta ? ' · ' + item.meta : '');
                selectBtn.classList.add('enabled');
            });

            grid.appendChild(card);
        });
    }

    /* ─── Use selected video ─── */
    selectBtn.addEventListener('click', function(){
        if(!selectedUrl || !targetInput) return;
        targetInput.value = selectedUrl;
        // trigger change for any preview listeners
        targetInput.dispatchEvent(new Event('input', {bubbles: true}));
        closeModal();
    });

    /* ─── Helpers ─── */
    function apiFetch(url, cb){
        fetch(url, { headers: { 'X-WP-Nonce': NONCE } })
            .then(function(r){ return r.json(); })
            .then(function(d){ cb(d, null); })
            .catch(function(e){ cb(null, e); });
    }
    function showLoading(){ loading.style.display=''; status.style.display='none'; grid.style.display='none'; }
    function hideLoading(){ loading.style.display='none'; }
    function showStatus(msg){ hideLoading(); status.textContent=msg; status.style.display=''; grid.style.display='none'; }
    function formatDuration(s){ var m=Math.floor(s/60); var sec=s%60; return m+'m'+(sec?sec+'s':''); }
    function escAttr(s){ return String(s).replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }
    function escHtml(s){ return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
})();
</script>
<?php
        });
    }

    public function register_submenu(): void
    {
        add_submenu_page(
            'edit.php?post_type=f5tv_conteudo',
            __('Central F5 Streaming', 'f5tv-admin-panel'),
            __('🍿 Central F5 Streaming', 'f5tv-admin-panel'),
            'manage_options',
            'f5tv-content-studio',
            [$this, 'render_page']
        );
    }

    public function save_studio_content(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die('Sem permissão.');
        }

        check_admin_referer('f5tv_save_studio_content');

        $post_id = intval($_POST['post_id'] ?? 0);
        if (!$post_id) {
            wp_redirect(admin_url('admin.php?page=f5tv-content-studio'));
            exit;
        }

        // Salvar título, resumo e conteúdo
        if (isset($_POST['post_title'])) {
            wp_update_post([
                'ID'           => $post_id,
                'post_status'  => 'publish',
                'post_title'   => sanitize_text_field($_POST['post_title']),
                'post_excerpt' => sanitize_text_field($_POST['post_excerpt'] ?? ''),
                'post_content' => wp_kses_post($_POST['post_content'] ?? ''),
            ]);
        }

        // Campos Meta
        $fields = [
            'genre',
            'content_type',
            'age_rating',
            'year',
            'duration',
            'cover_url',
            'banner_url',
            'trailer_url',
            'video_url',
            'cast',
            'directors',
            'is_featured',
            'is_free',
            'is_exclusive',
        ];

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                $val = $_POST[$field];
                if (in_array($field, ['cast', 'directors', 'post_content'])) {
                    $val = sanitize_textarea_field($val);
                } elseif (in_array($field, ['is_featured', 'is_free', 'is_exclusive'])) {
                    $val = intval($val);
                } else {
                    $val = sanitize_text_field($val);
                }
                update_post_meta($post_id, $field, $val);
            }
        }

        if (get_post_type($post_id) === 'f5tv_conteudo') {
            update_post_meta($post_id, 'series_id', absint($_POST['series_id'] ?? 0));
            $related = array_map('absint', (array) ($_POST['related_content_ids'] ?? []));
            $related = array_values(array_filter(array_unique($related), static function ($id) use ($post_id) {
                return $id && $id !== $post_id && get_post_type($id) === 'f5tv_conteudo';
            }));
            update_post_meta($post_id, 'related_content_ids', $related);
        }

        if (get_post_type($post_id) === 'f5tv_serie') {
            $related = array_map('absint', (array) ($_POST['related_series_ids'] ?? []));
            $related = array_values(array_filter(array_unique($related), static function ($id) use ($post_id) {
                return $id && $id !== $post_id && get_post_type($id) === 'f5tv_serie';
            }));
            update_post_meta($post_id, 'related_series_ids', $related);
        }

        wp_redirect(admin_url('admin.php?page=f5tv-content-studio&action=edit&id=' . $post_id . '&saved=1'));
        exit;
    }

    public function delete_content(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die('Sem permissao.');
        }

        check_admin_referer('f5tv_delete_content');
        $post_id = absint($_POST['post_id'] ?? 0);
        $post = $post_id ? get_post($post_id) : null;

        if ($post && in_array($post->post_type, ['f5tv_conteudo', 'f5tv_serie'], true)) {
            wp_delete_post($post_id, true);
        }

        wp_safe_redirect(admin_url('admin.php?page=f5tv-content-studio&deleted=1'));
        exit;
    }

    public function create_content(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die('Sem permissao.');
        }

        check_admin_referer('f5tv_create_content');
        $post_type = sanitize_key($_POST['post_type'] ?? 'f5tv_conteudo');
        if (!in_array($post_type, ['f5tv_conteudo', 'f5tv_serie'], true)) {
            $post_type = 'f5tv_conteudo';
        }

        $title = sanitize_text_field($_POST['post_title'] ?? '');
        if ($title === '') {
            wp_safe_redirect(admin_url('admin.php?page=f5tv-content-studio&action=new&type=' . $post_type . '&error=title'));
            exit;
        }

        $post_id = wp_insert_post([
            'post_title' => $title,
            'post_excerpt' => sanitize_textarea_field($_POST['post_excerpt'] ?? ''),
            'post_content' => wp_kses_post($_POST['post_content'] ?? ''),
            'post_type' => $post_type,
            'post_status' => 'draft',
        ], true);

        if (is_wp_error($post_id)) {
            wp_die('Nao foi possivel criar o conteudo: ' . esc_html($post_id->get_error_message()));
        }

        update_post_meta($post_id, 'genre', sanitize_text_field($_POST['genre'] ?? ''));
        update_post_meta($post_id, 'age_rating', sanitize_text_field($_POST['age_rating'] ?? 'L'));
        update_post_meta($post_id, 'year', absint($_POST['year'] ?? date('Y')));
        update_post_meta($post_id, 'content_type', $post_type === 'f5tv_serie' ? 'series' : 'movie');

        wp_safe_redirect(admin_url('admin.php?page=f5tv-content-studio&action=edit&id=' . $post_id . '&new=1'));
        exit;
    }

    public function add_season(): void
    {
        if (!current_user_can('manage_options')) wp_die('Sem permissão.');
        check_admin_referer('f5tv_add_season');

        $series_id = intval($_POST['series_id'] ?? 0);
        $number = intval($_POST['season_number'] ?? 1);

        if ($series_id) {
            $temp_id = wp_insert_post([
                'post_title'  => "Temporada $number",
                'post_type'   => 'f5tv_temporada',
                'post_status' => 'publish',
            ]);
            if ($temp_id) {
                update_post_meta($temp_id, 'series_id', $series_id);
                update_post_meta($temp_id, 'number', $number);
            }
        }

        wp_redirect(admin_url('admin.php?page=f5tv-content-studio&action=edit&id=' . $series_id . '&saved=1'));
        exit;
    }

    public function save_episode(): void
    {
        if (!current_user_can('manage_options')) wp_die('Sem permissão.');
        check_admin_referer('f5tv_save_episode');

        $series_id = intval($_POST['series_id'] ?? 0);
        $season_id = intval($_POST['season_id'] ?? 0);
        $episode_id = intval($_POST['episode_id'] ?? 0);
        $title = sanitize_text_field($_POST['ep_title'] ?? 'Novo Episódio');
        $video_url = sanitize_text_field($_POST['ep_video_url'] ?? '');
        $duration = sanitize_text_field($_POST['ep_duration'] ?? '45m');
        $number = intval($_POST['ep_number'] ?? 1);

        if (!$episode_id) {
            $episode_id = wp_insert_post([
                'post_title'  => $title,
                'post_type'   => 'f5tv_episodio',
                'post_status' => 'publish',
            ]);
        } else {
            wp_update_post([
                'ID'         => $episode_id,
                'post_title' => $title,
            ]);
        }

        if ($episode_id) {
            update_post_meta($episode_id, 'season_id', $season_id);
            update_post_meta($episode_id, 'video_url', $video_url);
            update_post_meta($episode_id, 'duration', $duration);
            update_post_meta($episode_id, 'number', $number);
        }

        wp_redirect(admin_url('admin.php?page=f5tv-content-studio&action=edit&id=' . $series_id . '&saved=1'));
        exit;
    }

    public function render_page(): void
    {
        $action = sanitize_text_field($_GET['action'] ?? '');
        $post_id = intval($_GET['id'] ?? 0);
        $type = sanitize_text_field($_GET['type'] ?? 'f5tv_conteudo');

        // A criação acontece em uma tela própria; não criamos posts em um GET.
        if ($action === 'new') {
            $post_type = in_array($type, ['f5tv_conteudo', 'f5tv_serie']) ? $type : 'f5tv_conteudo';
            $this->render_new_content_page($post_type);
            return;
        }

        if ($action === 'edit' && $post_id) {
            $this->render_studio_editor($post_id);
            return;
        }

        $this->render_studio_grid();
    }

    private function render_new_content_page(string $post_type): void
    {
        $is_series = $post_type === 'f5tv_serie';
        $title = $is_series ? 'Nova Série' : 'Novo Programa';
        ?>
        <style>
            .f5-new-wrap{max-width:900px;margin:24px 0;color:#f4f4f5}.f5-new-card{background:#0c101d;border:1px solid #1f293d;border-radius:16px;padding:28px;box-shadow:0 12px 35px rgba(0,0,0,.35)}.f5-new-title{font-size:28px;font-weight:900;color:#fff;margin:8px 0}.f5-new-label{display:block;color:#d1d5db;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;margin:18px 0 6px}.f5-new-input{width:100%;box-sizing:border-box;background:#060913;border:1px solid #27344d;color:#fff;border-radius:8px;padding:11px}.f5-new-input:focus{outline:none;border-color:#e50914;box-shadow:0 0 0 3px rgba(229,9,20,.18)}.f5-new-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}@media(max-width:700px){.f5-new-grid{grid-template-columns:1fr}}
        </style>
        <div class="wrap f5-new-wrap">
            <a href="<?php echo esc_url(admin_url('admin.php?page=f5tv-content-studio')); ?>" style="color:#9ca3af;text-decoration:none;font-weight:700">&larr; Voltar para Central F5 Streaming</a>
            <div class="f5-new-card" style="margin-top:16px;background:linear-gradient(135deg,#0c101d,#151b2e)">
                <span style="font:800 10px monospace;letter-spacing:.14em;color:#e50914;text-transform:uppercase">F5 STREAMING STUDIO</span>
                <h1 class="f5-new-title"><?php echo esc_html($title); ?></h1>
                <p style="color:#9ca3af;margin:0">Crie o registro inicial e continue no editor completo para adicionar capas, vídeo, categorias e demais informações.</p>
            </div>
            <?php if (isset($_GET['error'])): ?><div class="notice notice-error"><p>Informe um título para continuar.</p></div><?php endif; ?>
            <div class="f5-new-card">
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <?php wp_nonce_field('f5tv_create_content'); ?>
                    <input type="hidden" name="action" value="f5tv_create_content"><input type="hidden" name="post_type" value="<?php echo esc_attr($post_type); ?>">
                    <label class="f5-new-label">Título</label><input class="f5-new-input" name="post_title" required autofocus placeholder="<?php echo $is_series ? 'Ex: Investigação F5' : 'Ex: Jornal F5'; ?>">
                    <div class="f5-new-grid"><div><label class="f5-new-label">Gênero</label><input class="f5-new-input" name="genre" placeholder="Ex: Jornalismo, Entrevistas"></div><div><label class="f5-new-label">Ano</label><input class="f5-new-input" type="number" name="year" value="<?php echo esc_attr(date('Y')); ?>"></div></div>
                    <label class="f5-new-label">Resumo curto</label><textarea class="f5-new-input" name="post_excerpt" rows="3" placeholder="Apresente este conteúdo para o público."></textarea>
                    <label class="f5-new-label">Descrição inicial</label><textarea class="f5-new-input" name="post_content" rows="5" placeholder="Descrição completa, sinopse ou contexto editorial."></textarea>
                    <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:22px"><a href="<?php echo esc_url(admin_url('admin.php?page=f5tv-content-studio')); ?>" class="button button-secondary">Cancelar</a><button type="submit" class="button button-primary" style="background:#e50914;border-color:#e50914">Criar e abrir editor</button></div>
                </form>
            </div>
        </div>
        <?php
    }

    /**
     * Renderiza o Editor Completo do F5 Streaming Studio (Com Temporadas e Episódios)
     */
    private function render_studio_editor(int $post_id): void
    {
        $post = get_post($post_id);
        if (!$post) {
            echo '<div class="wrap"><p>Conteúdo não encontrado.</p></div>';
            return;
        }

        $saved = isset($_GET['saved']) && $_GET['saved'] === '1';
        $is_new = isset($_GET['new']) && $_GET['new'] === '1';
        $cover_url    = f5tv_get_field('cover_url', $post_id) ?: get_the_post_thumbnail_url($post_id, 'medium') ?: '';
        $banner_url   = f5tv_get_field('banner_url', $post_id) ?: '';
        $video_url    = f5tv_get_field('video_url', $post_id) ?: '';
        $trailer_url  = f5tv_get_field('trailer_url', $post_id) ?: '';
        $genre        = f5tv_get_field('genre', $post_id) ?: 'Investigativo';
        $age_rating   = f5tv_get_field('age_rating', $post_id) ?: '16';
        $year         = f5tv_get_field('year', $post_id) ?: date('Y');
        $duration     = f5tv_get_field('duration', $post_id) ?: '1 Temp';
        $cast         = f5tv_get_field('cast', $post_id) ?: '';
        $directors    = f5tv_get_field('directors', $post_id) ?: '';
        $content_type = f5tv_get_field('content_type', $post_id) ?: ($post->post_type === 'f5tv_serie' ? 'series' : 'movie');
        $is_featured  = f5tv_get_field('is_featured', $post_id);
        $is_free      = f5tv_get_field('is_free', $post_id);
        $is_exclusive = f5tv_get_field('is_exclusive', $post_id);
        $all_series = get_posts([
            'post_type' => 'f5tv_serie',
            'post_status' => ['publish', 'draft'],
            'posts_per_page' => -1,
            'post__not_in' => [$post_id],
            'orderby' => 'title',
            'order' => 'ASC',
        ]);
        $all_contents = get_posts([
            'post_type' => 'f5tv_conteudo',
            'post_status' => ['publish', 'draft'],
            'posts_per_page' => -1,
            'post__not_in' => [$post_id],
            'orderby' => 'title',
            'order' => 'ASC',
        ]);
        $linked_series_id = absint(get_post_meta($post_id, 'series_id', true));
        $related_content_ids = array_map('absint', (array) get_post_meta($post_id, 'related_content_ids', true));
        $related_series_ids = array_map('absint', (array) get_post_meta($post_id, 'related_series_ids', true));

        // Buscar Temporadas da Série
        $seasons = get_posts([
            'post_type'      => 'f5tv_temporada',
            'posts_per_page' => -1,
            'meta_query'     => [[
                'key' => 'series_id',
                'value' => $post_id,
                'compare' => '=',
            ]],
            'orderby'        => 'meta_value_num',
            'meta_key'       => 'number',
            'order'          => 'ASC',
        ]);
        ?>
        <style>
            .f5-editor-wrap { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; color: #f4f4f5; max-width: 1250px; margin: 20px 0; }
            .f5-card { background: #0c101d; border: 1px solid #1f293d; border-radius: 1rem; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
            .f5-title { font-size: 1.5rem; font-weight: 900; color: #ffffff; letter-spacing: -0.02em; display: flex; align-items: center; gap: 0.75rem; }
            .f5-subtitle { color: #9ca3af; font-size: 0.85rem; margin-top: 0.25rem; }

            .f5-form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem; margin-top: 1rem; }
            .f5-field { display: flex; flex-direction: column; gap: 0.35rem; }
            .f5-label { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #d1d5db; }
            .f5-input { background: #060913; border: 1px solid #27344d; color: #ffffff; padding: 0.65rem 0.85rem; border-radius: 0.5rem; font-size: 0.9rem; width: 100%; box-sizing: border-box; }
            .f5-input:focus { border-color: #e50914; outline: none; box-shadow: 0 0 0 3px rgba(229, 9, 20, 0.2); }
            
            .f5-btn-primary { background: #e50914 !important; color: #ffffff !important; font-weight: 800 !important; font-size: 0.85rem !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; border: none !important; padding: 0.75rem 1.5rem !important; border-radius: 0.5rem !important; cursor: pointer !important; display: inline-flex !important; align-items: center !important; gap: 0.5rem !important; transition: all 0.2s !important; text-decoration: none !important; box-shadow: 0 4px 15px rgba(229, 9, 20, 0.4) !important; }
            .f5-btn-primary:hover { background: #b80710 !important; transform: translateY(-1px) !important; }
            .f5-btn-secondary { background: #1f293d !important; color: #ffffff !important; font-weight: 700 !important; font-size: 0.8rem !important; text-transform: uppercase !important; border: none !important; padding: 0.6rem 1.25rem !important; border-radius: 0.5rem !important; cursor: pointer !important; text-decoration: none !important; display: inline-flex !important; align-items: center !important; gap: 0.4rem !important; }
            .f5-btn-secondary:hover { background: #374151 !important; }

            .f5-season-box { background: #060913; border: 1px solid #1f293d; border-radius: 0.85rem; padding: 1.25rem; margin-top: 1rem; }
            .f5-episode-item { background: #0c101d; border: 1px solid #1a2336; border-radius: 0.65rem; padding: 1rem; margin-top: 0.75rem; display: flex; flex-direction: column; gap: 0.75rem; }
            .f5-badge { font-family: monospace; font-size: 0.65rem; padding: 0.2rem 0.5rem; border-radius: 0.25rem; font-weight: 800; text-transform: uppercase; background: rgba(229,9,20,0.15); color: #e50914; border: 1px solid rgba(229,9,20,0.3); }
        </style>

        <div class="f5-editor-wrap wrap">
            
            <!-- Hero Header do Editor de Produção -->
            <div class="f5-card" style="background: linear-gradient(135deg, #0c101d 0%, #151b2e 100%);">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <a href="<?php echo esc_url(admin_url('admin.php?page=f5tv-content-studio')); ?>" style="color: #9ca3af; font-size: 0.8rem; font-weight: 700; text-decoration: none;">&larr; Voltar para Central F5 Streaming</a>
                            <span class="f5-badge">EDITOR DE PRODUÇÃO F5 STREAMING</span>
                        </div>
                        <h1 class="f5-title" style="margin-top: 0.5rem;">🎬 <?php echo esc_html($post->post_title); ?></h1>
                        <p class="f5-subtitle">Edite os metadados, capas, trailer, vídeo do Vimeo/MP4 e estruture temporadas e episódios.</p>
                    </div>

                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <a href="<?php echo esc_url(home_url('/assista?id=' . $post_id)); ?>" target="_blank" class="f5-btn-secondary" style="color: #38bdf8 !important;">
                            ▶️ Testar Player Digital
                        </a>
                        <button type="submit" form="f5-main-editor-form" class="f5-btn-primary">
                            💾 Salvar Alterações
                        </button>
                        <form method="post" action="admin-post.php" onsubmit="return confirm('Excluir este conteudo permanentemente?');">
                            <?php wp_nonce_field('f5tv_delete_content'); ?>
                            <input type="hidden" name="action" value="f5tv_delete_content">
                            <input type="hidden" name="post_id" value="<?php echo esc_attr($post_id); ?>">
                            <button type="submit" class="f5-btn-secondary" style="background:#7f1d1d !important;color:#fecaca !important;">Deletar</button>
                        </form>
                    </div>
                </div>
            </div>

            <?php if ($saved || $is_new): ?>
                <div style="background: #064e3b; border: 1px solid #059669; color: #34d399; padding: 0.85rem 1.25rem; border-radius: 0.5rem; font-weight: 700; margin-bottom: 1.5rem;">
                    <?php echo $is_new ? '🎉 Nova produção criada no Estúdio! Preencha as informações abaixo e clique em Salvar.' : '✅ Alterações salvas com sucesso no acervo da F5 TV!'; ?>
                </div>
            <?php endif; ?>


            <!-- Formulário Principal -->
            <form id="f5-main-editor-form" method="post" action="admin-post.php">
                <?php wp_nonce_field('f5tv_save_studio_content'); ?>
                <input type="hidden" name="action" value="f5tv_save_studio_content">
                <input type="hidden" name="post_id" value="<?php echo esc_attr($post_id); ?>">

                <!-- 1. Informações Básicas da Produção -->
                <div class="f5-card">
                    <h2 class="f5-title" style="font-size: 1.15rem;">📌 Informações da Produção</h2>
                    <div class="f5-form-grid">
                        <div class="f5-field" style="grid-column: span 2;">
                            <label class="f5-label">Título da Série / Filme</label>
                            <input type="text" name="post_title" value="<?php echo esc_attr($post->post_title); ?>" class="f5-input" style="font-size: 1.1rem; font-weight: 800;">
                        </div>

                        <div class="f5-field">
                            <label class="f5-label">Gênero Principal</label>
                            <input type="text" name="genre" value="<?php echo esc_attr($genre); ?>" class="f5-input" placeholder="ex: Investigativo, Jornalismo, Ação">
                        </div>

                        <div class="f5-field">
                            <label class="f5-label">Classificação Indicativa</label>
                            <select name="age_rating" class="f5-input">
                                <option value="Livre" <?php selected($age_rating, 'Livre'); ?>>Livre</option>
                                <option value="10" <?php selected($age_rating, '10'); ?>>10 Anos</option>
                                <option value="12" <?php selected($age_rating, '12'); ?>>12 Anos</option>
                                <option value="14" <?php selected($age_rating, '14'); ?>>14 Anos</option>
                                <option value="16" <?php selected($age_rating, '16'); ?>>16 Anos</option>
                                <option value="18" <?php selected($age_rating, '18'); ?>>18 Anos</option>
                            </select>
                        </div>

                        <div class="f5-field">
                            <label class="f5-label">Ano de Lançamento</label>
                            <input type="number" name="year" value="<?php echo esc_attr($year); ?>" class="f5-input">
                        </div>

                        <div class="f5-field">
                            <label class="f5-label">Duração / Temporadas</label>
                            <input type="text" name="duration" value="<?php echo esc_attr($duration); ?>" class="f5-input" placeholder="ex: 1 Temp (8 eps) ou 1h 45m">
                        </div>

                        <div class="f5-field" style="grid-column: span 2;">
                            <label class="f5-label">Sinopse Curta (Resumo)</label>
                            <textarea name="post_excerpt" class="f5-input" rows="2"><?php echo esc_textarea($post->post_excerpt); ?></textarea>
                        </div>

                        <div class="f5-field" style="grid-column: span 2;">
                            <label class="f5-label">Descrição Completa</label>
                            <textarea name="post_content" class="f5-input" rows="4"><?php echo esc_textarea($post->post_content); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- 2. Relacionamentos editoriais -->
                <div class="f5-card">
                    <h2 class="f5-title" style="font-size: 1.15rem;">🔗 Organização do Catálogo</h2>
                    <p class="f5-subtitle">Defina a série deste programa e escolha recomendações específicas, como em uma experiência de streaming.</p>
                    <div class="f5-form-grid">
                        <?php if ($post->post_type === 'f5tv_conteudo'): ?>
                            <div class="f5-field">
                                <label class="f5-label">Série relacionada</label>
                                <select name="series_id" class="f5-input">
                                    <option value="0">Nenhuma série</option>
                                    <?php foreach ($all_series as $series): ?>
                                        <option value="<?php echo esc_attr($series->ID); ?>" <?php selected($linked_series_id, $series->ID); ?>><?php echo esc_html($series->post_title); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small style="color:#6b7280;">Use quando este programa fizer parte de uma série.</small>
                            </div>
                            <div class="f5-field">
                                <label class="f5-label">Programas semelhantes</label>
                                <select name="related_content_ids[]" class="f5-input" multiple size="5">
                                    <?php foreach ($all_contents as $related_item): ?>
                                        <option value="<?php echo esc_attr($related_item->ID); ?>" <?php selected(in_array($related_item->ID, $related_content_ids, true), true); ?>><?php echo esc_html($related_item->post_title); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small style="color:#6b7280;">Segure Ctrl/Cmd para selecionar mais de um.</small>
                            </div>
                        <?php else: ?>
                            <div class="f5-field" style="grid-column: span 2;">
                                <label class="f5-label">Séries semelhantes</label>
                                <select name="related_series_ids[]" class="f5-input" multiple size="6">
                                    <?php foreach ($all_series as $related_series): ?>
                                        <option value="<?php echo esc_attr($related_series->ID); ?>" <?php selected(in_array($related_series->ID, $related_series_ids, true), true); ?>><?php echo esc_html($related_series->post_title); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small style="color:#6b7280;">A página da série exibirá estas recomendações primeiro.</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- 2. Vídeo Principal & Trailer (Vimeo, YouTube, MP4) -->
                <div class="f5-card">
                    <h2 class="f5-title" style="font-size: 1.15rem;">🎥 Mídia & Links de Vídeo</h2>
                    <p class="f5-subtitle">Selecione um vídeo importado do Vimeotheque, busque diretamente no Vimeo ou cole a URL manualmente.</p>

                    <div class="f5-form-grid">
                        <div class="f5-field" style="grid-column: span 2;">
                            <label class="f5-label">Vídeo Principal (Vimeo / MP4 / HLS)</label>
                            <div style="display:flex; gap:0.5rem; align-items:center; flex-wrap:wrap;">
                                <input type="text" id="f5_video_url" name="video_url" value="<?php echo esc_attr($video_url); ?>" class="f5-input font-mono" style="flex:1;" placeholder="https://vimeo.com/123456789 ou https://.../stream.m3u8">
                                <button type="button" class="f5-btn-secondary" data-f5vm-open="f5_video_url" style="white-space:nowrap;font-size:0.75rem;">
                                    🎬 Selecionar Vídeo
                                </button>
                            </div>
                        </div>

                        <div class="f5-field" style="grid-column: span 2;">
                            <label class="f5-label">Trailer Promocional</label>
                            <div style="display:flex; gap:0.5rem; align-items:center; flex-wrap:wrap;">
                                <input type="text" id="f5_trailer_url" name="trailer_url" value="<?php echo esc_attr($trailer_url); ?>" class="f5-input font-mono" style="flex:1;" placeholder="https://vimeo.com/987654321 ou YouTube">
                                <button type="button" class="f5-btn-secondary" data-f5vm-open="f5_trailer_url" style="white-space:nowrap;font-size:0.75rem;">
                                    🎬 Selecionar Vídeo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Imagens de Exibição (Capa Vertical & Banner 16:9) -->
                <div class="f5-card">
                    <h2 class="f5-title" style="font-size: 1.15rem;">🖼️ Posters & Banners de Exibição</h2>
                    <p class="f5-subtitle">Cole a URL externa da imagem ou clique no botão para fazer upload diretamente da sua máquina/biblioteca do WordPress.</p>
                    <div class="f5-form-grid">
                        <div class="f5-field">
                            <label class="f5-label">Capa Vertical (Poster 3:4)</label>
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                <input type="text" id="f5_cover_url" name="cover_url" value="<?php echo esc_attr($cover_url); ?>" class="f5-input" style="flex: 1;" placeholder="https://.../capa.jpg">
                                <button type="button" class="f5-btn-secondary f5-upload-btn" data-target="#f5_cover_url" data-preview="#f5_cover_preview">
                                    📁 Upload / Mídia
                                </button>
                            </div>
                            <img id="f5_cover_preview" src="<?php echo esc_url($cover_url); ?>" style="width: 100px; height: 140px; object-fit: cover; border-radius: 8px; margin-top: 0.5rem; border: 1px solid #1f293d; <?php echo empty($cover_url) ? 'display:none;' : ''; ?>">
                        </div>

                        <div class="f5-field">
                            <label class="f5-label">Banner Horizontal (Hero 16:9)</label>
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                <input type="text" id="f5_banner_url" name="banner_url" value="<?php echo esc_attr($banner_url); ?>" class="f5-input" style="flex: 1;" placeholder="https://.../banner.jpg">
                                <button type="button" class="f5-btn-secondary f5-upload-btn" data-target="#f5_banner_url" data-preview="#f5_banner_preview">
                                    📁 Upload / Mídia
                                </button>
                            </div>
                            <img id="f5_banner_preview" src="<?php echo esc_url($banner_url); ?>" style="width: 200px; height: 110px; object-fit: cover; border-radius: 8px; margin-top: 0.5rem; border: 1px solid #1f293d; <?php echo empty($banner_url) ? 'display:none;' : ''; ?>">
                        </div>
                    </div>
                </div>

                <script>
                jQuery(document).ready(function($){
                    $('.f5-upload-btn').on('click', function(e){
                        e.preventDefault();
                        var btn = $(this);
                        var targetInput = $(btn.data('target'));
                        var previewImg = $(btn.data('preview'));

                        var frame = wp.media({
                            title: 'Selecionar ou Enviar Imagem',
                            button: { text: 'Usar esta Imagem' },
                            multiple: false
                        });

                        frame.on('select', function(){
                            var attachment = frame.state().get('selection').first().toJSON();
                            targetInput.val(attachment.url);
                            if (previewImg.length) {
                                previewImg.attr('src', attachment.url).css('display', 'block');
                            }
                        });

                        frame.open();
                    });
                });
                </script>


                <!-- 4. Regras de Exibição e Acesso -->
                <div class="f5-card">
                    <h2 class="f5-title" style="font-size: 1.15rem;">🔒 Regras de Acesso & Destaque</h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-top: 1rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; background: #060913; padding: 0.75rem 1rem; border-radius: 0.5rem; border: 1px solid #27344d; cursor: pointer;">
                            <input type="checkbox" name="is_featured" value="1" <?php checked($is_featured, 1); ?> style="accent-color: #e50914; width: 1.1rem; height: 1.1rem;">
                            <span style="font-size: 0.85rem; color: #fff; font-weight: 700;">⭐ Destaque no Hero Principal</span>
                        </label>

                        <label style="display: flex; align-items: center; gap: 0.5rem; background: #060913; padding: 0.75rem 1rem; border-radius: 0.5rem; border: 1px solid #27344d; cursor: pointer;">
                            <input type="checkbox" name="is_free" value="1" <?php checked($is_free, 1); ?> style="accent-color: #e50914; width: 1.1rem; height: 1.1rem;">
                            <span style="font-size: 0.85rem; color: #fff; font-weight: 700;">🎁 Conteúdo Gratuito</span>
                        </label>

                        <label style="display: flex; align-items: center; gap: 0.5rem; background: #060913; padding: 0.75rem 1rem; border-radius: 0.5rem; border: 1px solid #27344d; cursor: pointer;">
                            <input type="checkbox" name="is_exclusive" value="1" <?php checked($is_exclusive, 1); ?> style="accent-color: #e50914; width: 1.1rem; height: 1.1rem;">
                            <span style="font-size: 0.85rem; color: #fff; font-weight: 700;">👑 Exclusivo Premium</span>
                        </label>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="f5-btn-primary" style="padding: 0.85rem 2rem; font-size: 0.9rem;">
                        💾 Salvar Alterações da Produção
                    </button>
                </div>
            </form>

            <!-- 5. ESTRUTURA DE TEMPORADAS E EPISÓDIOS DA SÉRIE -->
            <div class="f5-card" style="margin-top: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h2 class="f5-title" style="font-size: 1.25rem;">🍿 Temporadas & Vídeos dos Episódios</h2>
                        <p class="f5-subtitle">Estruture os episódios de cada temporada com seus respectivos links do Vimeo ou MP4.</p>
                    </div>

                    <form method="post" action="admin-post.php" style="display: inline-flex; gap: 0.5rem; align-items: center;">
                        <?php wp_nonce_field('f5tv_add_season'); ?>
                        <input type="hidden" name="action" value="f5tv_add_season">
                        <input type="hidden" name="series_id" value="<?php echo esc_attr($post_id); ?>">
                        <input type="hidden" name="season_number" value="<?php echo count($seasons) + 1; ?>">
                        <button type="submit" class="f5-btn-secondary">
                            ➕ Adicionar Temporada <?php echo count($seasons) + 1; ?>
                        </button>
                    </form>
                </div>

                <?php if (empty($seasons)): ?>
                    <div style="background: #060913; padding: 2rem; border-radius: 0.75rem; text-align: center; margin-top: 1rem; border: 1px solid #1f293d; color: #9ca3af;">
                        <p style="font-weight: 700; color: #fff;">Nenhuma temporada cadastrada para esta série.</p>
                        <p style="font-size: 0.8rem;">Clique no botão "Adicionar Temporada 1" acima para adicionar os episódios.</p>
                    </div>
                <?php else: foreach ($seasons as $season):
                    $season_number = get_post_meta($season->ID, 'number', true) ?: 1;
                    $episodes = get_posts([
                        'post_type'      => 'f5tv_episodio',
                        'posts_per_page' => -1,
                        'meta_query'     => [[
                            'key' => 'season_id',
                            'value' => $season->ID,
                            'compare' => '=',
                        ]],
                        'orderby'        => 'meta_value_num',
                        'meta_key'       => 'number',
                        'order'          => 'ASC',
                    ]);
                ?>
                    <div class="f5-season-box">
                        <div style="display: flex; justify-content: space-between; align-items: center; border-b border-zinc-800 pb-3;">
                            <h3 style="font-size: 1.1rem; font-weight: 900; color: #fff; margin: 0;">
                                📺 Temporada <?php echo esc_html($season_number); ?>
                                <span style="font-size: 0.75rem; color: #e50914; font-weight: 700; font-family: monospace; margin-left: 0.5rem;"><?php echo count($episodes); ?> Episódio(s)</span>
                            </h3>
                        </div>

                        <!-- Lista de Episódios Existentes -->
                        <?php foreach ($episodes as $ep):
                            $ep_video_url = f5tv_get_field('video_url', $ep->ID) ?: '';
                            $ep_duration  = f5tv_get_field('duration', $ep->ID) ?: '45m';
                            $ep_number    = f5tv_get_field('number', $ep->ID) ?: 1;
                            $ep_vimeo     = strpos($ep_video_url, 'vimeo') !== false;
                        ?>
                            <form method="post" action="admin-post.php" class="f5-episode-item">
                                <?php wp_nonce_field('f5tv_save_episode'); ?>
                                <input type="hidden" name="action" value="f5tv_save_episode">
                                <input type="hidden" name="series_id" value="<?php echo esc_attr($post_id); ?>">
                                <input type="hidden" name="season_id" value="<?php echo esc_attr($season->ID); ?>">
                                <input type="hidden" name="episode_id" value="<?php echo esc_attr($ep->ID); ?>">

                                <div style="display: grid; grid-template-columns: 80px 1fr 140px; gap: 0.75rem; align-items: center;">
                                    <div>
                                        <label class="f5-label">Nº Ep</label>
                                        <input type="number" name="ep_number" value="<?php echo esc_attr($ep_number); ?>" class="f5-input font-mono">
                                    </div>
                                    <div>
                                        <label class="f5-label">Título do Episódio</label>
                                        <input type="text" name="ep_title" value="<?php echo esc_attr($ep->post_title); ?>" class="f5-input" style="font-weight: 700;">
                                    </div>
                                    <div>
                                        <label class="f5-label">Duração</label>
                                        <input type="text" name="ep_duration" value="<?php echo esc_attr($ep_duration); ?>" class="f5-input">
                                    </div>
                                </div>

                                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                                        <label class="f5-label" style="flex:1;">
                                            Vídeo do Episódio (Vimeo / MP4)
                                            <?php if ($ep_vimeo): ?>
                                                <span style="color: #38bdf8; font-family: monospace; font-size: 10px; margin-left: 0.5rem;">[VIMEO HD]</span>
                                            <?php endif; ?>
                                        </label>
                                        <button type="button" class="f5-btn-secondary" data-f5vm-open="f5_ep_video_url_<?php echo esc_attr($ep->ID); ?>" style="white-space:nowrap;font-size:0.72rem;padding:0.3rem 0.75rem;">
                                            🎬 Selecionar
                                        </button>
                                    </div>
                                    <div style="display: grid; grid-template-columns: 1fr 120px; gap: 0.75rem; align-items: center;">
                                        <input type="text" id="f5_ep_video_url_<?php echo esc_attr($ep->ID); ?>" name="ep_video_url" value="<?php echo esc_attr($ep_video_url); ?>" class="f5-input font-mono" placeholder="https://vimeo.com/123456789">
                                        <button type="submit" class="f5-btn-secondary" style="width: 100%; justify-content: center;">
                                            💾 Salvar Ep
                                        </button>
                                    </div>
                                </div>
                            </form>
                        <?php endforeach; ?>

                        <!-- Formulário para Adicionar Novo Episódio -->
                        <form method="post" action="admin-post.php" class="f5-episode-item" style="border: 1px dashed #e50914; background: rgba(229,9,20,0.03);">
                            <?php wp_nonce_field('f5tv_save_episode'); ?>
                            <input type="hidden" name="action" value="f5tv_save_episode">
                            <input type="hidden" name="series_id" value="<?php echo esc_attr($post_id); ?>">
                            <input type="hidden" name="season_id" value="<?php echo esc_attr($season->ID); ?>">
                            <input type="hidden" name="episode_id" value="0">

                            <div style="font-size: 0.8rem; font-weight: 800; color: #e50914; text-transform: uppercase;">
                                ➕ Adicionar Novo Episódio na Temporada <?php echo esc_html($season_number); ?>
                            </div>

                            <div style="display: grid; grid-template-columns: 80px 1fr 140px; gap: 0.75rem; align-items: center;">
                                <div>
                                    <label class="f5-label">Nº Ep</label>
                                    <input type="number" name="ep_number" value="<?php echo count($episodes) + 1; ?>" class="f5-input font-mono">
                                </div>
                                <div>
                                    <label class="f5-label">Título do Episódio</label>
                                    <input type="text" name="ep_title" placeholder="ex: Episódio <?php echo count($episodes) + 1; ?>" class="f5-input">
                                </div>
                                <div>
                                    <label class="f5-label">Duração</label>
                                    <input type="text" name="ep_duration" value="45m" class="f5-input">
                                </div>
                            </div>

                            <div>
                                <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom:0.4rem;">
                                    <label class="f5-label" style="flex:1;">Vídeo do Episódio (Vimeo / MP4)</label>
                                    <button type="button" class="f5-btn-secondary" data-f5vm-open="f5_new_ep_video_<?php echo esc_attr($season->ID); ?>" style="white-space:nowrap;font-size:0.72rem;padding:0.3rem 0.75rem;">
                                        🎬 Selecionar
                                    </button>
                                </div>
                                <input type="text" id="f5_new_ep_video_<?php echo esc_attr($season->ID); ?>" name="ep_video_url" placeholder="https://vimeo.com/123456789" class="f5-input font-mono">
                            </div>
                            <button type="submit" class="f5-btn-primary" style="margin-top: 1.5rem; width: 100%; justify-content: center; font-size: 0.75rem;">
                                ➕ Criar Episódio
                            </button>
                        </form>

                    </div>
                <?php endforeach; endif; ?>
            </div>

        </div>
        <?php
    }

    /**
     * Renderiza o Grid da Central de Conteúdos
     */
    private function render_studio_grid(): void
    {
        $search = sanitize_text_field($_GET['s'] ?? '');
        $type_filter = sanitize_text_field($_GET['type'] ?? '');

        $args = [
            'post_type'      => ['f5tv_conteudo', 'f5tv_serie'],
            'posts_per_page' => 40,
            'post_status'    => 'publish',
            's'              => $search,
        ];

        if ($type_filter) {
            $args['post_type'] = $type_filter;
        }

        $query = new WP_Query($args);
        $items = $query->posts;
        ?>
        <style>
            .f5-studio-wrap { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; color: #f4f4f5; max-width: 1250px; margin: 20px 0; }
            .f5-card { background: #0c101d; border: 1px solid #1f293d; border-radius: 1rem; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
            .f5-title { font-size: 1.6rem; font-weight: 900; color: #ffffff; letter-spacing: -0.03em; display: flex; align-items: center; gap: 0.75rem; }
            .f5-subtitle { color: #9ca3af; font-size: 0.85rem; margin-top: 0.25rem; }
            
            .f5-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1.25rem; margin-top: 1.5rem; }
            .f5-item-card { background: #060913; border: 1px solid #1f293d; border-radius: 0.85rem; overflow: hidden; display: flex; flex-direction: column; transition: all 0.25s ease; position: relative; }
            .f5-item-card:hover { transform: translateY(-4px); border-color: #e50914; box-shadow: 0 12px 30px rgba(229, 9, 20, 0.25); }
            
            .f5-poster-wrap { aspect-ratio: 3/4; width: 100%; position: relative; background: #0e1424; overflow: hidden; }
            .f5-poster-img { width: 100%; height: 100%; object-fit: cover; }
            .f5-type-tag { position: absolute; top: 10px; left: 10px; background: #e50914; color: #fff; font-family: monospace; font-size: 9px; font-weight: 900; padding: 3px 8px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.05em; box-shadow: 0 4px 10px rgba(0,0,0,0.5); }
            .f5-source-badge { position: absolute; top: 10px; right: 10px; background: rgba(6, 9, 19, 0.85); backdrop-filter: blur(4px); color: #38bdf8; border: 1px solid rgba(255,255,255,0.1); font-family: monospace; font-size: 9px; font-weight: 800; padding: 3px 8px; border-radius: 4px; }
            
            .f5-item-info { padding: 1rem; flex: 1; display: flex; flex-direction: column; justify-content: space-between; gap: 0.75rem; }
            .f5-item-title { font-weight: 800; font-size: 0.95rem; color: #ffffff; line-height: 1.25; margin: 0; }
            .f5-item-meta { font-size: 0.75rem; color: #9ca3af; display: flex; justify-content: space-between; align-items: center; }
            
            .f5-btn-edit { background: #e50914; color: #ffffff !important; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 0.5rem 0.85rem; border-radius: 0.4rem; text-decoration: none; text-align: center; display: inline-flex; align-items: center; justify-content: center; gap: 0.35rem; transition: background 0.2s; }
            .f5-btn-edit:hover { background: #b80710; }
            .f5-btn-play { background: #1f293d; color: #38bdf8 !important; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 0.5rem 0.85rem; border-radius: 0.4rem; text-decoration: none; text-align: center; display: inline-flex; align-items: center; justify-content: center; gap: 0.35rem; }
            .f5-btn-play:hover { background: #38bdf8; color: #000000 !important; }

            .f5-search-bar { display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 1.25rem; }
            .f5-input { background: #060913; border: 1px solid #27344d; color: #ffffff; padding: 0.65rem 0.85rem; border-radius: 0.5rem; font-size: 0.85rem; }
        </style>

        <div class="f5-studio-wrap wrap">
            <!-- Header do Estúdio -->
            <div class="f5-card" style="background: linear-gradient(135deg, #0c101d 0%, #151b2e 100%);">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <span style="font-family: monospace; font-size: 10px; padding: 3px 8px; background: rgba(229,9,20,0.15); color: #e50914; border: 1px solid rgba(229,9,20,0.3); border-radius: 4px; font-weight: 900; text-transform: uppercase;">
                            ESTÚDIO DE GERENCIAMENTO F5 STREAMING
                        </span>
                        <h1 class="f5-title" style="margin-top: 0.5rem;">🍿 Central de Produções & Conteúdos</h1>
                        <p class="f5-subtitle">Gerencie capas, vídeos do Vimeo/MP4, temporadas e episódios das séries em tempo real.</p>
                    </div>

                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <a href="<?php echo esc_url(admin_url('admin.php?page=f5tv-content-studio&action=new&type=f5tv_conteudo')); ?>" class="f5-btn-primary">
                            🎬 Cadastrar Conteúdo / Filme
                        </a>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=f5tv-content-studio&action=new&type=f5tv_serie')); ?>" class="f5-btn-secondary">
                            🍿 Cadastrar Nova Série
                        </a>
                    </div>
                </div>

                <form method="get" class="f5-search-bar">
                    <input type="hidden" name="post_type" value="f5tv_conteudo">
                    <input type="hidden" name="page" value="f5tv-content-studio">
                    <input type="text" name="s" value="<?php echo esc_attr($search); ?>" placeholder="Buscar título no acervo..." class="f5-input" style="min-width: 280px;">
                    <select name="type" class="f5-input" style="min-width: 160px;">
                        <option value="">Todos os Tipos</option>
                        <option value="f5tv_conteudo" <?php selected($type_filter, 'f5tv_conteudo'); ?>>Conteúdos (Filmes/Boletins)</option>
                        <option value="f5tv_serie" <?php selected($type_filter, 'f5tv_serie'); ?>>Séries</option>
                    </select>
                    <button type="submit" class="f5-btn-edit">🔍 Buscar</button>
                </form>
            </div>

            <!-- Grid de Cards Estilo Studio Admin -->
            <div class="f5-grid">
                <?php if (empty($items)): ?>
                    <div style="grid-column: 1/-1; background: #0c101d; padding: 3rem; border-radius: 1rem; text-align: center; color: #9ca3af; border: 1px solid #1f293d;">
                        <p style="font-size: 1.1rem; font-weight: 700; color: #fff;">Nenhum conteúdo encontrado com esses filtros.</p>
                        <p style="font-size: 0.85rem;">Clique em "Cadastrar Conteúdo" acima para adicionar seu primeiro vídeo com suporte a Vimeo ou MP4.</p>
                    </div>
                <?php else: foreach ($items as $post):
                    $post_id = $post->ID;
                    $cover = f5tv_get_field('cover_url', $post_id) ?: get_the_post_thumbnail_url($post_id, 'medium') ?: 'https://images.unsplash.com/photo-1594909122845-11baa439b7bf?q=80&w=600';
                    $video_url = f5tv_get_field('video_url', $post_id) ?: '';
                    $genre = f5tv_get_field('genre', $post_id) ?: 'Streaming';
                    $views = intval(f5tv_get_field('views_count', $post_id));
                    
                    // Identificar Fonte do Vídeo
                    $source = 'MP4 Direct';
                    if (strpos($video_url, 'vimeo') !== false) {
                        $source = 'VIMEO HD';
                    } elseif (strpos($video_url, 'youtube') !== false || strpos($video_url, 'youtu.be') !== false) {
                        $source = 'YOUTUBE';
                    } elseif (strpos($video_url, '.m3u8') !== false) {
                        $source = 'HLS STREAM';
                    }
                ?>
                    <div class="f5-item-card">
                        <div class="f5-poster-wrap">
                            <img src="<?php echo esc_url($cover); ?>" alt="<?php echo esc_attr($post->post_title); ?>" class="f5-poster-img">
                            <span class="f5-type-tag"><?php echo esc_html($post->post_type === 'f5tv_serie' ? 'SÉRIE' : 'CONTEÚDO'); ?></span>
                            <span class="f5-source-badge"><?php echo esc_html($source); ?></span>
                        </div>

                        <div class="f5-item-info">
                            <div>
                                <h3 class="f5-item-title"><?php echo esc_html($post->post_title); ?></h3>
                                <div class="f5-item-meta" style="margin-top: 0.5rem;">
                                    <span style="color: #e50914; font-weight: 700; font-family: monospace; text-transform: uppercase;"><?php echo esc_html($genre); ?></span>
                                    <span>👁️ <?php echo number_format($views); ?></span>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-top: 0.5rem;">
                                <a href="<?php echo esc_url(admin_url('admin.php?page=f5tv-content-studio&action=edit&id=' . $post_id)); ?>" class="f5-btn-edit">
                                    ✏️ Editar
                                </a>
                                <a href="<?php echo esc_url(home_url('/assista?id=' . $post_id)); ?>" target="_blank" class="f5-btn-play">
                                    ▶️ Player
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
        <?php
    }
}
