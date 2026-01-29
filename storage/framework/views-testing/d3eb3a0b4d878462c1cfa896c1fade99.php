<?php
    $locations = $this->locations;
?>

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <flux:heading size="lg">Locations</flux:heading>
        <flux:text>Browse partner locations and confirm regional coverage.</flux:text>
    </div>

    <flux:card class="space-y-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
            <flux:field class="flex-1">
                <flux:label>Search</flux:label>
                <flux:input
                    wire:model.live="search"
                    placeholder="Name, city, region, or partner"
                />
            </flux:field>

            <flux:field class="w-full lg:w-56">
                <flux:label>Status</flux:label>
                <flux:select wire:model.live="status">
                    <flux:select.option value="all">All</flux:select.option>
                    <flux:select.option value="active">Active</flux:select.option>
                    <flux:select.option value="inactive">Inactive</flux:select.option>
                </flux:select>
            </flux:field>

            <flux:field class="w-full lg:w-40">
                <flux:label>Per page</flux:label>
                <flux:select wire:model.live="perPage">
                    <flux:select.option value="15">15</flux:select.option>
                    <flux:select.option value="30">30</flux:select.option>
                    <flux:select.option value="50">50</flux:select.option>
                </flux:select>
            </flux:field>
        </div>

        <flux:table :paginate="$locations">
            <thead data-flux-columns>
                <flux:table.row>
                    <flux:table.column>Location</flux:table.column>
                    <flux:table.column>Partner</flux:table.column>
                    <flux:table.column>City</flux:table.column>
                    <flux:table.column>Region</flux:table.column>
                    <flux:table.column>Country</flux:table.column>
                    <flux:table.column>Timezone</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Created</flux:table.column>
                </flux:table.row>
            </thead>
            <tbody data-flux-rows>
                <?php $__empty_1 = true; $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <flux:table.row :key="$location->id">
                        <flux:table.cell variant="strong">
                            <flux:link href="<?php echo e(route('admin.locations.show', $location)); ?>" wire:navigate>
                                <?php echo e($location->name); ?>

                            </flux:link>
                        </flux:table.cell>
                        <flux:table.cell><?php echo e($location->partner?->name); ?></flux:table.cell>
                        <flux:table.cell><?php echo e($location->city ?? '—'); ?></flux:table.cell>
                        <flux:table.cell><?php echo e($location->region ?? '—'); ?></flux:table.cell>
                        <flux:table.cell><?php echo e($location->country_code ?? '—'); ?></flux:table.cell>
                        <flux:table.cell><?php echo e($location->timezone); ?></flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$this->statusColor($location->status)">
                                <?php echo e(ucfirst($location->status)); ?>

                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell><?php echo e($location->created_at?->format('M d, Y')); ?></flux:table.cell>
                    </flux:table.row>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <flux:table.row>
                        <flux:table.cell colspan="8">
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                No locations match the current filters.
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                <?php endif; ?>
            </tbody>
        </flux:table>
    </flux:card>
</div><?php /**PATH /home/kevin/Projects/waav2/storage/framework/views/livewire-testing/views/7df36d92.blade.php ENDPATH**/ ?>