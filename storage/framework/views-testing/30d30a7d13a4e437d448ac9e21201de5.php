<?php
    $fees = $this->fees;
?>

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <flux:heading size="lg">Fees</flux:heading>
        <flux:text>Configure fees that apply to accommodation bookings.</flux:text>
    </div>

    <flux:card class="space-y-5">
        <div class="flex items-center justify-between gap-4">
            <div>
                <flux:heading size="lg">Add fee</flux:heading>
                <flux:text>Create a new fee rule.</flux:text>
            </div>
        </div>

        <?php if($this->savedMessage): ?>
            <flux:callout icon="check-circle">
                <flux:callout.heading><?php echo e($this->savedMessage); ?></flux:callout.heading>
            </flux:callout>
        <?php endif; ?>

        <form wire:submit="createFee" class="grid gap-4 lg:grid-cols-6">
            <flux:field class="lg:col-span-2">
                <flux:label>Name</flux:label>
                <flux:input wire:model.live="createName" placeholder="Cleaning fee" />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>Type</flux:label>
                <flux:select wire:model.live="createType">
                    <flux:select.option value="">Flat (default)</flux:select.option>
                    <flux:select.option value="flat">Flat</flux:select.option>
                    <flux:select.option value="per_night">Per night</flux:select.option>
                    <flux:select.option value="per_person">Per person</flux:select.option>
                </flux:select>
                <flux:error name="type" />
            </flux:field>

            <flux:field>
                <flux:label>Amount</flux:label>
                <flux:input wire:model.live="createAmount" placeholder="25.00" />
                <flux:error name="amount" />
            </flux:field>

            <flux:field class="lg:col-span-2">
                <flux:label>Applies to</flux:label>
                <flux:input wire:model.live="createAppliesTo" placeholder="booking" />
                <flux:error name="applies_to" />
            </flux:field>

            <flux:field>
                <flux:label>Status</flux:label>
                <flux:select wire:model.live="createStatus">
                    <flux:select.option value="active">Active</flux:select.option>
                    <flux:select.option value="inactive">Inactive</flux:select.option>
                </flux:select>
                <flux:error name="status" />
            </flux:field>

            <div class="lg:col-span-6">
                <flux:button type="submit" variant="primary">Create fee</flux:button>
            </div>
        </form>
    </flux:card>

    <?php if($this->editingId): ?>
        <flux:card class="space-y-5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <flux:heading size="lg">Edit fee</flux:heading>
                    <flux:text>Update the selected fee.</flux:text>
                </div>
                <flux:button type="button" variant="ghost" wire:click="cancelEditing">Cancel</flux:button>
            </div>

            <form wire:submit="updateFee" class="grid gap-4 lg:grid-cols-6">
                <flux:field class="lg:col-span-2">
                    <flux:label>Name</flux:label>
                    <flux:input wire:model.live="editName" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Type</flux:label>
                    <flux:select wire:model.live="editType">
                        <flux:select.option value="flat">Flat</flux:select.option>
                        <flux:select.option value="per_night">Per night</flux:select.option>
                        <flux:select.option value="per_person">Per person</flux:select.option>
                    </flux:select>
                    <flux:error name="type" />
                </flux:field>

                <flux:field>
                    <flux:label>Amount</flux:label>
                    <flux:input wire:model.live="editAmount" />
                    <flux:error name="amount" />
                </flux:field>

                <flux:field class="lg:col-span-2">
                    <flux:label>Applies to</flux:label>
                    <flux:input wire:model.live="editAppliesTo" />
                    <flux:error name="applies_to" />
                </flux:field>

                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select wire:model.live="editStatus">
                        <flux:select.option value="active">Active</flux:select.option>
                        <flux:select.option value="inactive">Inactive</flux:select.option>
                    </flux:select>
                    <flux:error name="status" />
                </flux:field>

                <div class="lg:col-span-6">
                    <flux:button type="submit" variant="primary">Update fee</flux:button>
                </div>
            </form>
        </flux:card>
    <?php endif; ?>

    <flux:card class="space-y-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
            <flux:field class="flex-1">
                <flux:label>Search</flux:label>
                <flux:input wire:model.live="search" placeholder="Name" />
            </flux:field>

            <flux:field class="w-full lg:w-44">
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

        <flux:table :paginate="$fees">
            <thead data-flux-columns>
                <flux:table.row>
                    <flux:table.column>Name</flux:table.column>
                    <flux:table.column>Type</flux:table.column>
                    <flux:table.column>Amount</flux:table.column>
                    <flux:table.column>Applies to</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column align="end"></flux:table.column>
                </flux:table.row>
            </thead>
            <tbody data-flux-rows>
                <?php $__empty_1 = true; $__currentLoopData = $fees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <flux:table.row :key="$fee->id">
                        <flux:table.cell variant="strong"><?php echo e($fee->name); ?></flux:table.cell>
                        <flux:table.cell><?php echo e($fee->type ?? 'flat'); ?></flux:table.cell>
                        <flux:table.cell><?php echo e($fee->amount); ?></flux:table.cell>
                        <flux:table.cell><?php echo e($fee->applies_to); ?></flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$fee->status === 'active' ? 'green' : 'red'">
                                <?php echo e(ucfirst($fee->status)); ?>

                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            <div class="flex justify-end gap-2">
                                <flux:button size="sm" variant="ghost" wire:click="startEditing('<?php echo e($fee->id); ?>')">
                                    Edit
                                </flux:button>
                                <flux:button
                                    size="sm"
                                    variant="ghost"
                                    wire:confirm="Delete this fee?"
                                    wire:click="deleteFee('<?php echo e($fee->id); ?>')"
                                >
                                    Delete
                                </flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <flux:table.row>
                        <flux:table.cell colspan="6">
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                No fees configured yet.
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                <?php endif; ?>
            </tbody>
        </flux:table>
    </flux:card>
</div><?php /**PATH /home/kevin/Projects/waav2/storage/framework/views/livewire-testing/views/30aed0a8.blade.php ENDPATH**/ ?>