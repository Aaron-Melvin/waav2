<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="dark">
    <head>
        <?php echo $__env->make('partials.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <?php if (isset($component)) { $__componentOriginal7b17d80ff7900603fe9e5f0b453cc7c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7b17d80ff7900603fe9e5f0b453cc7c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-logo','data' => ['sidebar' => true,'href' => ''.e(route('dashboard')).'','wire:navigate' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['sidebar' => true,'href' => ''.e(route('dashboard')).'','wire:navigate' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7b17d80ff7900603fe9e5f0b453cc7c3)): ?>
<?php $attributes = $__attributesOriginal7b17d80ff7900603fe9e5f0b453cc7c3; ?>
<?php unset($__attributesOriginal7b17d80ff7900603fe9e5f0b453cc7c3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7b17d80ff7900603fe9e5f0b453cc7c3)): ?>
<?php $component = $__componentOriginal7b17d80ff7900603fe9e5f0b453cc7c3; ?>
<?php unset($__componentOriginal7b17d80ff7900603fe9e5f0b453cc7c3); ?>
<?php endif; ?>
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        <?php echo e(__('Dashboard')); ?>

                    </flux:sidebar.item>

                    <?php if (\Illuminate\Support\Facades\Blade::check('role', 'super-admin')): ?>
                        <flux:sidebar.item icon="users" :href="route('admin.partners.index')" :current="request()->routeIs('admin.partners.*')" wire:navigate>
                            <?php echo e(__('Partners')); ?>

                        </flux:sidebar.item>

                        <flux:sidebar.item icon="map-pin" :href="route('admin.locations.index')" :current="request()->routeIs('admin.locations.*')" wire:navigate>
                            <?php echo e(__('Locations')); ?>

                        </flux:sidebar.item>

                        <flux:sidebar.item icon="cube" :href="route('admin.products.index')" :current="request()->routeIs('admin.products.*')" wire:navigate>
                            <?php echo e(__('Products')); ?>

                        </flux:sidebar.item>

                        <flux:sidebar.item icon="calendar-days" :href="route('admin.bookings.index')" :current="request()->routeIs('admin.bookings.*')" wire:navigate>
                            <?php echo e(__('Bookings')); ?>

                        </flux:sidebar.item>

                        <flux:sidebar.item icon="currency-euro" :href="route('admin.payments.index')" :current="request()->routeIs('admin.payments.*')" wire:navigate>
                            <?php echo e(__('Payments')); ?>

                        </flux:sidebar.item>
                    <?php endif; ?>

                    <?php if (\Illuminate\Support\Facades\Blade::check('role', 'partner-admin|partner-staff')): ?>
                        <flux:sidebar.item icon="cube" :href="route('partner.catalog.products.index')" :current="request()->routeIs('partner.catalog.products.*')" wire:navigate>
                            <?php echo e(__('Catalog products')); ?>

                        </flux:sidebar.item>

                        <flux:sidebar.item icon="map-pin" :href="route('partner.catalog.locations.index')" :current="request()->routeIs('partner.catalog.locations.*')" wire:navigate>
                            <?php echo e(__('Catalog locations')); ?>

                        </flux:sidebar.item>

                        <flux:sidebar.item icon="calendar-days" :href="route('partner.availability.events.index')" :current="request()->routeIs('partner.availability.events.*')" wire:navigate>
                            <?php echo e(__('Event availability')); ?>

                        </flux:sidebar.item>

                        <flux:sidebar.item icon="calendar-days" :href="route('partner.availability.blackouts.index')" :current="request()->routeIs('partner.availability.blackouts.*')" wire:navigate>
                            <?php echo e(__('Event blackouts')); ?>

                        </flux:sidebar.item>

                        <flux:sidebar.item icon="shield-check" :href="route('partner.policies.cancellation.index')" :current="request()->routeIs('partner.policies.cancellation.*')" wire:navigate>
                            <?php echo e(__('Cancellation policies')); ?>

                        </flux:sidebar.item>

                        <flux:sidebar.item icon="currency-euro" :href="route('partner.policies.taxes.index')" :current="request()->routeIs('partner.policies.taxes.*')" wire:navigate>
                            <?php echo e(__('Taxes')); ?>

                        </flux:sidebar.item>

                        <flux:sidebar.item icon="receipt" :href="route('partner.policies.fees.index')" :current="request()->routeIs('partner.policies.fees.*')" wire:navigate>
                            <?php echo e(__('Fees')); ?>

                        </flux:sidebar.item>
                    <?php endif; ?>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                    <?php echo e(__('Repository')); ?>

                </flux:sidebar.item>

                <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                    <?php echo e(__('Documentation')); ?>

                </flux:sidebar.item>
            </flux:sidebar.nav>

            <?php if (isset($component)) { $__componentOriginalca54afb14f8d43d7f1acc5dbe6164a0a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalca54afb14f8d43d7f1acc5dbe6164a0a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.desktop-user-menu','data' => ['class' => 'hidden lg:block','name' => auth()->user()->name]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('desktop-user-menu'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'hidden lg:block','name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(auth()->user()->name)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalca54afb14f8d43d7f1acc5dbe6164a0a)): ?>
<?php $attributes = $__attributesOriginalca54afb14f8d43d7f1acc5dbe6164a0a; ?>
<?php unset($__attributesOriginalca54afb14f8d43d7f1acc5dbe6164a0a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalca54afb14f8d43d7f1acc5dbe6164a0a)): ?>
<?php $component = $__componentOriginalca54afb14f8d43d7f1acc5dbe6164a0a; ?>
<?php unset($__componentOriginalca54afb14f8d43d7f1acc5dbe6164a0a); ?>
<?php endif; ?>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate"><?php echo e(auth()->user()->name); ?></flux:heading>
                                    <flux:text class="truncate"><?php echo e(auth()->user()->email); ?></flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            <?php echo e(__('Settings')); ?>

                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="<?php echo e(route('logout')); ?>" class="w-full">
                        <?php echo csrf_field(); ?>
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            <?php echo e(__('Log Out')); ?>

                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        <?php echo e($slot); ?>


        @fluxScripts
    </body>
</html>
<?php /**PATH /home/kevin/Projects/waav2/resources/views/layouts/app/sidebar.blade.php ENDPATH**/ ?>