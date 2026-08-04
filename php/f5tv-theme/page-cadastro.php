<?php
/**
 * Template Name: Cadastro
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
            <p class="text-white/50 text-sm mt-4">Crie sua conta e escolha um plano para começar</p>
        </div>

        <div class="bg-f5-blue-950 border border-white/5 rounded-2xl p-8 shadow-2xl">
            <?php if (!is_user_logged_in()): ?>
                <form action="<?php echo esc_url(home_url('/wp-register.php')); ?>" method="post" class="flex flex-col gap-5">
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-semibold text-zinc-300">Nome completo</label>
                        <input type="text" name="first_name" placeholder="Seu nome" class="w-full bg-[#030303] border border-white/5 focus:border-f5-red rounded-lg p-3 text-sm text-white placeholder:text-zinc-600 outline-none transition" required>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-semibold text-zinc-300">E-mail</label>
                        <input type="email" name="email" placeholder="seu@email.com" class="w-full bg-[#030303] border border-white/5 focus:border-f5-red rounded-lg p-3 text-sm text-white placeholder:text-zinc-600 outline-none transition" required>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="text-xs font-semibold text-zinc-300">Senha</label>
                        <input type="password" name="password" placeholder="••••••••" class="w-full bg-[#030303] border border-white/5 focus:border-f5-red rounded-lg p-3 text-sm text-white placeholder:text-zinc-600 outline-none transition" required>
                    </div>
                    <button type="submit" class="w-full bg-f5-red hover:bg-f5-red-700 text-white font-bold py-3 rounded-lg text-xs uppercase tracking-wider font-mono cursor-pointer transition shadow hover:shadow-red-950/40">
                        Criar Conta
                    </button>
                </form>
            <?php else: ?>
                <p class="text-sm text-zinc-300 text-center">Você já está logado.</p>
                <a href="<?php echo esc_url(home_url('/area-do-assinante/')); ?>" class="mt-4 w-full bg-f5-red hover:bg-f5-red-700 text-white font-bold py-3 rounded-lg text-xs uppercase tracking-wider font-mono flex items-center justify-center transition">
                    Acessar Área do Assinante
                </a>
            <?php endif; ?>

            <div class="mt-6 pt-6 border-t border-white/5 text-center text-xs text-zinc-500">
                Já tem conta? <a href="<?php echo esc_url(home_url('/login/')); ?>" class="text-f5-red hover:underline font-semibold">Entrar</a>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
