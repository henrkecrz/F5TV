<?php
/**
 * Template Name: Player Assista
 * Description: Player de vídeo interativo para reproduzir conteúdos, episódios e acompanhar progresso.
 */

get_header();

$content_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$content_post = $content_id ? get_post($content_id) : null;

$video_url = $content_post ? (get_field('video_url', $content_id) ?: 'https://assets.mixkit.co/videos/preview/mixkit-software-developer-working-on-his-computer-34289-large.mp4') : 'https://assets.mixkit.co/videos/preview/mixkit-software-developer-working-on-his-computer-34289-large.mp4';
$banner_url = $content_post ? (get_field('banner_url', $content_id) ?: get_the_post_thumbnail_url($content_id, 'full')) : '';
$title = $content_post ? get_the_title($content_post) : 'Reproduzindo Conteúdo F5 TV';
?>

<div class="min-h-screen bg-black text-white font-sans flex flex-col justify-between selection:bg-f5-red">
    <div class="max-w-7xl w-full mx-auto px-4 py-6 flex flex-col gap-4">
        
        <div class="flex items-center justify-between border-b border-zinc-900 pb-3 text-xs font-mono">
            <a href="javascript:history.back()" class="text-zinc-400 hover:text-white transition flex items-center gap-1.5 font-bold uppercase">
                &larr; Voltar
            </a>
            <span class="text-f5-red font-bold uppercase">REPRODUTOR DIGITAL F5 STREAM</span>
        </div>

        <!-- Video Player Wrapper -->
        <div class="relative aspect-video w-full bg-zinc-950 rounded-2xl overflow-hidden shadow-2xl border border-zinc-900">
            <?php echo f5tv_render_video_player($video_url, $banner_url, $title); ?>
        </div>

        <div class="bg-f5-blue-950 border border-zinc-900 p-6 rounded-2xl flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-black text-white"><?php echo esc_html($title); ?></h1>
                <span class="bg-f5-red/10 border border-f5-red/30 text-f5-red text-[10px] font-mono font-bold px-3 py-1 rounded-full uppercase">1080P Full HD</span>
            </div>
            <?php if ($content_post && get_the_excerpt($content_post)): ?>
                <p class="text-xs text-zinc-400 leading-relaxed max-w-3xl"><?php echo esc_html(get_the_excerpt($content_post)); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const player = document.getElementById('f5tv-main-player');
    const contentId = <?php echo json_encode($content_id); ?>;
    
    if (player && contentId) {
        let lastSaved = 0;
        player.addEventListener('timeupdate', () => {
            const currentTime = Math.floor(player.currentTime);
            const duration = Math.floor(player.duration) || 1;
            if (currentTime - lastSaved >= 15) {
                lastSaved = currentTime;
                const progress = Math.min(100, (currentTime / duration) * 100);
                
                fetch('/wp-json/f5tv/v1/watch-history', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        content_id: contentId,
                        watched_seconds: currentTime,
                        total_seconds: duration,
                        progress: progress
                    })
                }).catch(() => {});
            }
        });
    }
});
</script>

<?php get_footer(); ?>
