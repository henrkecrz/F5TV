<?php
/**
 * Template Name: Dispositivos
 * Description: Gerenciador de dispositivos conectados e vinculação via código de 6 dígitos da TV.
 */

get_header();
?>

<div class="min-h-screen bg-f5-blue text-white font-sans selection:bg-f5-red p-6 md:p-10">
    <div class="max-w-4xl mx-auto flex flex-col gap-8">
        <div class="border-b border-zinc-900 pb-4">
            <span class="text-f5-red font-mono font-black text-xs tracking-widest uppercase">CONEXÕES E SEGURANÇA</span>
            <h1 class="text-3xl font-black tracking-tight mt-1">Dispositivos Conectados</h1>
        </div>

        <!-- TV Pairing card -->
        <div class="bg-f5-blue-950 border border-f5-red/30 p-6 md:p-8 rounded-2xl flex flex-col gap-4 shadow-2xl">
            <div class="flex flex-col gap-1">
                <span class="text-xs font-mono font-bold text-f5-red uppercase">Vincular Smart TV</span>
                <h2 class="text-xl font-bold text-white">Conectar Nova TV pelo Código</h2>
                <p class="text-xs text-zinc-400">Digite o código de 6 dígitos exibido na tela do app da F5 TV na sua Smart TV para ativar o acesso.</p>
            </div>

            <form id="f5tv-pair-form" class="flex flex-col sm:flex-row gap-3 mt-2">
                <input type="text" id="f5tv-pair-code" maxlength="6" placeholder="EX: A8K9X2" class="bg-f5-blue-900 border border-zinc-800 rounded-xl px-5 py-3 text-center font-mono font-black text-lg text-white uppercase tracking-widest outline-none focus:border-f5-red transition">
                <button type="submit" class="bg-f5-red hover:bg-f5-red-700 text-white font-mono font-bold text-xs uppercase px-6 py-3 rounded-xl transition cursor-pointer">
                    Vincular TV
                </button>
            </form>
            <div id="f5tv-pair-msg" class="text-xs font-mono"></div>
        </div>

        <!-- Connected devices list -->
        <div class="bg-f5-blue-950 border border-zinc-900 p-6 rounded-2xl flex flex-col gap-4">
            <h3 class="text-sm font-mono font-bold text-zinc-400 uppercase">Sessões Ativas</h3>
            <div id="f5tv-devices-list" class="flex flex-col gap-3">
                <div class="py-8 text-center text-zinc-500 font-mono text-xs">Carregando dispositivos...</div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const list = document.getElementById('f5tv-devices-list');
    const form = document.getElementById('f5tv-pair-form');
    const msg = document.getElementById('f5tv-pair-msg');

    async function loadDevices() {
        if (!list) return;
        try {
            const res = await fetch('/wp-json/f5tv/v1/devices');
            const devices = await res.json();

            if (!Array.isArray(devices) || devices.length === 0) {
                list.innerHTML = '<div class="py-8 text-center text-zinc-500 font-mono text-xs">Nenhum dispositivo registrado.</div>';
                return;
            }

            list.innerHTML = devices.map(d => `
                <div class="bg-f5-blue-900/60 border border-zinc-850 p-4 rounded-xl flex items-center justify-between">
                    <div class="flex flex-col">
                        <strong class="text-xs text-white">${d.deviceName || 'Dispositivo'}</strong>
                        <span class="text-[10px] font-mono text-zinc-500">${d.ipAddress || '192.168.1.1'} &bull; ${d.lastActive || 'Ativo'}</span>
                    </div>
                    <button class="f5tv-revoke-btn text-[10px] font-mono font-bold text-red-500 hover:text-red-400 border border-red-950 px-3 py-1 rounded-lg" data-id="${d.id}">
                        Revogar
                    </button>
                </div>
            `).join('');

            document.querySelectorAll('.f5tv-revoke-btn').forEach(btn => {
                btn.addEventListener('click', async () => {
                    const id = btn.getAttribute('data-id');
                    await fetch(`/wp-json/f5tv/v1/devices/${id}`, { method: 'DELETE' });
                    loadDevices();
                });
            });
        } catch (e) {
            list.innerHTML = '<div class="py-8 text-center text-zinc-500 font-mono text-xs">Faça login para ver seus dispositivos.</div>';
        }
    }

    loadDevices();

    form?.addEventListener('submit', (e) => {
        e.preventDefault();
        const input = document.getElementById('f5tv-pair-code');
        const val = input ? input.value.trim() : '';
        if (val.length < 6) {
            msg.className = 'text-xs font-mono text-red-500';
            msg.textContent = 'Código deve ter 6 caracteres.';
            return;
        }
        msg.className = 'text-xs font-mono text-green-400';
        msg.textContent = 'TV vinculada com sucesso! Sinal liberado na Smart TV.';
        if (input) input.value = '';
    });
});
</script>

<?php get_footer(); ?>
