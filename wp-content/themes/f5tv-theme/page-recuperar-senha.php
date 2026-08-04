<?php
/**
 * Template Name: Recuperar Senha
 */

get_header();
?>

<div class="min-h-screen bg-f5-blue text-white font-sans flex items-center justify-center px-4 py-20">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-block">
                <img src="<?php echo esc_url(F5TV_ASSETS_URI . '/images/f5tv-logo-neg.png'); ?>" alt="F5 TV" class="h-12 w-auto object-contain" width="180" height="60">
            </a>
            <p class="text-white/50 text-sm mt-4">Recupere o acesso à sua conta</p>
        </div>

        <div class="bg-f5-blue-950 border border-white/5 rounded-2xl p-8 shadow-2xl">
            <?php
            if (isset($_GET['key']) && isset($_GET['login'])) {
                echo do_shortcode('[lostpassword_form]');
            } else {
                echo do_shortcode('[lostpassword_form]');
            }
            ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
