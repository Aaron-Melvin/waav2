@php
    $bookings = $this->bookings;
@endphp

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <flux:heading size="lg">Bookings overview</flux:heading>
        <flux:text>Monitor partner bookings, filter by status, and review customer activity.</flux:text>
    </div>

    <flux:card class="space-y-5">
        <div class="grid gap-4 lg:grid-cols-5">
            <flux:field class="lg:col-span-2">
                <flux:label>Search</flux:label>
                <flux:input
                    wire:model.live="search"
                    placeholder="Reference, customer, or partner"
                />
            </flux:field>

            <flux:field>
                <flux:label>Status</flux:label>
                <flux:select wire:model.live="status">
                    <flux:select.option value="all">All</flux:select.option>
                    <flux:select.option value="draft">Draft</flux:select.option>
                    <flux:select.option value="pending_payment">Pending Payment</flux:select.option>
                    <flux:select.option value="confirmed">Confirmed</flux:select.option>
                    <flux:select.option value="completed">Completed</flux:select.option>
                    <flux:select.option value="cancelled">Cancelled</flux:select.option>
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

        <flux:table :paginate="$bookings">
            <thead data-flux-columns>
                <flux:table.row>
                    <flux:table.column>Reference</flux:table.column>
                    <flux:table.column>Partner</flux:table.column>
                    <flux:table.column>Customer</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Total</flux:table.column>
                    <flux:table.column>Created</flux:table.column>
                </flux:table.row>
            </thead>
            <tbody data-flux-rows>
                @forelse ($bookings as $booking)
                    <flux:table.row :key="$booking->id">
                        <flux:table.cell variant="strong">
                            <flux:link href="{{ route('admin.bookings.show', $booking) }}" wire:navigate>
                                {{ $booking->booking_reference ?? $booking->id }}
                            </flux:link>
                        </flux:table.cell>
                        <flux:table.cell>{{ $booking->partner?->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex flex-col">
                                <span>{{ $booking->customer?->name ?? '—' }}</span>
                                <flux:text class="text-xs text-zinc-500 dark:text-zinc-300">
                                    {{ $booking->customer?->email ?? '' }}
                                </flux:text>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$this->statusColor($booking->status)">
                                {{ str_replace('_', ' ', ucfirst($booking->status)) }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ number_format((float) $booking->total_gross, 2) }} {{ $booking->currency }}
                        </flux:table.cell>
                        <flux:table.cell>{{ $booking->created_at?->format('M d, Y') }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6">
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                No bookings match the current filters.
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </tbody>
        </flux:table>
    </flux:card>
</div>
