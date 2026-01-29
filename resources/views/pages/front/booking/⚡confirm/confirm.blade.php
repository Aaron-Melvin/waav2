@php
    $partner = $booking->partner;
    $product = $booking->items->first()?->product;
    $currency = strtoupper($booking->currency ?? 'EUR');
@endphp

<div class="flex flex-col gap-8">
    <div class="flex flex-col gap-2">
        <flux:heading size="xl">Confirm booking</flux:heading>
        <flux:text class="text-slate-600 dark:text-slate-300">
            Review the booking details and finalize payment.
        </flux:text>
    </div>

    @if ($this->confirmed)
        <flux:callout icon="badge-check">
            <flux:callout.heading>Booking confirmed</flux:callout.heading>
            <flux:callout.text>
                Your booking is confirmed and ready. We’ll send confirmation to {{ $booking->customer?->email ?? 'your email' }}.
            </flux:callout.text>
        </flux:callout>
    @endif

    @if ($this->errorMessage)
        <flux:callout variant="danger" icon="triangle-alert">
            <flux:callout.heading>Unable to confirm</flux:callout.heading>
            <flux:callout.text>{{ $this->errorMessage }}</flux:callout.text>
        </flux:callout>
    @endif

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,0.8fr)]">
        <flux:card class="space-y-6">
            <div class="flex flex-col gap-2">
                <flux:heading size="lg">Payment</flux:heading>
                <flux:text>Capture payment details to finalize the booking.</flux:text>
            </div>

            <form wire:submit="confirmBooking" class="grid gap-4">
                <flux:field>
                    <flux:label>Payment method</flux:label>
                    <flux:select wire:model.live="paymentMethod">
                        <flux:select.option value="manual">Manual</flux:select.option>
                        <flux:select.option value="stripe">Stripe</flux:select.option>
                        <flux:select.option value="bank_transfer">Bank transfer</flux:select.option>
                    </flux:select>
                    <flux:error name="paymentMethod" />
                </flux:field>

                <flux:field>
                    <flux:label>Payment token (optional)</flux:label>
                    <flux:input wire:model.live="paymentToken" placeholder="gateway_token" />
                </flux:field>

                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select wire:model.live="status">
                        <flux:select.option value="captured">Captured</flux:select.option>
                        <flux:select.option value="authorized">Authorized</flux:select.option>
                    </flux:select>
                    <flux:error name="status" />
                </flux:field>

                <flux:button
                    type="submit"
                    variant="primary"
                    class="w-full data-loading:pointer-events-none data-loading:opacity-60"
                    @if ($this->confirmed) disabled @endif
                >
                    Confirm booking
                </flux:button>
            </form>
        </flux:card>

        <flux:card class="space-y-5">
            <flux:heading size="lg">Summary</flux:heading>
            <div class="space-y-3 text-sm text-slate-600 dark:text-slate-300">
                <div>
                    <flux:text class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-slate-400">Partner</flux:text>
                    <div class="font-medium text-slate-800 dark:text-slate-100">{{ $partner?->name ?? 'Partner' }}</div>
                </div>
                <div>
                    <flux:text class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-slate-400">Experience</flux:text>
                    <div class="font-medium text-slate-800 dark:text-slate-100">{{ $product?->name ?? 'Experience' }}</div>
                </div>
                <div class="grid grid-cols-2 gap-3 border-t border-slate-200/70 pt-4 text-sm dark:border-slate-700/70">
                    <div>
                        <flux:text class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-slate-400">Total</flux:text>
                        <div class="text-lg font-semibold text-slate-900 dark:text-white">
                            {{ $currency }} {{ number_format((float) $booking->total_gross, 2) }}
                        </div>
                    </div>
                    <div>
                        <flux:text class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-slate-400">Status</flux:text>
                        <div class="text-lg font-semibold text-slate-900 dark:text-white">
                            {{ ucfirst($booking->status) }}
                        </div>
                    </div>
                </div>
            </div>
        </flux:card>
    </div>
</div>
