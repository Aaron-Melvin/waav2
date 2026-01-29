<?php
    $location = $this->location;
?>

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="<?php echo e(route('admin.locations.index')); ?>" wire:navigate>
                Locations
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item><?php echo e($location->name); ?></flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <flux:heading size="lg"><?php echo e($location->name); ?></flux:heading>
        <flux:text>Address details, coverage, and blackout scheduling.</flux:text>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <flux:card class="space-y-4 lg:col-span-2">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Partner</flux:text>
                    <?php if($location->partner): ?>
                        <flux:link href="<?php echo e(route('admin.partners.show', $location->partner)); ?>" wire:navigate>
                            <?php echo e($location->partner->name); ?>

                        </flux:link>
                    <?php else: ?>
                        <flux:text>—</flux:text>
                    <?php endif; ?>
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Status</flux:text>
                    <flux:badge :color="$this->statusColor($location->status)">
                        <?php echo e(ucfirst($location->status)); ?>

                    </flux:badge>
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Address</flux:text>
                    <flux:text>
                        <?php echo e($location->address_line_1 ?? '—'); ?>

                        <?php if($location->address_line_2): ?>
                            <br /><?php echo e($location->address_line_2); ?>

                        <?php endif; ?>
                    </flux:text>
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">City / Region</flux:text>
                    <flux:text><?php echo e($location->city ?? '—'); ?>, <?php echo e($location->region ?? '—'); ?></flux:text>
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Postal / Country</flux:text>
                    <flux:text><?php echo e($location->postal_code ?? '—'); ?> · <?php echo e($location->country_code ?? '—'); ?></flux:text>
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Timezone</flux:text>
                    <flux:text><?php echo e($location->timezone ?? '—'); ?></flux:text>
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Coordinates</flux:text>
                    <flux:text><?php echo e($location->latitude ?? '—'); ?>, <?php echo e($location->longitude ?? '—'); ?></flux:text>
                </div>
            </div>
        </flux:card>

        <flux:card class="space-y-4">
            <flux:heading size="sm">Coverage</flux:heading>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <flux:text>Products</flux:text>
                    <flux:badge color="zinc"><?php echo e($location->products_count); ?></flux:badge>
                </div>
                <div class="flex items-center justify-between">
                    <flux:text>Blackouts</flux:text>
                    <flux:badge color="zinc"><?php echo e($location->event_blackouts_count); ?></flux:badge>
                </div>
            </div>
        </flux:card>
    </div>

    <flux:card class="space-y-4">
        <div class="flex flex-col gap-1">
            <flux:heading size="sm">Products at this location</flux:heading>
            <flux:text class="text-sm">Listings associated with the location.</flux:text>
        </div>

        <flux:table>
            <thead data-flux-columns>
                <flux:table.row>
                    <flux:table.column>Product</flux:table.column>
                    <flux:table.column>Type</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Visibility</flux:table.column>
                    <flux:table.column align="end">Capacity</flux:table.column>
                </flux:table.row>
            </thead>
            <tbody data-flux-rows>
                <?php $__empty_1 = true; $__currentLoopData = $location->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <flux:table.row :key="$product->id">
                        <flux:table.cell variant="strong">
                            <flux:link href="<?php echo e(route('admin.products.show', $product)); ?>" wire:navigate>
                                <?php echo e($product->name); ?>

                            </flux:link>
                        </flux:table.cell>
                        <flux:table.cell><?php echo e(ucfirst($product->type)); ?></flux:table.cell>
                        <flux:table.cell><?php echo e(ucfirst($product->status)); ?></flux:table.cell>
                        <flux:table.cell><?php echo e(ucfirst($product->visibility)); ?></flux:table.cell>
                        <flux:table.cell align="end"><?php echo e($product->capacity_total ?? '—'); ?></flux:table.cell>
                    </flux:table.row>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <flux:table.row>
                        <flux:table.cell colspan="5">
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                No products assigned yet.
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                <?php endif; ?>
            </tbody>
        </flux:table>
    </flux:card>

    <flux:card class="space-y-4">
        <div class="flex flex-col gap-1">
            <flux:heading size="sm">Event blackouts</flux:heading>
            <flux:text class="text-sm">Date ranges blocked for events or inventory.</flux:text>
        </div>

        <flux:table>
            <thead data-flux-columns>
                <flux:table.row>
                    <flux:table.column>Dates</flux:table.column>
                    <flux:table.column>Product</flux:table.column>
                    <flux:table.column>Reason</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                </flux:table.row>
            </thead>
            <tbody data-flux-rows>
                <?php $__empty_1 = true; $__currentLoopData = $location->eventBlackouts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $blackout): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <flux:table.row :key="$blackout->id">
                        <flux:table.cell>
                            <?php echo e($blackout->starts_at?->format('M d, Y') ?? '—'); ?>

                            <?php if($blackout->ends_at): ?>
                                – <?php echo e($blackout->ends_at?->format('M d, Y')); ?>

                            <?php endif; ?>
                        </flux:table.cell>
                        <flux:table.cell><?php echo e($blackout->product?->name ?? '—'); ?></flux:table.cell>
                        <flux:table.cell><?php echo e($blackout->reason ?? '—'); ?></flux:table.cell>
                        <flux:table.cell><?php echo e(ucfirst($blackout->status ?? '—')); ?></flux:table.cell>
                    </flux:table.row>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <flux:table.row>
                        <flux:table.cell colspan="4">
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                No blackouts scheduled.
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                <?php endif; ?>
            </tbody>
        </flux:table>
    </flux:card>
</div><?php /**PATH /home/kevin/Projects/waav2/storage/framework/views/livewire-testing/views/0aeb4aee.blade.php ENDPATH**/ ?>