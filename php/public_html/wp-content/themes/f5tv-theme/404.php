<?php
/**
 * 404 error template for F5TV Theme
 */

get_header();
?>

<div class="min-h-screen bg-f5-blue text-white font-sans flex items-center justify-center px-4 py-20">
    <div class="text-center max-w-md">
        <h1 class="text-8xl font-black tracking-tighter text-f5-red mb-4">404</h1>
        <p class="text-xl font-bold text-white mb-2">Página não encontrada</p>
        <p class="text-sm text-zinc-400 mb-8">A página que você está procurando não existe ou foi removida.</p>
        <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-flex bg-f5-red hover:bg-f5-red-700 text-white font-bold px-8 py-3 rounded text-xs uppercase tracking-wider transition">
            Voltar para a página inicial
        </a>
    </div>
</div>

<?php
get_footer();
