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
                <span class="text-3xl font-black tracking-tighter text-white uppercase">
                    F5 <span class="text-f5-red">TV</span>
                </span>
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
