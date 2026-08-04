<?php
/**
 * Template Name: Minha Conta
 * Description: Gerenciamento da conta do assinante, plano ativo, perfis e faturamento.
 */

get_header();

$current_user = wp_get_current_user();
$is_logged_in = is_user_logged_in();
?>

<div class="min-h-screen bg-f5-blue text-white font-sans selection:bg-f5-red p-6 md:p-10">
    <div class="max-w-4xl mx-auto flex flex-col gap-8">
        <div class="border-b border-zinc-900 pb-4">
            <span class="text-f5-red font-mono font-black text-xs tracking-widest uppercase">PAINEL PESSOAL</span>
            <h1 class="text-3xl font-black tracking-tight mt-1">Minha Conta</h1>
        </div>

        <?php if (!$is_logged_in): ?>
            <div class="bg-f5-blue-950 border border-zinc-900 p-8 rounded-2xl text-center flex flex-col items-center gap-4">
                <p class="text-zinc-400 text-sm">Você precisa estar conectado para acessar os detalhes da sua conta.</p>
                <a href="<?php echo esc_url(wp_login_url()); ?>" class="bg-f5-red hover:bg-f5-red-700 text-white font-mono font-bold text-xs uppercase px-6 py-3 rounded-xl transition">
                    Fazer Login
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- User card -->
                <div class="bg-f5-blue-950 border border-zinc-900 p-6 rounded-2xl flex flex-col gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-f5-red flex items-center justify-center font-black text-2xl text-white">
                        <?php echo esc_html(strtoupper(substr($current_user->display_name ?: $current_user->user_login, 0, 1))); ?>
                    </div>
                    <div class="flex flex-col">
                        <h2 class="text-lg font-bold text-white"><?php echo esc_html($current_user->display_name); ?></h2>
                        <span class="text-xs text-zinc-500 font-mono"><?php echo esc_html($current_user->user_email); ?></span>
                    </div>
                    <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="mt-4 text-xs font-mono font-bold text-f5-red hover:underline">
                        Sair da conta &rarr;
                    </a>
                </div>

                <!-- Subscription card -->
                <div class="md:col-span-2 bg-f5-blue-950 border border-zinc-900 p-6 rounded-2xl flex flex-col gap-4">
                    <div class="flex items-center justify-between border-b border-zinc-900 pb-3">
                        <span class="text-xs font-mono font-bold text-zinc-500 uppercase">Assinatura Ativa</span>
                        <span class="bg-emerald-950 border border-emerald-800 text-emerald-400 text-[10px] font-mono font-bold px-2.5 py-0.5 rounded-md uppercase">Ativa</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <h3 class="text-xl font-black text-white">Plano Premium 4K</h3>
                        <p class="text-xs text-zinc-400">Acesso ilimitado a 5 telas simultâneas, 4K Ultra HD e canais ao vivo.</p>
                    </div>
                    <div class="flex items-center gap-4 mt-2">
                        <a href="<?php echo esc_url(home_url('/planos/')); ?>" class="bg-f5-blue-900 hover:bg-zinc-800 border border-zinc-800 text-white font-mono font-bold text-xs uppercase px-4 py-2.5 rounded-xl transition">
                            Alterar Plano
                        </a>
                        <a href="<?php echo esc_url(home_url('/dispositivos/')); ?>" class="text-xs font-mono text-zinc-400 hover:text-white transition">
                            Gerenciar Dispositivos &rarr;
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
