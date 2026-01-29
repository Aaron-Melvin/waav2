<?php
    $product = $this->product;
    $unit = $this->unit;
    $calendars = $this->calendars;
?>

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="<?php echo e(route('partner.catalog.products.index')); ?>" wire:navigate>
                Catalog products
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="<?php echo e(route('partner.catalog.products.show', $product)); ?>" wire:navigate>
                <?php echo e($product->name); ?>

            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="<?php echo e(route('partner.catalog.products.units.index', $product)); ?>" wire:navigate>
                Units
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item><?php echo e($unit->name); ?></flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <flux:heading size="lg"><?php echo e($unit->name); ?></flux:heading>
        <flux:text>Update unit details and calendar availability.</flux:text>
    </div>

    <flux:card class="space-y-4">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Status</flux:text>
                <flux:badge :color="$unit->status === 'active' ? 'green' : 'red'">
                    <?php echo e(ucfirst($unit->status)); ?>

                </flux:badge>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Code</flux:text>
                <flux:text><?php echo e($unit->code ?? '—'); ?></flux:text>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Occupancy</flux:text>
                <flux:text><?php echo e($unit->occupancy_adults); ?> adults / <?php echo e($unit->occupancy_children); ?> children</flux:text>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Housekeeping</flux:text>
                <flux:text><?php echo e($unit->housekeeping_required ? 'Required' : 'Optional'); ?></flux:text>
            </div>
        </div>
    </flux:card>

    <flux:card class="space-y-5">
        <div class="flex flex-col gap-1">
            <flux:heading size="sm">Unit settings</flux:heading>
            <flux:text class="text-sm">Adjust occupancy, status, and housekeeping requirements.</flux:text>
        </div>

        <?php if($savedMessage): ?>
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:border-emerald-500/40 dark:bg-emerald-500/10 dark:text-emerald-100">
                <?php echo e($savedMessage); ?>

            </div>
        <?php endif; ?>

        <form wire:submit="updateUnit" class="space-y-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label>Name</flux:label>
                    <flux:input wire:model.live="name" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Code</flux:label>
                    <flux:input wire:model.live="code" />
                    <flux:error name="code" />
                </flux:field>

                <flux:field>
                    <flux:label>Adults</flux:label>
                    <flux:input type="number" min="1" wire:model.live="occupancyAdults" />
                    <flux:error name="occupancy_adults" />
                </flux:field>

                <flux:field>
                    <flux:label>Children</flux:label>
                    <flux:input type="number" min="0" wire:model.live="occupancyChildren" />
                    <flux:error name="occupancy_children" />
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
                    <flux:label>Housekeeping required</flux:label>
                    <flux:select wire:model.live="housekeepingRequired">
                        <flux:select.option value="1">Required</flux:select.option>
                        <flux:select.option value="0">Optional</flux:select.option>
                    </flux:select>
                    <flux:error name="housekeeping_required" />
                </flux:field>

                <flux:field class="sm:col-span-2">
                    <flux:label>Amenities / metadata (JSON)</flux:label>
                    <flux:textarea
                        rows="3"
                        wire:model.live="metaJson"
                        placeholder='{"amenities":["wifi","parking","breakfast"]}'
                    />
                    <flux:error name="meta" />
                </flux:field>
            </div>

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">Save changes</flux:button>
            </div>
        </form>
    </flux:card>

    <flux:card class="space-y-5">
        <div class="flex flex-col gap-1">
            <flux:heading size="sm">Calendar overrides</flux:heading>
            <flux:text class="text-sm">Block dates or adjust stay requirements for this unit.</flux:text>
        </div>

        <?php if($calendarMessage): ?>
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:border-emerald-500/40 dark:bg-emerald-500/10 dark:text-emerald-100">
                <?php echo e($calendarMessage); ?>

            </div>
        <?php endif; ?>

        <form wire:submit="saveCalendar" class="space-y-6">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <flux:field>
                    <flux:label>Date</flux:label>
                    <flux:input type="date" wire:model.live="calendarDate" />
                    <flux:error name="date" />
                </flux:field>

                <flux:field>
                    <flux:label>Availability</flux:label>
                    <flux:select wire:model.live="calendarAvailable">
                        <flux:select.option value="1">Available</flux:select.option>
                        <flux:select.option value="0">Blocked</flux:select.option>
                    </flux:select>
                    <flux:error name="is_available" />
                </flux:field>

                <flux:field>
                    <flux:label>Min stay (nights)</flux:label>
                    <flux:input type="number" min="1" wire:model.live="calendarMinStay" />
                    <flux:error name="min_stay_nights" />
                </flux:field>

                <flux:field>
                    <flux:label>Max stay (nights)</flux:label>
                    <flux:input type="number" min="1" wire:model.live="calendarMaxStay" />
                    <flux:error name="max_stay_nights" />
                </flux:field>

                <flux:field class="sm:col-span-2 lg:col-span-4">
                    <flux:label>Reason</flux:label>
                    <flux:input wire:model.live="calendarReason" placeholder="Maintenance, owner stay, etc." />
                    <flux:error name="reason" />
                </flux:field>
            </div>

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">Save date</flux:button>
            </div>
        </form>

        <div class="h-px bg-zinc-200 dark:bg-zinc-700"></div>

        <flux:table>
            <thead data-flux-columns>
                <flux:table.row>
                    <flux:table.column>Date</flux:table.column>
                    <flux:table.column>Availability</flux:table.column>
                    <flux:table.column align="end">Min stay</flux:table.column>
                    <flux:table.column align="end">Max stay</flux:table.column>
                    <flux:table.column>Reason</flux:table.column>
                    <flux:table.column align="end"></flux:table.column>
                </flux:table.row>
            </thead>
            <tbody data-flux-rows>
                <?php $__empty_1 = true; $__currentLoopData = $calendars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $calendar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <flux:table.row :key="$calendar->id">
                        <flux:table.cell variant="strong"><?php echo e($calendar->date?->format('M d, Y')); ?></flux:table.cell>
                        <flux:table.cell><?php echo e($calendar->is_available ? 'Available' : 'Blocked'); ?></flux:table.cell>
                        <flux:table.cell align="end"><?php echo e($calendar->min_stay_nights ?? '—'); ?></flux:table.cell>
                        <flux:table.cell align="end"><?php echo e($calendar->max_stay_nights ?? '—'); ?></flux:table.cell>
                        <flux:table.cell><?php echo e($calendar->reason ?? '—'); ?></flux:table.cell>
                        <flux:table.cell align="end">
                            <flux:button
                                variant="ghost"
                                size="sm"
                                wire:click="deleteCalendar('<?php echo e($calendar->id); ?>')"
                            >
                                Remove
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <flux:table.row>
                        <flux:table.cell colspan="6">
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                No calendar overrides set yet.
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                <?php endif; ?>
            </tbody>
        </flux:table>
    </flux:card>

    <flux:card class="space-y-5">
        <div class="flex flex-col gap-1">
            <flux:heading size="sm">Bulk calendar range</flux:heading>
            <flux:text class="text-sm">Apply availability rules across a date range.</flux:text>
        </div>

        <?php if($rangeMessage): ?>
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:border-emerald-500/40 dark:bg-emerald-500/10 dark:text-emerald-100">
                <?php echo e($rangeMessage); ?>

            </div>
        <?php endif; ?>

        <form wire:submit="saveCalendarRange" class="space-y-6">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <flux:field>
                    <flux:label>Start date</flux:label>
                    <flux:input type="date" wire:model.live="rangeStart" />
                    <flux:error name="start" />
                </flux:field>

                <flux:field>
                    <flux:label>End date</flux:label>
                    <flux:input type="date" wire:model.live="rangeEnd" />
                    <flux:error name="end" />
                </flux:field>

                <flux:field>
                    <flux:label>Availability</flux:label>
                    <flux:select wire:model.live="rangeAvailable">
                        <flux:select.option value="1">Available</flux:select.option>
                        <flux:select.option value="0">Blocked</flux:select.option>
                    </flux:select>
                    <flux:error name="is_available" />
                </flux:field>

                <flux:field>
                    <flux:label>Min stay (nights)</flux:label>
                    <flux:input type="number" min="1" wire:model.live="rangeMinStay" />
                    <flux:error name="min_stay_nights" />
                </flux:field>

                <flux:field>
                    <flux:label>Max stay (nights)</flux:label>
                    <flux:input type="number" min="1" wire:model.live="rangeMaxStay" />
                    <flux:error name="max_stay_nights" />
                    <flux:error name="rangeMaxStay" />
                </flux:field>

                <flux:field class="sm:col-span-2 lg:col-span-4">
                    <flux:label>Reason</flux:label>
                    <flux:input wire:model.live="rangeReason" placeholder="Seasonal rules, maintenance, etc." />
                    <flux:error name="reason" />
                </flux:field>
            </div>

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">Apply range</flux:button>
            </div>
        </form>
    </flux:card>
</div><?php /**PATH /home/kevin/Projects/waav2/storage/framework/views/livewire-testing/views/3db61823.blade.php ENDPATH**/ ?>