<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <?php echo $__env->make('partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </head>
    <body class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-amber-50 text-slate-950 antialiased dark:from-slate-950 dark:via-slate-900 dark:to-slate-900 dark:text-slate-100">
        <div class="relative">
            <header class="relative z-10">
                <div class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-6">
                    <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-3" wire:navigate>
                        <span class="flex size-11 items-center justify-center rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/80 dark:bg-slate-900 dark:ring-slate-700/70">
                            <?php if (isset($component)) { $__componentOriginal159d6670770cb479b1921cea6416c26c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal159d6670770cb479b1921cea6416c26c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-logo-icon','data' => ['class' => 'size-7 text-slate-900 dark:text-white']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-logo-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-7 text-slate-900 dark:text-white']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal159d6670770cb479b1921cea6416c26c)): ?>
<?php $attributes = $__attributesOriginal159d6670770cb479b1921cea6416c26c; ?>
<?php unset($__attributesOriginal159d6670770cb479b1921cea6416c26c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal159d6670770cb479b1921cea6416c26c)): ?>
<?php $component = $__componentOriginal159d6670770cb479b1921cea6416c26c; ?>
<?php unset($__componentOriginal159d6670770cb479b1921cea6416c26c); ?>
<?php endif; ?>
                        </span>
                        <span class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-600 dark:text-slate-300">
                            Wild Atlantic Adventures
                        </span>
                    </a>
                    <nav class="hidden items-center gap-6 text-sm font-medium text-slate-600 dark:text-slate-300 md:flex">
                        <flux:link href="<?php echo e(route('front.search')); ?>" wire:navigate>Search</flux:link>
                        <flux:link href="<?php echo e(route('home')); ?>" wire:navigate>Home</flux:link>
                    </nav>
                </div>
            </header>

            <main class="relative mx-auto w-full max-w-6xl px-6 pb-16">
                <?php echo e($slot); ?>

            </main>

            <footer class="border-t border-slate-200/70 py-10 text-center text-xs text-slate-500 dark:border-slate-700/60 dark:text-slate-400">
                <span>Handcrafted coastal adventures along Ireland’s Wild Atlantic Way.</span>
            </footer>
        </div>
        @fluxScripts
    </body>
</html>
<?php /**PATH /home/kevin/Projects/waav2/resources/views/layouts/front.blade.php ENDPATH**/ ?>