<?php
    $booking = $this->booking;
?>

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="<?php echo e(route('admin.bookings.index')); ?>" wire:navigate>
                Bookings
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item><?php echo e($booking->booking_reference ?? $booking->id); ?></flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <flux:heading size="lg">
            Booking <?php echo e($booking->booking_reference ?? $booking->id); ?>

        </flux:heading>
        <flux:text>Review booking totals, allocations, and payment activity.</flux:text>
    </div>

    <flux:card class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Status</flux:text>
                <flux:badge :color="$this->statusColor($booking->status)">
                    <?php echo e(str_replace('_', ' ', ucfirst($booking->status))); ?>

                </flux:badge>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Payment status</flux:text>
                <flux:badge :color="$this->paymentStatusColor($booking->payment_status)">
                    <?php echo e(str_replace('_', ' ', ucfirst($booking->payment_status ?? 'unpaid'))); ?>

                </flux:badge>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Partner</flux:text>
                <?php if($booking->partner): ?>
                    <flux:link href="<?php echo e(route('admin.partners.show', $booking->partner)); ?>" wire:navigate>
                        <?php echo e($booking->partner->name); ?>

                    </flux:link>
                <?php else: ?>
                    <flux:text>—</flux:text>
                <?php endif; ?>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Customer</flux:text>
                <div class="flex flex-col">
                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                        <?php echo e($booking->customer?->name ?? '—'); ?>

                    </span>
                    <flux:text class="text-xs text-zinc-500 dark:text-zinc-300">
                        <?php echo e($booking->customer?->email ?? ''); ?>

                    </flux:text>
                </div>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Channel</flux:text>
                <flux:text><?php echo e(ucfirst($booking->channel ?? '—')); ?></flux:text>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Currency</flux:text>
                <flux:text><?php echo e($booking->currency); ?></flux:text>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Created</flux:text>
                <flux:text><?php echo e($booking->created_at?->format('M d, Y H:i')); ?></flux:text>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Terms version</flux:text>
                <flux:text><?php echo e($booking->terms_version ?? '—'); ?></flux:text>
            </div>
        </div>

        <div class="h-px bg-zinc-200 dark:bg-zinc-700"></div>

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Gross total</flux:text>
                <flux:text class="text-lg font-semibold">
                    <?php echo e(number_format((float) $booking->total_gross, 2)); ?> <?php echo e($booking->currency); ?>

                </flux:text>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Taxes</flux:text>
                <flux:text class="text-lg font-semibold">
                    <?php echo e(number_format((float) $booking->total_tax, 2)); ?> <?php echo e($booking->currency); ?>

                </flux:text>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Fees</flux:text>
                <flux:text class="text-lg font-semibold">
                    <?php echo e(number_format((float) $booking->total_fees, 2)); ?> <?php echo e($booking->currency); ?>

                </flux:text>
            </div>
        </div>
    </flux:card>

    <flux:card class="space-y-4">
        <div class="flex flex-col gap-1">
            <flux:heading size="sm">Items</flux:heading>
            <flux:text class="text-sm">Products and time windows included in the booking.</flux:text>
        </div>

        <flux:table>
            <thead data-flux-columns>
                <flux:table.row>
                    <flux:table.column>Item</flux:table.column>
                    <flux:table.column>Dates</flux:table.column>
                    <flux:table.column align="end">Qty</flux:table.column>
                    <flux:table.column align="end">Unit price</flux:table.column>
                    <flux:table.column align="end">Total</flux:table.column>
                </flux:table.row>
            </thead>
            <tbody data-flux-rows>
                <?php $__empty_1 = true; $__currentLoopData = $booking->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <flux:table.row :key="$item->id">
                        <flux:table.cell variant="strong">
                            <div class="flex flex-col">
                                <span><?php echo e($item->product?->name ?? '—'); ?></span>
                                <flux:text class="text-xs text-zinc-500 dark:text-zinc-300">
                                    <?php echo e(ucfirst($item->item_type ?? 'item')); ?>

                                </flux:text>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <?php echo e($item->starts_on?->format('M d, Y') ?? '—'); ?>

                            <?php if($item->ends_on): ?>
                                – <?php echo e($item->ends_on?->format('M d, Y')); ?>

                            <?php endif; ?>
                        </flux:table.cell>
                        <flux:table.cell align="end"><?php echo e($item->quantity); ?></flux:table.cell>
                        <flux:table.cell align="end">
                            <?php echo e(number_format((float) $item->unit_price, 2)); ?> <?php echo e($booking->currency); ?>

                        </flux:table.cell>
                        <flux:table.cell align="end">
                            <?php echo e(number_format((float) $item->total, 2)); ?> <?php echo e($booking->currency); ?>

                        </flux:table.cell>
                    </flux:table.row>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <flux:table.row>
                        <flux:table.cell colspan="5">
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                No items recorded for this booking.
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                <?php endif; ?>
            </tbody>
        </flux:table>
    </flux:card>

    <flux:card class="space-y-4">
        <div class="flex flex-col gap-1">
            <flux:heading size="sm">Allocations</flux:heading>
            <flux:text class="text-sm">Capacity reservations tied to this booking.</flux:text>
        </div>

        <flux:table>
            <thead data-flux-columns>
                <flux:table.row>
                    <flux:table.column>Type</flux:table.column>
                    <flux:table.column>Target</flux:table.column>
                    <flux:table.column align="end">Quantity</flux:table.column>
                </flux:table.row>
            </thead>
            <tbody data-flux-rows>
                <?php $__empty_1 = true; $__currentLoopData = $booking->allocations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $allocation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <flux:table.row :key="$allocation->id">
                        <flux:table.cell>
                            <?php echo e($allocation->event_id ? 'Event' : 'Unit'); ?>

                        </flux:table.cell>
                        <flux:table.cell>
                            <?php if($allocation->event): ?>
                                <div class="flex flex-col">
                                    <span><?php echo e($allocation->event->product?->name ?? 'Event'); ?></span>
                                    <flux:text class="text-xs text-zinc-500 dark:text-zinc-300">
                                        <?php echo e($allocation->event->starts_at?->format('M d, Y H:i') ?? '—'); ?>

                                    </flux:text>
                                </div>
                            <?php else: ?>
                                <?php echo e($allocation->unit?->name ?? '—'); ?>

                            <?php endif; ?>
                        </flux:table.cell>
                        <flux:table.cell align="end"><?php echo e($allocation->quantity); ?></flux:table.cell>
                    </flux:table.row>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <flux:table.row>
                        <flux:table.cell colspan="3">
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                No allocations recorded.
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                <?php endif; ?>
            </tbody>
        </flux:table>
    </flux:card>

    <div class="grid gap-6 lg:grid-cols-2">
        <flux:card class="space-y-4">
            <div class="flex flex-col gap-1">
                <flux:heading size="sm">Payments</flux:heading>
                <flux:text class="text-sm">Payment attempts tied to this booking.</flux:text>
            </div>

            <flux:table>
                <thead data-flux-columns>
                    <flux:table.row>
                        <flux:table.column>Provider</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                        <flux:table.column align="end">Amount</flux:table.column>
                        <flux:table.column>Captured</flux:table.column>
                    </flux:table.row>
                </thead>
                <tbody data-flux-rows>
                    <?php $__empty_1 = true; $__currentLoopData = $booking->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <flux:table.row :key="$payment->id">
                            <flux:table.cell variant="strong">
                                <flux:link href="<?php echo e(route('admin.payments.show', $payment)); ?>" wire:navigate>
                                    <?php echo e(ucfirst($payment->provider)); ?>

                                </flux:link>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$this->paymentStatusColor($payment->status)">
                                    <?php echo e(ucfirst($payment->status)); ?>

                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell align="end">
                                <?php echo e(number_format((float) $payment->amount, 2)); ?> <?php echo e($payment->currency); ?>

                            </flux:table.cell>
                            <flux:table.cell><?php echo e($payment->captured_at?->format('M d, Y H:i') ?? '—'); ?></flux:table.cell>
                        </flux:table.row>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <flux:table.row>
                            <flux:table.cell colspan="4">
                                <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                    No payments recorded yet.
                                </flux:text>
                            </flux:table.cell>
                        </flux:table.row>
                    <?php endif; ?>
                </tbody>
            </flux:table>
        </flux:card>

        <flux:card class="space-y-4">
            <div class="flex flex-col gap-1">
                <flux:heading size="sm">Status history</flux:heading>
                <flux:text class="text-sm">Lifecycle events for this booking.</flux:text>
            </div>

            <flux:table>
                <thead data-flux-columns>
                    <flux:table.row>
                        <flux:table.column>From</flux:table.column>
                        <flux:table.column>To</flux:table.column>
                        <flux:table.column>Reason</flux:table.column>
                        <flux:table.column>When</flux:table.column>
                    </flux:table.row>
                </thead>
                <tbody data-flux-rows>
                    <?php $__empty_1 = true; $__currentLoopData = $booking->statusHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <flux:table.row :key="$history->id">
                            <flux:table.cell><?php echo e($history->from_status ?? '—'); ?></flux:table.cell>
                            <flux:table.cell><?php echo e($history->to_status); ?></flux:table.cell>
                            <flux:table.cell><?php echo e($history->reason ?? '—'); ?></flux:table.cell>
                            <flux:table.cell><?php echo e($history->created_at?->format('M d, Y H:i')); ?></flux:table.cell>
                        </flux:table.row>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <flux:table.row>
                            <flux:table.cell colspan="4">
                                <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                    No status history yet.
                                </flux:text>
                            </flux:table.cell>
                        </flux:table.row>
                    <?php endif; ?>
                </tbody>
            </flux:table>
        </flux:card>
    </div>
</div><?php /**PATH /home/kevin/Projects/waav2/storage/framework/views/livewire-testing/views/5fdab544.blade.php ENDPATH**/ ?>