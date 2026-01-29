@php
    $payment = $this->payment;
@endphp

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('admin.payments.index') }}" wire:navigate>
                Payments
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ $payment->provider_payment_id ?? $payment->id }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <flux:heading size="lg">Payment details</flux:heading>
        <flux:text>Provider activity, booking linkage, and refund tracking.</flux:text>
    </div>

    <flux:card class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Status</flux:text>
                <flux:badge :color="$this->statusColor($payment->status)">
                    {{ ucfirst($payment->status) }}
                </flux:badge>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Provider</flux:text>
                <flux:text>{{ ucfirst($payment->provider) }}</flux:text>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Provider ID</flux:text>
                <flux:text>{{ $payment->provider_payment_id ?? '—' }}</flux:text>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Amount</flux:text>
                <flux:text class="text-lg font-semibold">
                    {{ number_format((float) $payment->amount, 2) }} {{ $payment->currency }}
                </flux:text>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Booking</flux:text>
                @if ($payment->booking)
                    <flux:link href="{{ route('admin.bookings.show', $payment->booking) }}" wire:navigate>
                        {{ $payment->booking->booking_reference ?? $payment->booking->id }}
                    </flux:link>
                @else
                    <flux:text>—</flux:text>
                @endif
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Partner</flux:text>
                @if ($payment->partner)
                    <flux:link href="{{ route('admin.partners.show', $payment->partner) }}" wire:navigate>
                        {{ $payment->partner->name }}
                    </flux:link>
                @else
                    <flux:text>—</flux:text>
                @endif
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Captured</flux:text>
                <flux:text>{{ $payment->captured_at?->format('M d, Y H:i') ?? '—' }}</flux:text>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Created</flux:text>
                <flux:text>{{ $payment->created_at?->format('M d, Y H:i') }}</flux:text>
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
                @forelse ($payment->refunds as $refund)
                    <flux:table.row :key="$refund->id">
                        <flux:table.cell variant="strong">
                            {{ $refund->provider_refund_id ?? $refund->id }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$this->statusColor($refund->status)">
                                {{ ucfirst($refund->status) }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            {{ number_format((float) $refund->amount, 2) }} {{ $refund->currency }}
                        </flux:table.cell>
                        <flux:table.cell>{{ $refund->reason ?? '—' }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4">
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                No refunds recorded.
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </tbody>
        </flux:table>
    </flux:card>

    <div class="grid gap-6 lg:grid-cols-2">
        <flux:card class="space-y-3">
            <flux:heading size="sm">Raw payload</flux:heading>
            <flux:text class="text-sm">Provider payload stored for audit.</flux:text>
            <div class="max-h-72 overflow-auto rounded-lg bg-zinc-50 p-3 text-xs text-zinc-700 dark:bg-zinc-900/60 dark:text-zinc-200">
                <pre class="whitespace-pre-wrap">{{ json_encode($payment->raw_payload ?? [], JSON_PRETTY_PRINT) }}</pre>
            </div>
        </flux:card>

        <flux:card class="space-y-3">
            <flux:heading size="sm">Metadata</flux:heading>
            <flux:text class="text-sm">Additional payment attributes.</flux:text>
            <div class="max-h-72 overflow-auto rounded-lg bg-zinc-50 p-3 text-xs text-zinc-700 dark:bg-zinc-900/60 dark:text-zinc-200">
                <pre class="whitespace-pre-wrap">{{ json_encode($payment->meta ?? [], JSON_PRETTY_PRINT) }}</pre>
            </div>
        </flux:card>
    </div>
</div>
