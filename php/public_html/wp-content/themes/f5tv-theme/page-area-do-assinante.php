<?php
/**
 * Template Name: Área do Assinante
 * Description: Página principal do painel do assinante da F5 TV.
 */

get_header();
?>

<div class="min-h-screen bg-f5-blue text-white font-sans flex flex-col justify-between selection:bg-f5-red pb-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-8 py-8 w-full">
        <div class="flex items-center justify-between border-b border-white/5 pb-6 mb-8">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-white uppercase tracking-tight">Área do Assinante</h1>
                <p class="text-xs text-zinc-400 font-mono mt-1">Gerencie seu perfil, assista conteúdos continuados e explore seus favoritos.</p>
            </div>
            <div id="f5tv-current-profile" class="flex items-center gap-3 bg-f5-blue-950 px-4 py-2 rounded-xl border border-white/5">
                <!-- Preenchido via client-app.js -->
            </div>
        </div>

        <?php echo do_shortcode('[f5tv_dashboard]'); ?>
    </div>
</div>

<?php get_footer(); ?>
