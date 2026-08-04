<?php
/**
 * Footer template for F5TV Theme
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
    </main>

    <footer class="mt-auto border-t border-white/5 bg-f5-blue py-12 px-4 sm:px-8">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="flex flex-col gap-4">
                <img src="<?php echo esc_url(F5TV_ASSETS_URI . '/images/f5tv-logo-neg.png'); ?>" alt="F5 TV" class="h-10 w-auto object-contain" width="180" height="60">
                <p class="text-white/50 text-xs leading-relaxed max-w-xs">
                    A plataforma premium de streaming e portal de conteúdos exclusivos da emissora F5 TV. Jornalismo tático, entretenimento, esportes e muito mais.
                </p>
            </div>

            <div class="flex flex-col gap-4">
                <h3 class="text-white font-mono tracking-[0.2em] uppercase text-[11px] font-bold">Navegação</h3>
                <ul class="flex flex-col gap-2.5 text-xs text-white/50">
                    <li><a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-white transition">Início</a></li>
                    <li><a href="<?php echo esc_url(home_url('/planos/')); ?>" class="hover:text-white transition">Planos</a></li>
                    <li><a href="<?php echo esc_url(home_url('/catalogo/')); ?>" class="hover:text-white transition">Catálogo</a></li>
                    <li><a href="<?php echo esc_url(home_url('/area-do-assinante/')); ?>" class="hover:text-white transition">Área do Assinante</a></li>
                </ul>
            </div>

            <div class="flex flex-col gap-4">
                <h3 class="text-white font-mono tracking-[0.2em] uppercase text-[11px] font-bold">Institucional</h3>
                <ul class="flex flex-col gap-2.5 text-xs text-white/50">
                    <li><a href="<?php echo esc_url(home_url('/sobre/')); ?>" class="hover:text-white transition">Sobre a F5 TV</a></li>
                    <li><a href="<?php echo esc_url(home_url('/contato/')); ?>" class="hover:text-white transition">Contato & Suporte</a></li>
                    <li><a href="<?php echo esc_url(home_url('/termos/')); ?>" class="hover:text-white transition">Termos de Serviço</a></li>
                    <li><a href="<?php echo esc_url(home_url('/privacidade/')); ?>" class="hover:text-white transition">Políticas de Privacidade</a></li>
                </ul>
            </div>

            <div class="flex flex-col gap-4">
                <h3 class="text-white font-mono tracking-[0.2em] uppercase text-[11px] font-bold">Suporte</h3>
                <p class="text-white/50 text-xs leading-relaxed">
                    Email: contato@f5tv.com.br<br>
                    Suporte: 24h para assinantes Premium
                </p>
            </div>
        </div>
        <div class="mt-8 flex items-center justify-center gap-x-3 text-center text-[clamp(9px,2.4vw,12px)] leading-none whitespace-nowrap">
            <span class="text-white/30 font-mono tracking-wider">
                &copy; <?php echo esc_html(date('Y')); ?> F5 TV Brasil. All rights reserved.
            </span>
            <span class="text-white/40">
                Desenvolvido por
                <a href="https://conectacomunicacao.pt" target="_blank" rel="noopener noreferrer" class="text-white/70 hover:text-f5-red transition">
                    Conecta Comunicação
                </a>
            </span>
        </div>
    </footer>
</div>

<?php wp_footer(); ?>
</body>
</html>
