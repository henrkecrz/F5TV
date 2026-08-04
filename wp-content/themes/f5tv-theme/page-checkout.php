<?php
/**
 * Template Name: Checkout
 * Description: Página de finalização de assinatura com seleção de método de pagamento (Cartão, Pix, Boleto) e cupom.
 */

get_header();

?>
<main class="min-h-[55vh] bg-f5-blue text-white flex items-center justify-center px-6 py-20">
    <div class="max-w-2xl text-center">
        <span class="text-f5-red font-mono font-black text-xs tracking-widest uppercase">ASSINATURAS EM BREVE</span>
        <h1 class="text-3xl md:text-5xl font-black tracking-tight mt-3">Conteúdos por assinatura disponíveis em breve!</h1>
        <p class="text-white/60 text-sm md:text-base mt-5">A área de assinatura ainda não está disponível. Em breve teremos novidades.</p>
    </div>
</main>
<?php
get_footer();
return;

$plan = isset($_GET['plan']) ? sanitize_text_field($_GET['plan']) : 'plano-premium';
$plan_names = [
    'plano-basico'  => ['name' => 'Plano Básico', 'price' => 19.90],
    'plano-familia' => ['name' => 'Plano Família', 'price' => 34.90],
    'plano-premium' => ['name' => 'Plano Premium 4K', 'price' => 49.90],
];
$selected_plan = $plan_names[$plan] ?? $plan_names['plano-premium'];
?>

<div class="min-h-screen bg-f5-blue text-white font-sans selection:bg-f5-red p-6 md:p-10">
    <div class="max-w-4xl mx-auto flex flex-col gap-8">
        <div class="border-b border-zinc-900 pb-4">
            <span class="text-f5-red font-mono font-black text-xs tracking-widest uppercase">CHECKOUT SEGURO</span>
            <h1 class="text-3xl font-black tracking-tight mt-1">Ativar Assinatura</h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Order summary -->
            <div class="bg-f5-blue-950 border border-zinc-900 p-6 rounded-2xl flex flex-col gap-4">
                <span class="text-xs font-mono font-bold text-zinc-500 uppercase">Resumo do Pedido</span>
                <div class="flex flex-col gap-1 border-b border-zinc-900 pb-4">
                    <strong class="text-lg text-white"><?php echo esc_html($selected_plan['name']); ?></strong>
                    <span class="text-2xl font-black text-f5-red">R$ <?php echo number_format($selected_plan['price'], 2, ',', '.'); ?> <span class="text-xs text-zinc-500 font-normal">/mês</span></span>
                </div>

                <!-- Coupon Box -->
                <div class="flex flex-col gap-2 pt-2">
                    <span class="text-xs font-mono text-zinc-400">Possui um cupom de desconto?</span>
                    <div class="flex gap-2">
                        <input type="text" id="f5tv-coupon-code" placeholder="CÓDIGO" class="w-full bg-f5-blue-900 border border-zinc-800 rounded-lg px-3 py-2 text-xs text-white uppercase outline-none focus:border-f5-red">
                        <button type="button" id="f5tv-coupon-btn" class="bg-zinc-800 hover:bg-zinc-700 text-white px-3 py-2 rounded-lg text-xs font-mono font-bold">Aplicar</button>
                    </div>
                    <div id="f5tv-coupon-msg" class="text-[11px] font-mono"></div>
                </div>
            </div>

            <!-- Payment details -->
            <div class="md:col-span-2 bg-f5-blue-950 border border-zinc-900 p-6 md:p-8 rounded-2xl flex flex-col gap-6">
                <span class="text-xs font-mono font-bold text-zinc-500 uppercase">Forma de Pagamento</span>
                
                <div class="grid grid-cols-3 gap-3">
                    <button type="button" class="f5tv-pay-tab bg-f5-blue-900 border border-f5-red text-white py-3 rounded-xl font-mono text-xs font-bold uppercase" data-method="card">
                        Cartão
                    </button>
                    <button type="button" class="f5tv-pay-tab bg-f5-blue-950 border border-zinc-900 text-zinc-400 hover:text-white py-3 rounded-xl font-mono text-xs font-bold uppercase" data-method="pix">
                        Pix
                    </button>
                    <button type="button" class="f5tv-pay-tab bg-f5-blue-950 border border-zinc-900 text-zinc-400 hover:text-white py-3 rounded-xl font-mono text-xs font-bold uppercase" data-method="boleto">
                        Boleto
                    </button>
                </div>

                <form id="f5tv-checkout-form" class="flex flex-col gap-4">
                    <div id="f5tv-pay-card-fields" class="flex flex-col gap-3">
                        <input type="text" placeholder="Nome impresso no cartão" required class="bg-f5-blue-900 border border-zinc-800 rounded-xl px-4 py-3 text-xs text-white outline-none focus:border-f5-red">
                        <input type="text" placeholder="Número do Cartão (0000 0000 0000 0000)" required class="bg-f5-blue-900 border border-zinc-800 rounded-xl px-4 py-3 text-xs text-white outline-none focus:border-f5-red">
                        <div class="grid grid-cols-2 gap-3">
                            <input type="text" placeholder="MM/AA" required class="bg-f5-blue-900 border border-zinc-800 rounded-xl px-4 py-3 text-xs text-white outline-none focus:border-f5-red">
                            <input type="text" placeholder="CVV" required class="bg-f5-blue-900 border border-zinc-800 rounded-xl px-4 py-3 text-xs text-white outline-none focus:border-f5-red">
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-f5-red hover:bg-f5-red-700 text-white font-mono font-bold text-xs uppercase tracking-wider py-4 rounded-xl transition shadow-lg cursor-pointer mt-2">
                        Confirmar e Ativar Assinatura
                    </button>
                </form>
                <div id="f5tv-checkout-msg" class="text-xs font-mono text-center"></div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const couponBtn = document.getElementById('f5tv-coupon-btn');
    const couponInput = document.getElementById('f5tv-coupon-code');
    const couponMsg = document.getElementById('f5tv-coupon-msg');
    const form = document.getElementById('f5tv-checkout-form');
    const msg = document.getElementById('f5tv-checkout-msg');

    couponBtn?.addEventListener('click', () => {
        const val = couponInput ? couponInput.value.trim().toUpperCase() : '';
        if (val === 'F510' || val === 'PROMO20') {
            couponMsg.className = 'text-[11px] font-mono text-green-400';
            couponMsg.textContent = 'Cupom de 20% aplicado com sucesso!';
        } else if (val) {
            couponMsg.className = 'text-[11px] font-mono text-red-500';
            couponMsg.textContent = 'Cupom inválido ou expirado.';
        }
    });

    form?.addEventListener('submit', (e) => {
        e.preventDefault();
        msg.className = 'text-xs font-mono text-green-400 text-center mt-2';
        msg.textContent = 'Pagamento aprovado! Redirecionando para a área do assinante...';
        setTimeout(() => {
            window.location.href = '/area-do-assinante/';
        }, 1500);
    });
});
</script>

<?php get_footer(); ?>
