<?php
/**
 * Main template file for F5TV Theme
 * Landing page matching React design
 */

get_header();
?>

<?php
// Hero section
$hero_content = get_posts([
    'post_type' => 'f5tv_conteudo',
    'posts_per_page' => 1,
    'meta_key' => 'is_featured',
    'meta_value' => '1',
]);

$hero_bg = '';
$hero_title = 'Uma nova forma de<br><span class="text-f5-red italic whitespace-nowrap">fazer televisão.</span>';
$hero_desc = 'A F5 TV Streaming nasce para marcar uma nova etapa na televisão em Portugal. Escolha o que quer ver, quando quer ver e em qualquer ecrã.';

if (!empty($hero_content)) {
    $hero = $hero_content[0];
    $hero_title = get_the_title($hero);
    $hero_desc = get_field('short_description', $hero->ID) ?: get_the_excerpt($hero);
    $hero_bg = get_field('banner_url', $hero->ID) ?: get_the_post_thumbnail_url($hero->ID, 'full');
}
?>

<section id="hero" class="relative h-auto min-h-[560px] lg:h-[560px] px-5 sm:px-8 pt-8 lg:pt-12 flex flex-col justify-end pb-10 lg:pb-16 overflow-hidden border-b border-white/5">
    <?php if ($hero_bg): ?>
        <div class="absolute inset-0 bg-cover bg-center opacity-65 grayscale-[0.25]" style="background-image: url('<?php echo esc_url($hero_bg); ?>')"></div>
    <?php else: ?>
        <div class="absolute inset-0 bg-gradient-to-b from-f5-blue to-black"></div>
    <?php endif; ?>
    <div class="absolute inset-0 bg-gradient-to-r from-[#071a33] via-[#071a33]/80 to-transparent z-10"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-[#071a33] to-transparent z-10"></div>

    <div class="max-w-4xl mx-auto w-full relative z-20 flex flex-col items-start gap-3 lg:gap-4 px-0 sm:px-4">
        <div class="inline-flex items-center gap-2 bg-f5-red px-2.5 py-0.5 text-[10px] font-mono font-bold rounded uppercase tracking-widest">
            <span>SÉRIE ORIGINAL F5</span>
        </div>

        <h1 class="text-4xl sm:text-6xl md:text-7xl font-black tracking-tighter leading-none text-white max-w-3xl">
            <?php echo $hero_title; ?>
        </h1>

        <p class="text-white/70 text-sm md:text-lg max-w-2xl leading-relaxed font-normal">
            <?php echo esc_html($hero_desc); ?>
        </p>

        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 w-full h-fit max-w-md mt-2 sm:mt-4">
            <a href="<?php echo esc_url(home_url('/planos/')); ?>" class="px-5 sm:px-8 py-3 sm:py-3.5 rounded bg-white text-black font-bold flex items-center justify-center gap-2 hover:bg-f5-red hover:text-white transition-colors text-sm uppercase tracking-wider">
                <span>Conteúdos em breve</span>
            </a>
            <a href="<?php echo esc_url(home_url('/catalogo/')); ?>" class="bg-white/10 backdrop-blur-md text-white border border-white/20 px-5 sm:px-8 py-3 sm:py-3.5 rounded font-bold flex items-center justify-center gap-2 hover:bg-white/20 transition text-sm uppercase tracking-wider">
                <span>Explorar a Programação</span>
            </a>
        </div>

        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[9px] sm:text-[10px] text-white/30 uppercase tracking-[0.16em] sm:tracking-[0.2em] font-medium font-mono mt-6 sm:mt-8">
            <span>&#10003; CANCELAMENTO 100% ONLINE</span>
            <span>&#8226;</span>
            <span>&#10003; FILMES FILTRADOS EM 4K</span>
        </div>
    </div>
</section>

<?php
// Content preview section
$contents = get_posts([
    'post_type' => 'f5tv_conteudo',
    'posts_per_page' => 10,
    'post_status' => 'publish',
]);
?>

<section id="conteudo-previa" class="py-20 px-4 sm:px-8 bg-f5-blue border-b border-white/5">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-12">
            <div>
                <span class="text-[10px] font-mono font-bold tracking-[0.2em] text-f5-red uppercase">NO CATÁLOGO</span>
                <h2 class="text-3xl md:text-5xl font-black tracking-tight text-white mt-2">Conteúdo que encontra o seu público</h2>
            </div>
            <a href="<?php echo esc_url(home_url('/catalogo/')); ?>" class="text-sm font-semibold text-f5-red hover:text-f5-red flex items-center gap-2 transition-colors uppercase tracking-wider font-mono text-xs">
                <span>Ver catálogo completo</span>
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <?php foreach ($contents as $item): ?>
                <?php
                $cover = get_field('cover_url', $item->ID) ?: get_the_post_thumbnail_url($item->ID, 'medium');
                $genre = get_field('genre', $item->ID) ?: get_the_category_list(', ', '', $item->ID);
                $age_rating = get_field('age_rating', $item->ID) ?: 'Livre';
                $is_exclusive = get_field('is_exclusive', $item->ID);
                ?>
                <div class="group relative bg-f5-blue-950 border border-white/5 hover:border-f5-red rounded-lg overflow-hidden cursor-pointer hover:border-f5-red/50 transform transition-all duration-300 hover:scale-[1.03] shadow-2xl">
                    <div class="aspect-[3/4] relative w-full bg-f5-blue-900">
                        <?php if ($cover): ?>
                            <img src="<?php echo esc_url($cover); ?>" alt="<?php echo esc_attr(get_the_title($item)); ?>" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity duration-300">
                        <?php endif; ?>
                        <div class="absolute top-2.5 right-2.5 bg-f5-blue/90 text-[10px] font-mono font-bold text-f5-red px-2 py-0.5 rounded border border-white/5">
                            <?php echo esc_html($age_rating); ?>
                        </div>
                        <?php if ($is_exclusive): ?>
                            <div class="absolute bottom-2.5 left-2.5 bg-f5-red text-[10px] font-bold text-black px-2 py-0.5 rounded uppercase font-sans tracking-tight">
                                Exclusivo
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-4 bg-f5-blue-950 flex flex-col gap-1.5">
                        <span class="text-[9px] font-mono tracking-wider font-semibold uppercase text-white/40">
                            <?php echo esc_html($genre); ?>
                        </span>
                        <h3 class="font-bold text-sm text-white/90 line-clamp-1 group-hover:text-white transition-colors duration-250">
                            <?php echo esc_html(get_the_title($item)); ?>
                        </h3>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php
// Benefits section
$benefits = [
    ['icon' => 'Smartphone', 'title' => 'Assista onde quiser', 'desc' => 'Disponível em Celulares, Tablets, Smart TVs, Computadores e consoles de videogame sem custo extra.'],
    ['icon' => 'Sparkles', 'title' => 'Conteúdos próprios e parceiros', 'desc' => 'Formatos diferenciados, histórias, ideias e protagonistas que merecem ser vistos e ouvidos.'],
    ['icon' => 'Database', 'title' => 'Downloads sob demanda', 'desc' => 'Baixe episódios inteiros em segundos e assista no avião, metrô ou estrada mesmo sem internet.'],
    ['icon' => 'Tv', 'title' => 'Alta Fidelidade 4K / HDR', 'desc' => 'Assista seus documentários favoritos com cores calibradas de cinema e som Dolby Atmos imersivo.'],
    ['icon' => 'Users', 'title' => 'Multi-perfil Familiar', 'desc' => 'Crie perfis independentes para cada membro da família, incluindo um perfil Kids totalmente vigiado.'],
    ['icon' => 'ShieldCheck', 'title' => 'Sem taxas de cancelamento', 'desc' => 'Assine mensalmente, sem carência ou multas rescisórias. Cancele online em dois cliques a qualquer segundo.'],
];
?>

<section id="beneficios" class="py-24 px-4 sm:px-8 bg-f5-blue border-b border-white/5">
    <div class="max-w-7xl mx-auto">
        <div class="text-left max-w-2xl mb-20 flex flex-col gap-3">
            <span class="text-[10px] font-mono font-bold tracking-[0.2em] text-f5-red uppercase">BENEFÍCIOS EXCLUSIVOS</span>
            <h2 class="text-4xl sm:text-6xl font-black tracking-tighter text-white leading-none">Televisão para um novo tempo</h2>
            <p class="text-white/60 text-base font-normal mt-2 leading-relaxed">Uma experiência mais flexível, contemporânea e conectada com o seu tempo, com conteúdos próprios e parceiros.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($benefits as $b): ?>
                <div class="p-8 bg-f5-blue-950 border border-white/5 hover:border-white/10 rounded-xl transition-all duration-300 flex flex-col gap-4 group">
                    <div class="p-3 bg-f5-blue w-fit rounded-lg border border-white/5 group-hover:bg-f5-red/10 group-hover:border-f5-red/30 transition duration-350">
                        <span class="text-f5-red text-lg"><?php echo esc_html($b['icon']); ?></span>
                    </div>
                    <h3 class="text-xl font-bold text-white group-hover:text-f5-red transition duration-200"><?php echo esc_html($b['title']); ?></h3>
                    <p class="text-sm text-white/60 leading-relaxed font-normal"><?php echo esc_html($b['desc']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php
// Plans section
$plans = get_posts([
    'post_type' => 'f5tv_plano',
    'posts_per_page' => 3,
    'post_status' => 'publish',
]);
?>

<section id="planos" class="py-24 px-4 sm:px-8 bg-f5-blue border-b border-white/5">
    <div class="max-w-7xl mx-auto">
        <div class="text-left max-w-2xl mb-18 flex flex-col gap-3">
            <span class="text-[10px] font-mono font-bold tracking-[0.2em] text-f5-red uppercase">ASSINATURAS EM BREVE</span>
            <h2 class="text-4xl sm:text-6xl font-black tracking-tighter text-white leading-none">Conteúdos por assinatura disponíveis em breve!</h2>
            <p class="text-white/60 text-base font-normal mt-2">Estamos a preparar novas experiências de conteúdo para a F5 TV.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl">
            <?php foreach ($plans as $plan): ?>
                <?php
                $price = get_field('price', $plan->ID) ?: 0;
                $features = get_field('features', $plan->ID) ?: [];
                $is_premium = get_field('is_premium', $plan->ID);
                $plan_id = get_post_field('post_name', $plan->ID);
                ?>
                <div class="flex flex-col rounded-xl relative border overflow-hidden <?php echo $is_premium ? 'bg-gradient-to-b from-[#100c0c] to-[#050505] border-f5-red shadow-2xl' : 'bg-f5-blue-950 border-white/5'; ?>">
                    <?php if ($is_premium): ?>
                        <div class="absolute top-0 right-0 left-0 bg-f5-red text-white text-[9px] font-bold text-center py-1.5 uppercase tracking-widest font-mono">
                            RECOMENDADO / MAIOR QUALIDADE
                        </div>
                    <?php endif; ?>

                    <div class="<?php echo $is_premium ? 'pt-10' : ''; ?> p-8 flex flex-col gap-5 border-b border-white/5">
                        <span class="text-xs font-mono tracking-widest font-bold uppercase text-white/40"><?php echo esc_html(get_the_title($plan)); ?></span>
                        <p class="text-sm font-semibold text-f5-red">Conteúdos por assinatura disponíveis em breve!</p>
                        <span class="w-full font-bold px-4 py-3.5 rounded text-xs text-center uppercase font-mono flex items-center justify-center gap-2 bg-white/10 text-white/60 border border-white/10">
                            Assinatura em Breve
                        </span>
                    </div>

                    <div class="flex-1 p-8 flex flex-col gap-4 bg-black/20">
                        <?php foreach ($features as $feat): ?>
                            <div class="flex gap-3 items-start text-xs text-white/80">
                                <span class="text-emerald-500 shrink-0 mt-0.5">&#10003;</span>
                                <span class="leading-relaxed font-normal"><?php echo esc_html($feat); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php
// FAQ section
$faq_items = [
    ['q' => 'O que é a F5 TV?', 'a' => 'A F5 TV é uma plataforma de streaming premium nacional controlada pela rede F5 de emissoras. Ela combina transmissões de jornalismo investigativo ao vivo com um catálogo rico sob demanda.'],
    ['q' => 'Como funciona o período de cancelamento?', 'a' => 'Nossa política de assinatura é 100% transparente. Não há contratos de fidelidade. Você pode cancelar a sua assinatura de forma instantânea diretamente na seção "Minha Conta".'],
    ['q' => 'Quais aparelhos são compatíveis?', 'a' => 'Você pode sintonizar a F5 TV em computadores via navegador web, smart TVs modernas, dispositivos de streaming como Chromecast e Apple TV, além de smartphones e tablets.'],
    ['q' => 'O Plano Família suporta visualização em telas simultâneas?', 'a' => 'Sim! Com o Plano Família você pode assistir em até 3 telas simultaneamente em altíssima qualidade Full HD.'],
];
?>

<section id="faq" class="py-24 px-4 sm:px-8 bg-f5-blue border-b border-white/5">
    <div class="max-w-3xl mx-auto">
        <span class="text-[10px] font-mono font-bold tracking-[0.2em] text-f5-red uppercase">PERGUNTAS FREQUENTES</span>
        <h2 class="text-3xl md:text-5xl font-black tracking-tighter text-white mt-2 mb-12">Dúvidas?</h2>

        <div class="flex flex-col gap-4">
            <?php foreach ($faq_items as $faq): ?>
                <div class="bg-f5-blue-950 border border-white/5 rounded-xl p-6">
                    <h3 class="font-bold text-white mb-2"><?php echo esc_html($faq['q']); ?></h3>
                    <p class="text-sm text-white/60 leading-relaxed"><?php echo esc_html($faq['a']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>
