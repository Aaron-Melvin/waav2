<?php
    $event = $this->event;
    $overrides = $this->overrides;
?>

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="<?php echo e(route('partner.availability.events.index')); ?>" wire:navigate>
                Event availability
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="<?php echo e(route('partner.availability.events.show', $event)); ?>" wire:navigate>
                <?php echo e($event->product?->name ?? 'Event'); ?>

            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Overrides</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <flux:heading size="lg">Event overrides</flux:heading>
        <flux:text>Adjust capacity and pricing for a single event date.</flux:text>
    </div>

    <flux:card class="space-y-4">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Starts</flux:text>
                <flux:text><?php echo e($event->starts_at?->format('M d, Y H:i') ?? '—'); ?></flux:text>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Capacity</flux:text>
                <flux:text><?php echo e($event->capacity_total ?? '—'); ?></flux:text>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Reserved</flux:text>
                <flux:text><?php echo e($event->capacity_reserved); ?></flux:text>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Publish</flux:text>
                <flux:text><?php echo e(ucfirst($event->publish_state)); ?></flux:text>
            </div>
        </div>
    </flux:card>

    <flux:card class="space-y-5">
        <div class="flex flex-col gap-1">
            <flux:heading size="sm">Add override</flux:heading>
            <flux:text class="text-sm">Overrides apply only to this event date.</flux:text>
        </div>

        <?php if($savedMessage): ?>
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:border-emerald-500/40 dark:bg-emerald-500/10 dark:text-emerald-100">
                <?php echo e($savedMessage); ?>

            </div>
        <?php endif; ?>

        <form wire:submit="addOverride" class="space-y-6">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <flux:field>
                    <flux:label>Field</flux:label>
                    <flux:select wire:model.live="overrideField">
                        <flux:select.option value="capacity_total">Capacity total</flux:select.option>
                        <flux:select.option value="price_override">Price override</flux:select.option>
                        <flux:select.option value="notes">Notes</flux:select.option>
                    </flux:select>
                    <flux:error name="field" />
                </flux:field>

                <flux:field>
                    <flux:label>Value</flux:label>
                    <flux:input wire:model.live="overrideValue" placeholder="e.g. 24" />
                    <flux:error name="value" />
                </flux:field>

                <?php if($overrideField === 'price_override'): ?>
                    <flux:field>
                        <flux:label>Currency</flux:label>
                        <flux:input wire:model.live="overrideCurrency" placeholder="EUR" />
                        <flux:error name="currency" />
                    </flux:field>
                <?php endif; ?>
            </div>

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">Add override</flux:button>
            </div>
        </form>
    </flux:card>

    <flux:card class="space-y-4">
        <div class="flex flex-col gap-1">
            <flux:heading size="sm">Current overrides</flux:heading>
            <flux:text class="text-sm">Remove overrides to return to defaults.</flux:text>
        </div>

        <flux:table>
            <thead data-flux-columns>
                <flux:table.row>
                    <flux:table.column>Field</flux:table.column>
                    <flux:table.column>Value</flux:table.column>
                    <flux:table.column align="end"></flux:table.column>
                </flux:table.row>
            </thead>
            <tbody data-flux-rows>
                <?php $__empty_1 = true; $__currentLoopData = $overrides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $override): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <flux:table.row :key="$override->id">
                        <flux:table.cell variant="strong"><?php echo e(str_replace('_', ' ', ucfirst($override->field))); ?></flux:table.cell>
                        <flux:table.cell><?php echo e($this->displayValue($override)); ?></flux:table.cell>
                        <flux:table.cell align="end">
                            <flux:button
                                variant="ghost"
                                size="sm"
                                wire:click="deleteOverride('<?php echo e($override->id); ?>')"
                            >
                                Remove
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <flux:table.row>
                        <flux:table.cell colspan="3">
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                No overrides added yet.
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                <?php endif; ?>
            </tbody>
        </flux:table>
    </flux:card>
</div><?php /**PATH /home/kevin/Projects/waav2/storage/framework/views/livewire-testing/views/dafe84ae.blade.php ENDPATH**/ ?>