<?php
/**
 * Template: Shortcode Player de Vídeo com suporte completo a Vimeo, YouTube, MP4 e HLS
 * Uso: [f5tv_player url="https://..." poster="https://..." id="123"]
 */

if (!defined('ABSPATH')) {
    exit;
}

$video_url = sanitize_text_field($atts['url'] ?? '');
$poster_url = sanitize_text_field($atts['poster'] ?? '');
$content_id = intval($atts['id'] ?? 0);

if (!$video_url && $content_id) {
    $video_url = get_field('video_url', $content_id) ?: '';
}

if (!$video_url) {
    echo '<p class="f5tv-player-error text-zinc-500 font-mono text-xs text-center py-8">Vídeo não configurado.</p>';
    return;
}

$user_id = get_current_user_id();
if (!$user_id) {
    $watch_url = home_url('/assista?id=' . $content_id);
    $login_url = add_query_arg('redirect_to', $watch_url, home_url('/login/'));
    echo '<div class="p-8 text-center bg-f5-blue-950 border border-f5-red/30 rounded-2xl flex flex-col items-center gap-3">
        <span class="text-f5-red font-mono font-bold text-xs uppercase">Acesso Restrito</span>
        <h3 class="text-xl font-black text-white">Entre para assistir grátis</h3>
        <p class="text-xs text-zinc-400 max-w-md">Crie sua conta gratuita para acessar este conteúdo.</p>
        <a href="' . esc_url($login_url) . '" class="mt-2 bg-f5-red hover:bg-f5-red-700 text-white font-bold px-6 py-2.5 rounded-xl text-xs uppercase font-mono transition">Entrar ou criar conta</a>
    </div>';
    return;
}

$watch_url = home_url('/assista?id=' . $content_id);
?>
<div class="f5tv-player-wrapper aspect-video w-full rounded-2xl overflow-hidden bg-black border border-zinc-900 shadow-2xl relative" data-content-id="<?php echo esc_attr($content_id); ?>">
    <a href="<?php echo esc_url($watch_url); ?>" class="absolute inset-0 flex items-center justify-center bg-black/60 text-white font-black uppercase">Assistir agora</a>
</div>
