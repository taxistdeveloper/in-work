<?php
/**
 * Герой для страниц помощи и юридических документов.
 * Перед подключением задайте: $staticHeroTitle, $staticHeroSubtitle (опц.), $staticHeroIcon ('help'|'shield'|'doc')
 */
$icon = $staticHeroIcon ?? 'help';
$icons = [
    'help' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    'shield' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
    'doc' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
];
$path = $icons[$icon] ?? $icons['help'];
$subtitle = $staticHeroSubtitle ?? '';
?>
<section class="static-page-hero relative bg-gradient-to-br from-gray-900 via-gray-800 to-brand-900 text-white">
    <div class="absolute inset-0 opacity-30">
        <div class="absolute top-0 left-1/4 w-72 h-72 bg-brand-400 rounded-full filter blur-3xl"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-emerald-500/80 rounded-full filter blur-3xl"></div>
    </div>
    <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-24 md:pt-14 md:pb-28">
        <nav class="text-sm text-white/65 mb-6 fade-in" aria-label="Навигация">
            <a href="<?= url('/') ?>" class="hover:text-white transition">Главная</a>
            <span class="mx-2 text-white/40">/</span>
            <span class="text-white/95 font-medium"><?= e($staticHeroTitle) ?></span>
        </nav>
        <div class="flex flex-col sm:flex-row sm:items-start gap-5">
            <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center shadow-lg">
                <svg class="w-8 h-8 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="<?= $path ?>"/>
                </svg>
            </div>
            <div class="min-w-0">
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-white leading-tight"><?= e($staticHeroTitle) ?></h1>
                <?php if ($subtitle !== ''): ?>
                <p class="mt-3 text-lg text-gray-300 leading-relaxed max-w-xl"><?= e($staticHeroSubtitle) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
