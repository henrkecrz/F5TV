<?php
/**
 * F5TV — Botão "Minha Lista" reutilizável
 * Uso: f5tv_render_minha_lista_button(get_the_ID());
 * Requer window.f5tvRest no <head> (injetado via functions.php wp_head)
 */
if (!function_exists('f5tv_render_minha_lista_button')) {
    function f5tv_render_minha_lista_button(int $post_id, string $extra_classes = ''): void {
        $user_id   = get_current_user_id();
        $list      = $user_id ? (array)(get_user_meta($user_id, 'f5tv_minha_lista', true) ?: []) : [];
        $in_list   = in_array($post_id, array_map('intval', $list), true);
        $logged_in = is_user_logged_in();
        ?>
        <button
            type="button"
            id="f5tv-minha-lista-btn-<?php echo $post_id; ?>"
            data-post-id="<?php echo esc_attr($post_id); ?>"
            data-in-list="<?php echo $in_list ? 'true' : 'false'; ?>"
            class="f5tv-minha-lista-btn py-3.5 px-5 rounded-xl border flex items-center justify-center gap-1.5 cursor-pointer text-xs font-mono font-bold uppercase transition <?php echo $in_list
                ? 'bg-f5-red-950/20 border-f5-red-900 text-f5-red'
                : 'bg-f5-blue-950/50 border-zinc-900 text-zinc-400 hover:border-zinc-700 hover:text-white'; ?> <?php echo esc_attr($extra_classes); ?>"
            title="<?php echo $in_list ? 'Remover da Minha Lista' : 'Adicionar à Minha Lista'; ?>"
        >
            <svg class="w-4 h-4 f5tv-heart-icon <?php echo $in_list ? 'fill-f5-red text-f5-red' : ''; ?>" fill="<?php echo $in_list ? 'currentColor' : 'none'; ?>" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            <span class="f5tv-lista-label"><?php echo $in_list ? 'Na Minha Lista' : 'Salvar Lista'; ?></span>
        </button>

        <script>
        (function(){
            var btn = document.getElementById('f5tv-minha-lista-btn-<?php echo $post_id; ?>');
            if (!btn) return;

            btn.addEventListener('click', function() {
                var rest = window.f5tvRest || {};

                // Usuário não logado — redirecionar para login
                if (!rest.userId) {
                    window.location.href = (rest.loginUrl || '<?php echo esc_url(wp_login_url(get_permalink($post_id))); ?>');
                    return;
                }

                btn.disabled = true;
                btn.style.opacity = '0.6';

                fetch((rest.restUrl || '/wp-json/f5tv/v1') + '/minha-lista/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': rest.nonce || ''
                    },
                    body: JSON.stringify({ post_id: <?php echo $post_id; ?> })
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    var inList   = data.in_list === true || data.action === 'added';
                    var label    = btn.querySelector('.f5tv-lista-label');
                    var heartSvg = btn.querySelector('.f5tv-heart-icon');

                    // Update label
                    if (label) label.textContent = inList ? 'Na Minha Lista' : 'Salvar Lista';

                    // Update heart fill
                    if (heartSvg) {
                        heartSvg.setAttribute('fill', inList ? 'currentColor' : 'none');
                        heartSvg.classList.toggle('fill-f5-red',  inList);
                        heartSvg.classList.toggle('text-f5-red',  inList);
                    }

                    // Update button classes
                    btn.setAttribute('data-in-list', inList ? 'true' : 'false');
                    btn.title = inList ? 'Remover da Minha Lista' : 'Adicionar à Minha Lista';

                    if (inList) {
                        btn.classList.remove('bg-f5-blue-950/50','border-zinc-900','text-zinc-400','hover:border-zinc-700','hover:text-white');
                        btn.classList.add('bg-f5-red-950/20','border-f5-red-900','text-f5-red');
                    } else {
                        btn.classList.remove('bg-f5-red-950/20','border-f5-red-900','text-f5-red');
                        btn.classList.add('bg-f5-blue-950/50','border-zinc-900','text-zinc-400','hover:border-zinc-700','hover:text-white');
                    }

                    // Micro-feedback: pulse
                    btn.style.transform = 'scale(1.05)';
                    setTimeout(function() { btn.style.transform = ''; }, 200);
                })
                .catch(function(err) {
                    console.error('f5tv minha-lista error:', err);
                })
                .finally(function() {
                    btn.disabled = false;
                    btn.style.opacity = '';
                });
            });
        })();
        </script>
        <?php
    }
}
