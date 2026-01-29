<?php
    $products = $this->products;
?>

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <flux:heading size="lg">Catalog products</flux:heading>
        <flux:text>Manage the products and accommodations visible to customers.</flux:text>
    </div>

    <flux:card class="space-y-5">
        <div class="flex items-center justify-between gap-4">
            <div>
                <flux:heading size="lg">Add product</flux:heading>
                <flux:text>Create a new event or accommodation product.</flux:text>
                <flux:link class="text-sm" href="<?php echo e(route('partner.catalog.accommodations.create')); ?>" wire:navigate>
                    Use the accommodation setup wizard
                </flux:link>
            </div>
        </div>

        <?php if($this->savedMessage): ?>
            <flux:callout icon="check-circle">
                <flux:callout.heading><?php echo e($this->savedMessage); ?></flux:callout.heading>
            </flux:callout>
        <?php endif; ?>

        <form wire:submit="createProduct" class="grid gap-4 lg:grid-cols-6">
            <flux:field class="lg:col-span-2">
                <flux:label>Name</flux:label>
                <flux:input wire:model.live="createName" placeholder="Wild Atlantic Adventure" />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>Type</flux:label>
                <flux:select wire:model.live="createType">
                    <flux:select.option value="event">Event</flux:select.option>
                    <flux:select.option value="accommodation">Accommodation</flux:select.option>
                </flux:select>
                <flux:error name="type" />
            </flux:field>

            <flux:field>
                <flux:label>Slug (optional)</flux:label>
                <flux:input wire:model.live="createSlug" placeholder="wild-atlantic-adventure" />
                <flux:error name="slug" />
            </flux:field>

            <flux:field>
                <flux:label>Location</flux:label>
                <flux:select wire:model.live="createLocationId">
                    <flux:select.option value="">No location</flux:select.option>
                    <?php $__currentLoopData = $this->locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <flux:select.option value="<?php echo e($location->id); ?>" wire:key="create-location-<?php echo e($location->id); ?>">
                            <?php echo e($location->name); ?>

                        </flux:select.option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </flux:select>
                <flux:error name="location_id" />
            </flux:field>

            <flux:field>
                <flux:label>Capacity</flux:label>
                <flux:input type="number" min="1" wire:model.live="createCapacityTotal" />
                <flux:error name="capacity_total" />
            </flux:field>

            <flux:field>
                <flux:label>Currency</flux:label>
                <flux:input wire:model.live="createDefaultCurrency" placeholder="EUR" />
                <flux:error name="default_currency" />
            </flux:field>

            <flux:field>
                <flux:label>Status</flux:label>
                <flux:select wire:model.live="createStatus">
                    <flux:select.option value="active">Active</flux:select.option>
                    <flux:select.option value="inactive">Inactive</flux:select.option>
                </flux:select>
                <flux:error name="status" />
            </flux:field>

            <flux:field>
                <flux:label>Visibility</flux:label>
                <flux:select wire:model.live="createVisibility">
                    <flux:select.option value="public">Public</flux:select.option>
                    <flux:select.option value="unlisted">Unlisted</flux:select.option>
                    <flux:select.option value="private">Private</flux:select.option>
                </flux:select>
                <flux:error name="visibility" />
            </flux:field>

            <flux:field>
                <flux:label>Lead time (minutes)</flux:label>
                <flux:input type="number" min="0" wire:model.live="createLeadTimeMinutes" />
                <flux:error name="lead_time_minutes" />
            </flux:field>

            <flux:field>
                <flux:label>Cutoff (minutes)</flux:label>
                <flux:input type="number" min="0" wire:model.live="createCutoffMinutes" />
                <flux:error name="cutoff_minutes" />
            </flux:field>

            <flux:field class="lg:col-span-6">
                <flux:label>Description</flux:label>
                <flux:textarea rows="3" wire:model.live="createDescription" placeholder="Add a short description." />
                <flux:error name="description" />
            </flux:field>

            <div class="lg:col-span-6">
                <flux:button type="submit" variant="primary">Create product</flux:button>
            </div>
        </form>
    </flux:card>

    <flux:card class="space-y-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
            <flux:field class="flex-1">
                <flux:label>Search</flux:label>
                <flux:input
                    wire:model.live="search"
                    placeholder="Name, slug, or location"
                />
            </flux:field>

            <flux:field class="w-full lg:w-44">
                <flux:label>Type</flux:label>
                <flux:select wire:model.live="type">
                    <flux:select.option value="all">All</flux:select.option>
                    <flux:select.option value="event">Event</flux:select.option>
                    <flux:select.option value="accommodation">Accommodation</flux:select.option>
                </flux:select>
            </flux:field>

            <flux:field class="w-full lg:w-44">
                <flux:label>Status</flux:label>
                <flux:select wire:model.live="status">
                    <flux:select.option value="all">All</flux:select.option>
                    <flux:select.option value="active">Active</flux:select.option>
                    <flux:select.option value="inactive">Inactive</flux:select.option>
                </flux:select>
            </flux:field>

            <flux:field class="w-full lg:w-44">
                <flux:label>Visibility</flux:label>
                <flux:select wire:model.live="visibility">
                    <flux:select.option value="all">All</flux:select.option>
                    <flux:select.option value="public">Public</flux:select.option>
                    <flux:select.option value="unlisted">Unlisted</flux:select.option>
                    <flux:select.option value="private">Private</flux:select.option>
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

        <flux:table :paginate="$products">
            <thead data-flux-columns>
                <flux:table.row>
                    <flux:table.column>Product</flux:table.column>
                    <flux:table.column>Type</flux:table.column>
                    <flux:table.column>Location</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Visibility</flux:table.column>
                    <flux:table.column align="end">Capacity</flux:table.column>
                </flux:table.row>
            </thead>
            <tbody data-flux-rows>
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <flux:table.row :key="$product->id">
                        <flux:table.cell variant="strong">
                            <flux:link href="<?php echo e(route('partner.catalog.products.show', $product)); ?>" wire:navigate>
                                <?php echo e($product->name); ?>

                            </flux:link>
                        </flux:table.cell>
                        <flux:table.cell><?php echo e(ucfirst($product->type)); ?></flux:table.cell>
                        <flux:table.cell><?php echo e($product->location?->name ?? '—'); ?></flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$this->statusColor($product->status)">
                                <?php echo e(ucfirst($product->status)); ?>

                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$this->visibilityColor($product->visibility)">
                                <?php echo e(ucfirst($product->visibility)); ?>

                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            <?php echo e($product->capacity_total ?? '—'); ?>

                        </flux:table.cell>
                    </flux:table.row>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <flux:table.row>
                        <flux:table.cell colspan="6">
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                No products match the current filters.
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                <?php endif; ?>
            </tbody>
        </flux:table>
    </flux:card>
</div><?php /**PATH /home/kevin/Projects/waav2/storage/framework/views/livewire-testing/views/3e91b71d.blade.php ENDPATH**/ ?>