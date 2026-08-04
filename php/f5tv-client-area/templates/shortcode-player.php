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
$can_access = true;

if ($user_id) {
    $user = get_userdata($user_id);
    if (!in_array('administrator', (array)$user->roles) && !in_array('editor', (array)$user->roles)) {
        $is_free = get_field('is_free', $content_id);
        if (!$is_free) {
            $can_access = current_user_can('f5tv_access_content', $content_id);
        }
    }
} else {
    $is_free = $content_id ? get_field('is_free', $content_id) : false;
    if (!$is_free) {
        $can_access = false;
    }
}

if (!$can_access) {
    echo '<div class="p-8 text-center bg-f5-blue-950 border border-f5-red/30 rounded-2xl flex flex-col items-center gap-3">
        <span class="text-f5-red font-mono font-bold text-xs uppercase">Acesso Restrito</span>
        <h3 class="text-xl font-black text-white">Assine para Assistir</h3>
        <p class="text-xs text-zinc-400 max-w-md">Este conteúdo é exclusivo para assinantes F5 TV Premium.</p>
        <a href="/planos/" class="mt-2 bg-f5-red hover:bg-f5-red-700 text-white font-bold px-6 py-2.5 rounded-xl text-xs uppercase font-mono transition">Conhecer Planos</a>
    </div>';
    return;
}

$signed_url = apply_filters('f5tv_signed_video_url', $video_url, $content_id, $user_id);
?>
<div class="f5tv-player-wrapper aspect-video w-full rounded-2xl overflow-hidden bg-black border border-zinc-900 shadow-2xl relative" data-content-id="<?php echo esc_attr($content_id); ?>">
    <?php
    if (function_exists('f5tv_render_video_player')) {
        echo f5tv_render_video_player($signed_url, $poster_url);
    } else {
        echo '<video id="f5tv-player" controls poster="' . esc_url($poster_url) . '" class="w-full h-full object-cover"><source src="' . esc_url($signed_url) . '"></video>';
    }
    ?>
</div>
