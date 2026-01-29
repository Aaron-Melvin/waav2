<?php
    $partners = $this->partners;
    $locations = $this->locations;
    $products = $this->products;
    $results = $this->results;
    $hasRange = $this->hasRange;
?>

<div class="flex flex-col gap-10">
    <section class="relative overflow-hidden rounded-3xl border border-slate-200/70 bg-white/80 p-8 shadow-sm backdrop-blur dark:border-slate-700/70 dark:bg-slate-900/70">
        <?php if (isset($component)) { $__componentOriginal1e4630c5daeca7ac226f30794c203a2d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1e4630c5daeca7ac226f30794c203a2d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.placeholder-pattern','data' => ['class' => 'pointer-events-none absolute inset-0 h-full w-full text-slate-200/70 dark:text-slate-700/60','stroke' => 'currentColor']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('placeholder-pattern'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'pointer-events-none absolute inset-0 h-full w-full text-slate-200/70 dark:text-slate-700/60','stroke' => 'currentColor']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1e4630c5daeca7ac226f30794c203a2d)): ?>
<?php $attributes = $__attributesOriginal1e4630c5daeca7ac226f30794c203a2d; ?>
<?php unset($__attributesOriginal1e4630c5daeca7ac226f30794c203a2d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1e4630c5daeca7ac226f30794c203a2d)): ?>
<?php $component = $__componentOriginal1e4630c5daeca7ac226f30794c203a2d; ?>
<?php unset($__componentOriginal1e4630c5daeca7ac226f30794c203a2d); ?>
<?php endif; ?>
        <div class="relative z-10 grid gap-8 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,0.8fr)]">
            <div class="flex flex-col gap-5">
                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-300">
                    <flux:badge color="blue">Coastal escapes</flux:badge>
                    <flux:badge color="amber">Live availability</flux:badge>
                </div>
                <flux:heading size="xl">Find your next adventure.</flux:heading>
                <flux:text class="text-base text-slate-600 dark:text-slate-300">
                    Browse real-time spaces across surf, hikes, paddling, and Atlantic stays. Lock in your dates and
                    discover local partners in minutes.
                </flux:text>
            </div>
            <div class="flex flex-col gap-4 rounded-2xl border border-slate-200/70 bg-white/90 p-6 shadow-sm dark:border-slate-700/70 dark:bg-slate-950/70">
                <flux:text class="text-sm uppercase tracking-[0.2em] text-slate-500 dark:text-slate-300">
                    What you can do
                </flux:text>
                <div class="grid gap-3 text-sm text-slate-600 dark:text-slate-300">
                    <div class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-emerald-500"></span>
                        <span>Filter by partner, product, and date range.</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-sky-500"></span>
                        <span>See live pricing, capacity, and availability.</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-amber-500"></span>
                        <span>Start a hold or booking from the results.</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <flux:card class="space-y-6">
        <div class="flex flex-col gap-2">
            <flux:heading size="lg">Search availability</flux:heading>
            <flux:text>Choose a partner and product to explore availability windows.</flux:text>
        </div>

        <form wire:submit="search" class="grid gap-4 lg:grid-cols-10">
            <flux:field class="lg:col-span-2">
                <flux:label>Partner</flux:label>
                <flux:select wire:model.live="partnerId">
                    <flux:select.option value="">All partners</flux:select.option>
                    <?php $__currentLoopData = $partners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <flux:select.option value="<?php echo e($partner->id); ?>" wire:key="partner-<?php echo e($partner->id); ?>">
                            <?php echo e($partner->name); ?>

                        </flux:select.option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </flux:select>
            </flux:field>

            <flux:field class="lg:col-span-2">
                <flux:label>Location</flux:label>
                <flux:select wire:model.live="locationId">
                    <flux:select.option value="">All locations</flux:select.option>
                    <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <flux:select.option value="<?php echo e($location->id); ?>" wire:key="location-<?php echo e($location->id); ?>">
                            <?php echo e($location->name); ?>

                        </flux:select.option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </flux:select>
            </flux:field>

            <flux:field class="lg:col-span-2">
                <flux:label>Type</flux:label>
                <flux:select wire:model.live="productType">
                    <flux:select.option value="">All types</flux:select.option>
                    <flux:select.option value="event">Event</flux:select.option>
                    <flux:select.option value="accommodation">Accommodation</flux:select.option>
                </flux:select>
            </flux:field>

            <flux:field class="lg:col-span-2">
                <flux:label>Product (optional)</flux:label>
                <flux:select wire:model.live="productId">
                    <flux:select.option value="">Select a product</flux:select.option>
                    <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <flux:select.option value="<?php echo e($product->id); ?>" wire:key="product-<?php echo e($product->id); ?>">
                            <?php echo e($product->name); ?>

                        </flux:select.option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <flux:select.option disabled>No products available</flux:select.option>
                    <?php endif; ?>
                </flux:select>
            </flux:field>

            <div class="lg:col-span-3 lg:row-start-2">
                <flux:date-picker
                    wire:model.live="dateRange"
                    mode="range"
                    label="Dates"
                    placeholder="Select dates"
                    with-inputs
                    with-presets
                    presets="today last7Days thisMonth"
                    min="today"
                    clearable
                />
            </div>

            <flux:field class="lg:row-start-2">
                <flux:label>Guests</flux:label>
                <flux:input type="number" min="1" wire:model.live="quantity" />
            </flux:field>

            <flux:field class="lg:row-start-2">
                <flux:label>Sort</flux:label>
                <flux:select wire:model.live="sort">
                    <flux:select.option value="date">Soonest first</flux:select.option>
                    <flux:select.option value="price_low">Lowest price</flux:select.option>
                    <flux:select.option value="price_high">Highest price</flux:select.option>
                    <flux:select.option value="availability">Most available</flux:select.option>
                </flux:select>
            </flux:field>

            <div class="lg:col-span-10">
                <flux:button
                    type="submit"
                    variant="primary"
                    class="w-full data-loading:pointer-events-none data-loading:opacity-60"
                >
                    <span class="in-data-loading:hidden">Search availability</span>
                    <span class="hidden in-data-loading:flex">Searching…</span>
                </flux:button>
            </div>
        </form>
    </flux:card>

    <section class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <flux:heading size="lg">Availability</flux:heading>
            <?php if($this->hasSearched && $results->isNotEmpty()): ?>
                <flux:text class="text-sm text-slate-500 dark:text-slate-300">
                    <?php echo e($results->count()); ?> options found
                </flux:text>
            <?php endif; ?>
        </div>

        <?php if($this->holdMessage): ?>
            <flux:callout icon="clock">
                <flux:callout.heading>Hold status</flux:callout.heading>
                <flux:callout.text>
                    <?php echo e($this->holdMessage); ?>

                    <?php if($this->lastHoldId): ?>
                        <flux:callout.link href="<?php echo e(route('front.booking.details', $this->lastHoldId)); ?>" wire:navigate>
                            Continue booking
                        </flux:callout.link>
                    <?php endif; ?>
                </flux:callout.text>
            </flux:callout>
        <?php endif; ?>

        <?php if(! $this->hasSearched): ?>
            <flux:card class="p-6 text-center">
                <flux:text class="text-sm text-slate-500 dark:text-slate-300">
                    Set your filters and run a search to see live availability.
                </flux:text>
            </flux:card>
        <?php elseif(! $hasRange): ?>
            <flux:card class="p-6 text-center">
                <flux:text class="text-sm text-slate-500 dark:text-slate-300">
                    Select a date range to view matching availability windows.
                </flux:text>
            </flux:card>
        <?php elseif($results->isEmpty()): ?>
            <flux:card class="p-6 text-center">
                <flux:text class="text-sm text-slate-500 dark:text-slate-300">
                    No availability matched those dates. Try adjusting your range or guest count.
                </flux:text>
            </flux:card>
        <?php else: ?>
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $product = $result->product;
                        $partner = $result->partner;
                        $location = $result->location;
                        $currency = strtoupper($result->currency ?? 'EUR');
                        $priceMin = $result->price_min ? number_format((float) $result->price_min, 2) : null;
                        $priceMax = $result->price_max ? number_format((float) $result->price_max, 2) : null;
                        $capacity = $result->capacity_available ?? null;
                    ?>
                    <flux:card class="flex h-full flex-col gap-4" wire:key="availability-<?php echo e($result->id); ?>">
                        <div class="flex items-start justify-between gap-3">
                            <div class="space-y-1">
                                <flux:heading size="lg"><?php echo e($product?->name ?? 'Experience'); ?></flux:heading>
                                <flux:text class="text-sm text-slate-500 dark:text-slate-300">
                                    <?php echo e($partner?->name ?? 'Partner'); ?>

                                </flux:text>
                            </div>
                            <flux:badge color="blue"><?php echo e(ucfirst($product?->type ?? 'event')); ?></flux:badge>
                        </div>

                        <div class="space-y-2 text-sm text-slate-600 dark:text-slate-300">
                            <div>
                                <span class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-slate-400">Dates</span>
                                <div class="font-medium text-slate-700 dark:text-slate-200">
                                    <?php echo e($result->starts_on?->format('M d, Y') ?? '—'); ?>

                                    <?php if($result->ends_on): ?>
                                        – <?php echo e($result->ends_on?->format('M d, Y')); ?>

                                    <?php endif; ?>
                                </div>
                            </div>
                            <div>
                                <span class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-slate-400">Location</span>
                                <div class="font-medium text-slate-700 dark:text-slate-200">
                                    <?php echo e($location?->name ?? 'Atlantic Coast'); ?>

                                    <?php if($location?->city): ?>
                                        · <?php echo e($location->city); ?>

                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="mt-auto flex items-end justify-between gap-4 border-t border-slate-200/70 pt-4 text-sm dark:border-slate-700/70">
                            <div>
                                <flux:text class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-slate-400">
                                    Starting from
                                </flux:text>
                                <div class="text-lg font-semibold text-slate-900 dark:text-white">
                                    <?php echo e($priceMin ? "{$currency} {$priceMin}" : 'Contact for price'); ?>

                                </div>
                                <?php if($priceMax && $priceMax !== $priceMin): ?>
                                    <flux:text class="text-xs text-slate-500 dark:text-slate-400">
                                        Up to <?php echo e($currency); ?> <?php echo e($priceMax); ?>

                                    </flux:text>
                                <?php endif; ?>
                            </div>
                            <div class="text-right">
                                <flux:text class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-slate-400">
                                    Spaces
                                </flux:text>
                                <div class="text-lg font-semibold text-slate-900 dark:text-white">
                                    <?php echo e($capacity ?? 'Open'); ?>

                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <flux:button
                                type="button"
                                variant="filled"
                                class="w-full data-loading:pointer-events-none data-loading:opacity-60"
                                wire:click="createHold('<?php echo e($result->id); ?>', true)"
                            >
                                Start booking
                            </flux:button>
                            <flux:button
                                type="button"
                                variant="ghost"
                                class="w-full data-loading:pointer-events-none data-loading:opacity-60"
                                wire:click="createHold('<?php echo e($result->id); ?>')"
                            >
                                Hold for 15 minutes
                            </flux:button>
                        </div>
                    </flux:card>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </section>
</div><?php /**PATH /home/kevin/Projects/waav2/storage/framework/views/livewire-testing/views/f5197895.blade.php ENDPATH**/ ?>