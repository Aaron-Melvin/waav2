<?php
    $product = $this->product;
?>

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="<?php echo e(route('admin.products.index')); ?>" wire:navigate>
                Products
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item><?php echo e($product->name); ?></flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <flux:heading size="lg"><?php echo e($product->name); ?></flux:heading>
        <flux:text>Catalog details, rate plans, and media coverage.</flux:text>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <flux:card class="space-y-4 lg:col-span-2">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Type</flux:text>
                    <flux:text><?php echo e(ucfirst($product->type)); ?></flux:text>
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Status</flux:text>
                    <flux:badge :color="$this->statusColor($product->status)">
                        <?php echo e(ucfirst($product->status)); ?>

                    </flux:badge>
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Visibility</flux:text>
                    <flux:badge :color="$this->visibilityColor($product->visibility)">
                        <?php echo e(ucfirst($product->visibility)); ?>

                    </flux:badge>
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Partner</flux:text>
                    <?php if($product->partner): ?>
                        <flux:link href="<?php echo e(route('admin.partners.show', $product->partner)); ?>" wire:navigate>
                            <?php echo e($product->partner->name); ?>

                        </flux:link>
                    <?php else: ?>
                        <flux:text>—</flux:text>
                    <?php endif; ?>
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Location</flux:text>
                    <?php if($product->location): ?>
                        <flux:link href="<?php echo e(route('admin.locations.show', $product->location)); ?>" wire:navigate>
                            <?php echo e($product->location->name); ?>

                        </flux:link>
                    <?php else: ?>
                        <flux:text>—</flux:text>
                    <?php endif; ?>
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Capacity</flux:text>
                    <flux:text><?php echo e($product->capacity_total ?? '—'); ?></flux:text>
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Currency</flux:text>
                    <flux:text><?php echo e($product->default_currency ?? '—'); ?></flux:text>
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Slug</flux:text>
                    <flux:text><?php echo e($product->slug); ?></flux:text>
                </div>
            </div>

            <div class="h-px bg-zinc-200 dark:bg-zinc-700"></div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Lead time</flux:text>
                    <flux:text><?php echo e($product->lead_time_minutes ? $product->lead_time_minutes.' min' : '—'); ?></flux:text>
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Cutoff</flux:text>
                    <flux:text><?php echo e($product->cutoff_minutes ? $product->cutoff_minutes.' min' : '—'); ?></flux:text>
                </div>
            </div>

            <?php if($product->description): ?>
                <div class="h-px bg-zinc-200 dark:bg-zinc-700"></div>
                <div class="space-y-2">
                    <flux:heading size="sm">Description</flux:heading>
                    <flux:text><?php echo e($product->description); ?></flux:text>
                </div>
            <?php endif; ?>
        </flux:card>

        <flux:card class="space-y-4">
            <flux:heading size="sm">Coverage</flux:heading>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <flux:text>Event series</flux:text>
                    <flux:badge color="zinc"><?php echo e($product->event_series_count); ?></flux:badge>
                </div>
                <div class="flex items-center justify-between">
                    <flux:text>Events</flux:text>
                    <flux:badge color="zinc"><?php echo e($product->events_count); ?></flux:badge>
                </div>
                <div class="flex items-center justify-between">
                    <flux:text>Units</flux:text>
                    <flux:badge color="zinc"><?php echo e($product->units_count); ?></flux:badge>
                </div>
                <div class="flex items-center justify-between">
                    <flux:text>Rate plans</flux:text>
                    <flux:badge color="zinc"><?php echo e($product->rate_plans_count); ?></flux:badge>
                </div>
                <div class="flex items-center justify-between">
                    <flux:text>Media assets</flux:text>
                    <flux:badge color="zinc"><?php echo e($product->media_count); ?></flux:badge>
                </div>
                <div class="flex items-center justify-between">
                    <flux:text>Eligibility rules</flux:text>
                    <flux:badge color="zinc"><?php echo e($product->eligibility_rules_count); ?></flux:badge>
                </div>
            </div>
        </flux:card>
    </div>

    <flux:card class="space-y-4">
        <div class="flex flex-col gap-1">
            <flux:heading size="sm">Rate plans</flux:heading>
            <flux:text class="text-sm">Pricing configurations linked to this product.</flux:text>
        </div>

        <flux:table>
            <thead data-flux-columns>
                <flux:table.row>
                    <flux:table.column>Plan</flux:table.column>
                    <flux:table.column>Code</flux:table.column>
                    <flux:table.column>Model</flux:table.column>
                    <flux:table.column>Currency</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                </flux:table.row>
            </thead>
            <tbody data-flux-rows>
                <?php $__empty_1 = true; $__currentLoopData = $product->ratePlans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ratePlan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <flux:table.row :key="$ratePlan->id">
                        <flux:table.cell variant="strong"><?php echo e($ratePlan->name); ?></flux:table.cell>
                        <flux:table.cell><?php echo e($ratePlan->code ?? '—'); ?></flux:table.cell>
                        <flux:table.cell><?php echo e(ucfirst($ratePlan->pricing_model ?? '—')); ?></flux:table.cell>
                        <flux:table.cell><?php echo e($ratePlan->currency ?? '—'); ?></flux:table.cell>
                        <flux:table.cell><?php echo e(ucfirst($ratePlan->status ?? '—')); ?></flux:table.cell>
                    </flux:table.row>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <flux:table.row>
                        <flux:table.cell colspan="5">
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                No rate plans configured yet.
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                <?php endif; ?>
            </tbody>
        </flux:table>
    </flux:card>

    <flux:card class="space-y-4">
        <div class="flex flex-col gap-1">
            <flux:heading size="sm">Units</flux:heading>
            <flux:text class="text-sm">Accommodation units connected to this product.</flux:text>
        </div>

        <flux:table>
            <thead data-flux-columns>
                <flux:table.row>
                    <flux:table.column>Name</flux:table.column>
                    <flux:table.column>Code</flux:table.column>
                    <flux:table.column align="end">Adults</flux:table.column>
                    <flux:table.column align="end">Children</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                </flux:table.row>
            </thead>
            <tbody data-flux-rows>
                <?php $__empty_1 = true; $__currentLoopData = $product->units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <flux:table.row :key="$unit->id">
                        <flux:table.cell variant="strong"><?php echo e($unit->name); ?></flux:table.cell>
                        <flux:table.cell><?php echo e($unit->code ?? '—'); ?></flux:table.cell>
                        <flux:table.cell align="end"><?php echo e($unit->occupancy_adults); ?></flux:table.cell>
                        <flux:table.cell align="end"><?php echo e($unit->occupancy_children); ?></flux:table.cell>
                        <flux:table.cell><?php echo e(ucfirst($unit->status ?? '—')); ?></flux:table.cell>
                    </flux:table.row>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <flux:table.row>
                        <flux:table.cell colspan="5">
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                No units configured for this product.
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                <?php endif; ?>
            </tbody>
        </flux:table>
    </flux:card>

    <flux:card class="space-y-4">
        <div class="flex flex-col gap-1">
            <flux:heading size="sm">Media library</flux:heading>
            <flux:text class="text-sm">Media assets published for this product.</flux:text>
        </div>

        <flux:table>
            <thead data-flux-columns>
                <flux:table.row>
                    <flux:table.column>URL</flux:table.column>
                    <flux:table.column>Kind</flux:table.column>
                    <flux:table.column align="end">Sort</flux:table.column>
                </flux:table.row>
            </thead>
            <tbody data-flux-rows>
                <?php $__empty_1 = true; $__currentLoopData = $product->media; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $media): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <flux:table.row :key="$media->id">
                        <flux:table.cell variant="strong"><?php echo e($media->url); ?></flux:table.cell>
                        <flux:table.cell><?php echo e(ucfirst($media->kind ?? '—')); ?></flux:table.cell>
                        <flux:table.cell align="end"><?php echo e($media->sort ?? '—'); ?></flux:table.cell>
                    </flux:table.row>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <flux:table.row>
                        <flux:table.cell colspan="3">
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                No media assets uploaded yet.
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                <?php endif; ?>
            </tbody>
        </flux:table>
    </flux:card>
</div><?php /**PATH /home/kevin/Projects/waav2/storage/framework/views/livewire-testing/views/437e4763.blade.php ENDPATH**/ ?>