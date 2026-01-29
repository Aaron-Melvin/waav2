<?php
    $payment = $this->payment;
?>

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="<?php echo e(route('admin.payments.index')); ?>" wire:navigate>
                Payments
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item><?php echo e($payment->provider_payment_id ?? $payment->id); ?></flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <flux:heading size="lg">Payment details</flux:heading>
        <flux:text>Provider activity, booking linkage, and refund tracking.</flux:text>
    </div>

    <flux:card class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Status</flux:text>
                <flux:badge :color="$this->statusColor($payment->status)">
                    <?php echo e(ucfirst($payment->status)); ?>

                </flux:badge>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Provider</flux:text>
                <flux:text><?php echo e(ucfirst($payment->provider)); ?></flux:text>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Provider ID</flux:text>
                <flux:text><?php echo e($payment->provider_payment_id ?? '—'); ?></flux:text>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Amount</flux:text>
                <flux:text class="text-lg font-semibold">
                    <?php echo e(number_format((float) $payment->amount, 2)); ?> <?php echo e($payment->currency); ?>

                </flux:text>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Booking</flux:text>
                <?php if($payment->booking): ?>
                    <flux:link href="<?php echo e(route('admin.bookings.show', $payment->booking)); ?>" wire:navigate>
                        <?php echo e($payment->booking->booking_reference ?? $payment->booking->id); ?>

                    </flux:link>
                <?php else: ?>
                    <flux:text>—</flux:text>
                <?php endif; ?>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Partner</flux:text>
                <?php if($payment->partner): ?>
                    <flux:link href="<?php echo e(route('admin.partners.show', $payment->partner)); ?>" wire:navigate>
                        <?php echo e($payment->partner->name); ?>

                    </flux:link>
                <?php else: ?>
                    <flux:text>—</flux:text>
                <?php endif; ?>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Captured</flux:text>
                <flux:text><?php echo e($payment->captured_at?->format('M d, Y H:i') ?? '—'); ?></flux:text>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Created</flux:text>
                <flux:text><?php echo e($payment->created_at?->format('M d, Y H:i')); ?></flux:text>
            </div>
        </div>
    </flux:card>

    <flux:card class="space-y-4">
        <div class="flex flex-col gap-1">
            <flux:heading size="sm">Refunds</flux:heading>
            <flux:text class="text-sm">Refund activity associated with this payment.</flux:text>
        </div>

        <flux:table>
            <thead data-flux-columns>
                <flux:table.row>
                    <flux:table.column>Refund</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column align="end">Amount</flux:table.column>
                    <flux:table.column>Reason</flux:table.column>
                </flux:table.row>
            </thead>
            <tbody data-flux-rows>
                <?php $__empty_1 = true; $__currentLoopData = $payment->refunds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $refund): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <flux:table.row :key="$refund->id">
                        <flux:table.cell variant="strong">
                            <?php echo e($refund->provider_refund_id ?? $refund->id); ?>

                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$this->statusColor($refund->status)">
                                <?php echo e(ucfirst($refund->status)); ?>

                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            <?php echo e(number_format((float) $refund->amount, 2)); ?> <?php echo e($refund->currency); ?>

                        </flux:table.cell>
                        <flux:table.cell><?php echo e($refund->reason ?? '—'); ?></flux:table.cell>
                    </flux:table.row>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <flux:table.row>
                        <flux:table.cell colspan="4">
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                No refunds recorded.
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                <?php endif; ?>
            </tbody>
        </flux:table>
    </flux:card>

    <div class="grid gap-6 lg:grid-cols-2">
        <flux:card class="space-y-3">
            <flux:heading size="sm">Raw payload</flux:heading>
            <flux:text class="text-sm">Provider payload stored for audit.</flux:text>
            <div class="max-h-72 overflow-auto rounded-lg bg-zinc-50 p-3 text-xs text-zinc-700 dark:bg-zinc-900/60 dark:text-zinc-200">
                <pre class="whitespace-pre-wrap"><?php echo e(json_encode($payment->raw_payload ?? [], JSON_PRETTY_PRINT)); ?></pre>
            </div>
        </flux:card>

        <flux:card class="space-y-3">
            <flux:heading size="sm">Metadata</flux:heading>
            <flux:text class="text-sm">Additional payment attributes.</flux:text>
            <div class="max-h-72 overflow-auto rounded-lg bg-zinc-50 p-3 text-xs text-zinc-700 dark:bg-zinc-900/60 dark:text-zinc-200">
                <pre class="whitespace-pre-wrap"><?php echo e(json_encode($payment->meta ?? [], JSON_PRETTY_PRINT)); ?></pre>
            </div>
        </flux:card>
    </div>
</div><?php /**PATH /home/kevin/Projects/waav2/storage/framework/views/livewire-testing/views/7ff01057.blade.php ENDPATH**/ ?>