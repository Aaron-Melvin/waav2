@php
    $payments = $this->payments;
@endphp

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <flux:heading size="lg">Payments overview</flux:heading>
        <flux:text>Track provider activity, refunds, and booking revenue by partner.</flux:text>
    </div>

    <flux:card class="space-y-5">
        <div class="grid gap-4 lg:grid-cols-5">
            <flux:field class="lg:col-span-2">
                <flux:label>Search</flux:label>
                <flux:input
                    wire:model.live="search"
                    placeholder="Provider reference, booking, or partner"
                />
            </flux:field>

            <flux:field>
                <flux:label>Status</flux:label>
                <flux:select wire:model.live="status">
                    <flux:select.option value="all">All</flux:select.option>
                    <flux:select.option value="pending">Pending</flux:select.option>
                    <flux:select.option value="authorized">Authorized</flux:select.option>
                    <flux:select.option value="captured">Captured</flux:select.option>
                    <flux:select.option value="failed">Failed</flux:select.option>
                    <flux:select.option value="refunded">Refunded</flux:select.option>
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label>Provider</flux:label>
                <flux:select wire:model.live="provider">
                    <flux:select.option value="all">All</flux:select.option>
                    <flux:select.option value="stripe">Stripe</flux:select.option>
                    <flux:select.option value="adyen">Adyen</flux:select.option>
                    <flux:select.option value="manual">Manual</flux:select.option>
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label>Per page</flux:label>
                <flux:select wire:model.live="perPage">
                    <flux:select.option value="15">15</flux:select.option>
                    <flux:select.option value="30">30</flux:select.option>
                    <flux:select.option value="50">50</flux:select.option>
                </flux:select>
            </flux:field>
        </div>

        <flux:table :paginate="$payments">
            <thead data-flux-columns>
                <flux:table.row>
                    <flux:table.column>Payment</flux:table.column>
                    <flux:table.column>Partner</flux:table.column>
                    <flux:table.column>Booking</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Amount</flux:table.column>
                    <flux:table.column>Captured</flux:table.column>
                </flux:table.row>
            </thead>
            <tbody data-flux-rows>
                @forelse ($payments as $payment)
                    <flux:table.row :key="$payment->id">
                        <flux:table.cell variant="strong">
                            <flux:link href="{{ route('admin.payments.show', $payment) }}" wire:navigate>
                                {{ $payment->provider_payment_id ?? $payment->id }}
                            </flux:link>
                        </flux:table.cell>
                        <flux:table.cell>{{ $payment->partner?->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            {{ $payment->booking?->booking_reference ?? '—' }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$this->statusColor($payment->status)">
                                {{ ucfirst($payment->status) }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ number_format((float) $payment->amount, 2) }} {{ $payment->currency }}
                        </flux:table.cell>
                        <flux:table.cell>{{ $payment->captured_at?->format('M d, Y H:i') ?? '—' }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6">
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                No payments match the current filters.
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </tbody>
        </flux:table>
    </flux:card>
</div>
