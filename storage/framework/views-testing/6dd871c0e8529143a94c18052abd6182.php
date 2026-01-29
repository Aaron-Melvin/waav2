<?php
    $policies = $this->policies;
?>

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <flux:heading size="lg">Cancellation policies</flux:heading>
        <flux:text>Define refund rules for accommodation bookings.</flux:text>
    </div>

    <flux:card class="space-y-5">
        <div class="flex items-center justify-between gap-4">
            <div>
                <flux:heading size="lg">Add policy</flux:heading>
                <flux:text>Create rules as JSON array of rule objects.</flux:text>
            </div>
        </div>

        <?php if($this->savedMessage): ?>
            <flux:callout icon="check-circle">
                <flux:callout.heading><?php echo e($this->savedMessage); ?></flux:callout.heading>
            </flux:callout>
        <?php endif; ?>

        <form wire:submit="createPolicy" class="grid gap-4 lg:grid-cols-6">
            <flux:field class="lg:col-span-2">
                <flux:label>Name</flux:label>
                <flux:input wire:model.live="createName" placeholder="Standard stay" />
                <flux:error name="name" />
            </flux:field>

            <flux:field class="lg:col-span-2">
                <flux:label>Status</flux:label>
                <flux:select wire:model.live="createStatus">
                    <flux:select.option value="active">Active</flux:select.option>
                    <flux:select.option value="inactive">Inactive</flux:select.option>
                </flux:select>
                <flux:error name="status" />
            </flux:field>

            <flux:field class="lg:col-span-6">
                <flux:label>Description</flux:label>
                <flux:textarea rows="2" wire:model.live="createDescription" />
                <flux:error name="description" />
            </flux:field>

            <flux:field class="lg:col-span-6">
                <flux:label>Rules JSON</flux:label>
                <flux:textarea
                    rows="4"
                    wire:model.live="createRulesJson"
                    placeholder='[{"days_before":14,"penalty_percent":0},{"days_before":3,"penalty_percent":50},{"days_before":0,"penalty_percent":100}]'
                />
                <flux:error name="rules" />
            </flux:field>

            <div class="lg:col-span-6">
                <flux:button type="submit" variant="primary">Create policy</flux:button>
            </div>
        </form>
    </flux:card>

    <?php if($this->editingId): ?>
        <flux:card class="space-y-5">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <flux:heading size="lg">Edit policy</flux:heading>
                    <flux:text>Update the selected cancellation policy.</flux:text>
                </div>
                <flux:button type="button" variant="ghost" wire:click="cancelEditing">Cancel</flux:button>
            </div>

            <form wire:submit="updatePolicy" class="grid gap-4 lg:grid-cols-6">
                <flux:field class="lg:col-span-2">
                    <flux:label>Name</flux:label>
                    <flux:input wire:model.live="editName" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field class="lg:col-span-2">
                    <flux:label>Status</flux:label>
                    <flux:select wire:model.live="editStatus">
                        <flux:select.option value="active">Active</flux:select.option>
                        <flux:select.option value="inactive">Inactive</flux:select.option>
                    </flux:select>
                    <flux:error name="status" />
                </flux:field>

                <flux:field class="lg:col-span-6">
                    <flux:label>Description</flux:label>
                    <flux:textarea rows="2" wire:model.live="editDescription" />
                    <flux:error name="description" />
                </flux:field>

                <flux:field class="lg:col-span-6">
                    <flux:label>Rules JSON</flux:label>
                    <flux:textarea rows="4" wire:model.live="editRulesJson" />
                    <flux:error name="edit_rules" />
                </flux:field>

                <div class="lg:col-span-6">
                    <flux:button type="submit" variant="primary">Update policy</flux:button>
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

        <flux:table :paginate="$policies">
            <thead data-flux-columns>
                <flux:table.row>
                    <flux:table.column>Name</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column align="end">Rules</flux:table.column>
                    <flux:table.column align="end"></flux:table.column>
                </flux:table.row>
            </thead>
            <tbody data-flux-rows>
                <?php $__empty_1 = true; $__currentLoopData = $policies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $policy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <flux:table.row :key="$policy->id">
                        <flux:table.cell variant="strong"><?php echo e($policy->name); ?></flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$policy->status === 'active' ? 'green' : 'red'">
                                <?php echo e(ucfirst($policy->status)); ?>

                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell align="end"><?php echo e(is_array($policy->rules) ? count($policy->rules) : 0); ?></flux:table.cell>
                        <flux:table.cell align="end">
                            <div class="flex justify-end gap-2">
                                <flux:button size="sm" variant="ghost" wire:click="startEditing('<?php echo e($policy->id); ?>')">
                                    Edit
                                </flux:button>
                                <flux:button
                                    size="sm"
                                    variant="ghost"
                                    wire:confirm="Delete this policy?"
                                    wire:click="deletePolicy('<?php echo e($policy->id); ?>')"
                                >
                                    Delete
                                </flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <flux:table.row>
                        <flux:table.cell colspan="4">
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                No policies configured yet.
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                <?php endif; ?>
            </tbody>
        </flux:table>
    </flux:card>
</div><?php /**PATH /home/kevin/Projects/waav2/storage/framework/views/livewire-testing/views/29be7eff.blade.php ENDPATH**/ ?>