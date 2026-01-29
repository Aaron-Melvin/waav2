<?php
    $blackout = $this->blackout;
    $products = $this->products;
    $locations = $this->locations;
?>

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="<?php echo e(route('partner.availability.blackouts.index')); ?>" wire:navigate>
                Event blackouts
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item>
                <?php echo e($blackout->product?->name ?? $blackout->location?->name ?? 'Blackout'); ?>

            </flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <flux:heading size="lg">Blackout details</flux:heading>
        <flux:text>Update blackout dates, scope, and status.</flux:text>
    </div>

    <flux:card class="space-y-4">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Status</flux:text>
                <flux:badge :color="$blackout->status === 'active' ? 'green' : 'zinc'">
                    <?php echo e(ucfirst($blackout->status)); ?>

                </flux:badge>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Starts</flux:text>
                <flux:text><?php echo e($blackout->starts_at?->format('M d, Y')); ?></flux:text>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Ends</flux:text>
                <flux:text><?php echo e($blackout->ends_at?->format('M d, Y')); ?></flux:text>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Scope</flux:text>
                <flux:text><?php echo e($blackout->product?->name ?? $blackout->location?->name ?? '—'); ?></flux:text>
            </div>
        </div>
    </flux:card>

    <flux:card class="space-y-5">
        <div class="flex flex-col gap-1">
            <flux:heading size="sm">Blackout settings</flux:heading>
            <flux:text class="text-sm">Update blackout details or remove it.</flux:text>
        </div>

        <?php if($savedMessage): ?>
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:border-emerald-500/40 dark:bg-emerald-500/10 dark:text-emerald-100">
                <?php echo e($savedMessage); ?>

            </div>
        <?php endif; ?>

        <form wire:submit="updateBlackout" class="space-y-6">
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
                    <flux:input wire:model.live="reason" />
                    <flux:error name="reason" />
                </flux:field>
            </div>

            <div class="flex flex-wrap justify-end gap-3">
                <flux:button type="button" variant="ghost" wire:click="deleteBlackout">
                    Delete blackout
                </flux:button>
                <flux:button type="submit" variant="primary">Save changes</flux:button>
            </div>
        </form>
    </flux:card>
</div><?php /**PATH /home/kevin/Projects/waav2/storage/framework/views/livewire-testing/views/9b61a9c5.blade.php ENDPATH**/ ?>