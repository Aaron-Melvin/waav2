<?php
    $event = $this->event;
?>

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="<?php echo e(route('partner.availability.events.index')); ?>" wire:navigate>
                Event availability
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item><?php echo e($event->product?->name ?? 'Event'); ?></flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <flux:heading size="lg"><?php echo e($event->product?->name ?? 'Event'); ?></flux:heading>
        <flux:text>Adjust publish state and capacity for this event.</flux:text>
    </div>

    <flux:card>
        <div class="flex flex-wrap gap-4 text-sm">
            <flux:link href="<?php echo e(route('partner.availability.events.overrides', $event)); ?>" wire:navigate>
                Manage overrides
            </flux:link>
        </div>
    </flux:card>

    <flux:card class="space-y-4">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Starts</flux:text>
                <flux:text><?php echo e($event->starts_at?->format('M d, Y H:i') ?? '—'); ?></flux:text>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Ends</flux:text>
                <flux:text><?php echo e($event->ends_at?->format('M d, Y H:i') ?? '—'); ?></flux:text>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Status</flux:text>
                <flux:badge :color="$this->statusColor($event->status)">
                    <?php echo e(ucfirst($event->status)); ?>

                </flux:badge>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Publish</flux:text>
                <flux:badge :color="$this->publishStateColor($event->publish_state)">
                    <?php echo e(ucfirst($event->publish_state)); ?>

                </flux:badge>
            </div>
        </div>
    </flux:card>

    <flux:card class="space-y-5">
        <div class="flex flex-col gap-1">
            <flux:heading size="sm">Availability settings</flux:heading>
            <flux:text class="text-sm">Update capacity and publish state.</flux:text>
        </div>

        <?php if($savedMessage): ?>
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:border-emerald-500/40 dark:bg-emerald-500/10 dark:text-emerald-100">
                <?php echo e($savedMessage); ?>

            </div>
        <?php endif; ?>

        <form wire:submit="updateEvent" class="space-y-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select wire:model.live="status">
                        <flux:select.option value="scheduled">Scheduled</flux:select.option>
                        <flux:select.option value="completed">Completed</flux:select.option>
                        <flux:select.option value="cancelled">Cancelled</flux:select.option>
                    </flux:select>
                    <flux:error name="status" />
                </flux:field>

                <flux:field>
                    <flux:label>Publish state</flux:label>
                    <flux:select wire:model.live="publishState">
                        <flux:select.option value="draft">Draft</flux:select.option>
                        <flux:select.option value="published">Published</flux:select.option>
                    </flux:select>
                    <flux:error name="publish_state" />
                </flux:field>

                <flux:field>
                    <flux:label>Traffic light</flux:label>
                    <flux:select wire:model.live="trafficLight">
                        <flux:select.option value="">None</flux:select.option>
                        <flux:select.option value="green">Green</flux:select.option>
                        <flux:select.option value="yellow">Yellow</flux:select.option>
                        <flux:select.option value="red">Red</flux:select.option>
                    </flux:select>
                    <flux:error name="traffic_light" />
                </flux:field>

                <flux:field>
                    <flux:label>Total capacity</flux:label>
                    <flux:input type="number" min="0" wire:model.live="capacityTotal" />
                    <flux:error name="capacity_total" />
                </flux:field>

                <flux:field>
                    <flux:label>Reserved capacity</flux:label>
                    <flux:input type="number" min="0" wire:model.live="capacityReserved" />
                    <flux:error name="capacity_reserved" />
                </flux:field>
            </div>

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">Save changes</flux:button>
            </div>
        </form>
    </flux:card>
</div><?php /**PATH /home/kevin/Projects/waav2/storage/framework/views/livewire-testing/views/398e5c31.blade.php ENDPATH**/ ?>