<?php
/**
 * Template Name: Login
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
            <p class="text-white/50 text-sm mt-4">Acesse sua conta para continuar assistindo</p>
        </div>

        <div class="bg-f5-blue-950 border border-white/5 rounded-2xl p-8 shadow-2xl">
            <?php if (isset($_GET['login']) && $_GET['login'] === 'failed'): ?>
                <div class="mb-6 p-4 bg-f5-red-950/40 border border-f5-red-900 rounded-lg text-xs text-f5-red-400">
                    E-mail ou senha incorretos. Tente novamente.
                </div>
            <?php endif; ?>

            <form action="<?php echo esc_url(wp_login_url()); ?>" method="post" class="flex flex-col gap-5">
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-semibold text-zinc-300">E-mail</label>
                    <input type="text" name="log" placeholder="seu@email.com" class="w-full bg-[#030303] border border-white/5 focus:border-f5-red rounded-lg p-3 text-sm text-white placeholder:text-zinc-600 outline-none transition" required>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-semibold text-zinc-300">Senha</label>
                    <input type="password" name="pwd" placeholder="••••••••" class="w-full bg-[#030303] border border-white/5 focus:border-f5-red rounded-lg p-3 text-sm text-white placeholder:text-zinc-600 outline-none transition" required>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 text-zinc-400 cursor-pointer">
                        <input type="checkbox" name="rememberme" value="forever" class="rounded border-zinc-700 bg-f5-blue-900 text-f5-red focus:ring-f5-red">
                        <span>Lembrar-me</span>
                    </label>
                    <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="text-f5-red hover:underline">Esqueceu a senha?</a>
                </div>
                <button type="submit" name="wp-submit" class="w-full bg-f5-red hover:bg-f5-red-700 text-white font-bold py-3 rounded-lg text-xs uppercase tracking-wider font-mono cursor-pointer transition shadow hover:shadow-red-950/40">
                    Entrar
                </button>
                <input type="hidden" name="redirect_to" value="<?php echo esc_url(home_url('/area-do-assinante/')); ?>">
            </form>

            <div class="mt-6 pt-6 border-t border-white/5 text-center text-xs text-zinc-500">
                Ainda não tem conta? <a href="<?php echo esc_url(home_url('/cadastro/')); ?>" class="text-f5-red hover:underline font-semibold">Assine agora</a>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
