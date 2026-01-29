<?php
    $events = $this->events;
?>

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <flux:heading size="lg">Event availability</flux:heading>
        <flux:text>Track upcoming event capacity, reservations, and publish status.</flux:text>
    </div>

    <flux:card class="space-y-5">
        <div class="flex items-center justify-between gap-4">
            <div>
                <flux:heading size="lg">Add event</flux:heading>
                <flux:text>Create a new event instance for your schedule.</flux:text>
            </div>
        </div>

        <?php if($this->savedMessage): ?>
            <flux:callout icon="check-circle">
                <flux:callout.heading><?php echo e($this->savedMessage); ?></flux:callout.heading>
            </flux:callout>
        <?php endif; ?>

        <form wire:submit="createEvent" class="grid gap-4 lg:grid-cols-6">
            <flux:field class="lg:col-span-2">
                <flux:label>Product</flux:label>
                <flux:select wire:model.live="createProductId">
                    <flux:select.option value="">Select product</flux:select.option>
                    <?php $__currentLoopData = $this->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <flux:select.option value="<?php echo e($product->id); ?>" wire:key="event-product-<?php echo e($product->id); ?>">
                            <?php echo e($product->name); ?>

                        </flux:select.option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </flux:select>
                <flux:error name="product_id" />
            </flux:field>

            <flux:field class="lg:col-span-2">
                <flux:label>Event series (optional)</flux:label>
                <flux:select wire:model.live="createEventSeriesId">
                    <flux:select.option value="">No series</flux:select.option>
                    <?php $__currentLoopData = $this->eventSeries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $series): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <flux:select.option value="<?php echo e($series->id); ?>" wire:key="event-series-<?php echo e($series->id); ?>">
                            <?php echo e($series->name); ?>

                        </flux:select.option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </flux:select>
                <flux:error name="event_series_id" />
            </flux:field>

            <flux:field>
                <flux:label>Status</flux:label>
                <flux:select wire:model.live="createStatus">
                    <flux:select.option value="scheduled">Scheduled</flux:select.option>
                    <flux:select.option value="completed">Completed</flux:select.option>
                    <flux:select.option value="cancelled">Cancelled</flux:select.option>
                </flux:select>
                <flux:error name="status" />
            </flux:field>

            <flux:field>
                <flux:label>Publish</flux:label>
                <flux:select wire:model.live="createPublishState">
                    <flux:select.option value="draft">Draft</flux:select.option>
                    <flux:select.option value="published">Published</flux:select.option>
                </flux:select>
                <flux:error name="publish_state" />
            </flux:field>

            <flux:field>
                <flux:label>Starts at</flux:label>
                <flux:input type="datetime-local" wire:model.live="createStartsAt" />
                <flux:error name="starts_at" />
            </flux:field>

            <flux:field>
                <flux:label>Ends at</flux:label>
                <flux:input type="datetime-local" wire:model.live="createEndsAt" />
                <flux:error name="ends_at" />
            </flux:field>

            <flux:field>
                <flux:label>Capacity</flux:label>
                <flux:input type="number" min="0" wire:model.live="createCapacityTotal" />
                <flux:error name="capacity_total" />
            </flux:field>

            <flux:field>
                <flux:label>Reserved</flux:label>
                <flux:input type="number" min="0" wire:model.live="createCapacityReserved" />
                <flux:error name="capacity_reserved" />
            </flux:field>

            <flux:field>
                <flux:label>Traffic</flux:label>
                <flux:select wire:model.live="createTrafficLight">
                    <flux:select.option value="">None</flux:select.option>
                    <flux:select.option value="green">Green</flux:select.option>
                    <flux:select.option value="yellow">Yellow</flux:select.option>
                    <flux:select.option value="red">Red</flux:select.option>
                </flux:select>
                <flux:error name="traffic_light" />
            </flux:field>

            <div class="flex items-center gap-3 lg:col-span-2">
                <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-200">
                    <input type="checkbox" wire:model.live="createWeatherAlert">
                    Weather alert
                </label>
                <flux:error name="weather_alert" />
            </div>

            <div class="lg:col-span-6">
                <flux:button type="submit" variant="primary">Create event</flux:button>
            </div>
        </form>
    </flux:card>

    <flux:card class="space-y-5">
        <div class="grid gap-4 lg:grid-cols-6">
            <flux:field class="lg:col-span-2">
                <flux:label>Search</flux:label>
                <flux:input
                    wire:model.live="search"
                    placeholder="Product name or slug"
                />
            </flux:field>

            <flux:field>
                <flux:label>Status</flux:label>
                <flux:select wire:model.live="status">
                    <flux:select.option value="all">All</flux:select.option>
                    <flux:select.option value="scheduled">Scheduled</flux:select.option>
                    <flux:select.option value="completed">Completed</flux:select.option>
                    <flux:select.option value="cancelled">Cancelled</flux:select.option>
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label>Publish</flux:label>
                <flux:select wire:model.live="publishState">
                    <flux:select.option value="all">All</flux:select.option>
                    <flux:select.option value="draft">Draft</flux:select.option>
                    <flux:select.option value="published">Published</flux:select.option>
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label>Traffic</flux:label>
                <flux:select wire:model.live="trafficLight">
                    <flux:select.option value="all">All</flux:select.option>
                    <flux:select.option value="green">Green</flux:select.option>
                    <flux:select.option value="yellow">Yellow</flux:select.option>
                    <flux:select.option value="red">Red</flux:select.option>
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label>From</flux:label>
                <flux:input type="date" wire:model.live="dateFrom" />
            </flux:field>

            <flux:field>
                <flux:label>To</flux:label>
                <flux:input type="date" wire:model.live="dateTo" />
            </flux:field>
        </div>

        <div class="flex justify-end">
            <flux:field class="w-full lg:w-40">
                <flux:label>Per page</flux:label>
                <flux:select wire:model.live="perPage">
                    <flux:select.option value="15">15</flux:select.option>
                    <flux:select.option value="30">30</flux:select.option>
                    <flux:select.option value="50">50</flux:select.option>
                </flux:select>
            </flux:field>
        </div>

        <flux:table :paginate="$events">
            <thead data-flux-columns>
                <flux:table.row>
                    <flux:table.column>Event</flux:table.column>
                    <flux:table.column>Starts</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Publish</flux:table.column>
                    <flux:table.column>Traffic</flux:table.column>
                    <flux:table.column align="end">Capacity</flux:table.column>
                    <flux:table.column align="end">Reserved</flux:table.column>
                    <flux:table.column align="end">Available</flux:table.column>
                </flux:table.row>
            </thead>
            <tbody data-flux-rows>
                <?php $__empty_1 = true; $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $available = $event->capacity_total === null
                            ? null
                            : max($event->capacity_total - $event->capacity_reserved, 0);
                    ?>
                    <flux:table.row :key="$event->id">
                        <flux:table.cell variant="strong">
                            <div class="flex flex-col">
                                <flux:link href="<?php echo e(route('partner.availability.events.show', $event)); ?>" wire:navigate>
                                    <?php echo e($event->product?->name ?? 'Event'); ?>

                                </flux:link>
                                <flux:text class="text-xs text-zinc-500 dark:text-zinc-300">
                                    <?php echo e($event->starts_at?->format('M d, Y H:i') ?? '—'); ?>

                                </flux:text>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell><?php echo e($event->starts_at?->format('M d, Y') ?? '—'); ?></flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$this->statusColor($event->status)">
                                <?php echo e(ucfirst($event->status)); ?>

                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$this->publishStateColor($event->publish_state)">
                                <?php echo e(ucfirst($event->publish_state)); ?>

                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$this->trafficLightColor($event->traffic_light)">
                                <?php echo e(ucfirst($event->traffic_light ?? '—')); ?>

                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell align="end"><?php echo e($event->capacity_total ?? '—'); ?></flux:table.cell>
                        <flux:table.cell align="end"><?php echo e($event->capacity_reserved); ?></flux:table.cell>
                        <flux:table.cell align="end"><?php echo e($available ?? '—'); ?></flux:table.cell>
                    </flux:table.row>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <flux:table.row>
                        <flux:table.cell colspan="8">
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                No events match the current filters.
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                <?php endif; ?>
            </tbody>
        </flux:table>
    </flux:card>
</div><?php /**PATH /home/kevin/Projects/waav2/storage/framework/views/livewire-testing/views/4d58f993.blade.php ENDPATH**/ ?>