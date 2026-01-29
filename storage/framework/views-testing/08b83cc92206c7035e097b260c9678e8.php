<?php
    $blackouts = $this->blackouts;
    $products = $this->products;
    $locations = $this->locations;
?>

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <flux:heading size="lg">Event blackouts</flux:heading>
        <flux:text>Pause availability across products or locations.</flux:text>
    </div>

    <flux:card class="space-y-5">
        <div class="flex flex-col gap-1">
            <flux:heading size="sm">Create blackout</flux:heading>
            <flux:text class="text-sm">Block a date range for a product or location.</flux:text>
        </div>

        <?php if($savedMessage): ?>
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:border-emerald-500/40 dark:bg-emerald-500/10 dark:text-emerald-100">
                <?php echo e($savedMessage); ?>

            </div>
        <?php endif; ?>

        <form wire:submit="createBlackout" class="space-y-6">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <flux:field>
                    <flux:label>Product</flux:label>
                    <flux:select wire:model.live="productId">
                        <flux:select.option value="">Select product</flux:select.option>
                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <flux:select.option value="<?php echo e($product->id); ?>">
                                <?php echo e($product->name); ?>

                            </flux:select.option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </flux:select>
                    <flux:error name="product_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Location</flux:label>
                    <flux:select wire:model.live="locationId">
                        <flux:select.option value="">Select location</flux:select.option>
                        <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <flux:select.option value="<?php echo e($location->id); ?>">
                                <?php echo e($location->name); ?>

                            </flux:select.option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </flux:select>
                    <flux:error name="location_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select wire:model.live="status">
                        <flux:select.option value="active">Active</flux:select.option>
                        <flux:select.option value="inactive">Inactive</flux:select.option>
                    </flux:select>
                    <flux:error name="status" />
                </flux:field>

                <flux:field>
                    <flux:label>Starts at</flux:label>
                    <flux:input type="date" wire:model.live="startsAt" />
                    <flux:error name="starts_at" />
                </flux:field>

                <flux:field>
                    <flux:label>Ends at</flux:label>
                    <flux:input type="date" wire:model.live="endsAt" />
                    <flux:error name="ends_at" />
                </flux:field>

                <flux:field class="sm:col-span-2 lg:col-span-3">
                    <flux:label>Reason</flux:label>
                    <flux:input wire:model.live="reason" placeholder="Maintenance, holiday, weather" />
                    <flux:error name="reason" />
                </flux:field>
            </div>

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">Create blackout</flux:button>
            </div>
        </form>
    </flux:card>

    <flux:card class="space-y-5">
        <div class="grid gap-4 lg:grid-cols-6">
            <flux:field class="lg:col-span-2">
                <flux:label>Search</flux:label>
                <flux:input wire:model.live="search" placeholder="Product or location" />
            </flux:field>

            <flux:field class="lg:col-span-2">
                <flux:label>Product</flux:label>
                <flux:select wire:model.live="filterProductId">
                    <flux:select.option value="">All products</flux:select.option>
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <flux:select.option value="<?php echo e($product->id); ?>">
                            <?php echo e($product->name); ?>

                        </flux:select.option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </flux:select>
            </flux:field>

            <flux:field class="lg:col-span-2">
                <flux:label>Location</flux:label>
                <flux:select wire:model.live="filterLocationId">
                    <flux:select.option value="">All locations</flux:select.option>
                    <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <flux:select.option value="<?php echo e($location->id); ?>">
                            <?php echo e($location->name); ?>

                        </flux:select.option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label>From</flux:label>
                <flux:input type="date" wire:model.live="filterDateFrom" />
            </flux:field>

            <flux:field>
                <flux:label>To</flux:label>
                <flux:input type="date" wire:model.live="filterDateTo" />
            </flux:field>

            <flux:field>
                <flux:label>Status</flux:label>
                <flux:select wire:model.live="filterStatus">
                    <flux:select.option value="all">All</flux:select.option>
                    <flux:select.option value="active">Active</flux:select.option>
                    <flux:select.option value="inactive">Inactive</flux:select.option>
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label>Per page</flux:label>
                <flux:select wire:model.live="perPage">
                    <flux:select.option value="15">15</flux:select.option>
                    <flux:select.option value="30">30</flux:select.option>
                    <flux:select.option value="50">50</flux:select.option>
                </flux:select>
            </flux:field>
        </div>

        <flux:table :paginate="$blackouts">
            <thead data-flux-columns>
                <flux:table.row>
                    <flux:table.column>Scope</flux:table.column>
                    <flux:table.column>Dates</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Reason</flux:table.column>
                </flux:table.row>
            </thead>
            <tbody data-flux-rows>
                <?php $__empty_1 = true; $__currentLoopData = $blackouts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $blackout): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <flux:table.row :key="$blackout->id">
                        <flux:table.cell variant="strong">
                            <flux:link href="<?php echo e(route('partner.availability.blackouts.show', $blackout)); ?>" wire:navigate>
                                <?php echo e($blackout->product?->name ?? $blackout->location?->name ?? 'Blackout'); ?>

                            </flux:link>
                        </flux:table.cell>
                        <flux:table.cell>
                            <?php echo e($blackout->starts_at?->format('M d, Y')); ?> - <?php echo e($blackout->ends_at?->format('M d, Y')); ?>

                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$blackout->status === 'active' ? 'green' : 'zinc'">
                                <?php echo e(ucfirst($blackout->status)); ?>

                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell><?php echo e($blackout->reason ?? '—'); ?></flux:table.cell>
                    </flux:table.row>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <flux:table.row>
                        <flux:table.cell colspan="4">
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                No blackouts match the current filters.
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                <?php endif; ?>
            </tbody>
        </flux:table>
    </flux:card>
</div><?php /**PATH /home/kevin/Projects/waav2/storage/framework/views/livewire-testing/views/b246fc39.blade.php ENDPATH**/ ?>