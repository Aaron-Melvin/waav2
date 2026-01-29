@php
    $product = $hold->product;
    $partner = $hold->partner;
    $location = $product?->location;
    $currency = strtoupper($partner?->currency ?? 'EUR');
    $estimateTotal = number_format($this->estimateTotal, 2);
@endphp

<div class="flex flex-col gap-8">
    <div class="flex flex-col gap-2">
        <flux:heading size="xl">Complete your booking</flux:heading>
        <flux:text class="text-slate-600 dark:text-slate-300">
            Reserve your {{ $product?->name ?? 'experience' }} and confirm your details.
        </flux:text>
    </div>

    @if ($this->isExpired)
        <flux:callout icon="clock">
            <flux:callout.heading>Hold expired</flux:callout.heading>
            <flux:callout.text>
                This hold is no longer active. Return to search to find a new window.
                <flux:callout.link href="{{ route('front.search') }}" wire:navigate>Back to search</flux:callout.link>
            </flux:callout.text>
        </flux:callout>
    @elseif ($hold->expires_at)
        <flux:callout icon="clock">
            <flux:callout.heading>Hold active</flux:callout.heading>
            <flux:callout.text>
                Complete your booking before {{ $hold->expires_at->format('M d, Y H:i') }}.
            </flux:callout.text>
        </flux:callout>
    @endif

    @if ($this->errorMessage)
        <flux:callout variant="danger" icon="triangle-alert">
            <flux:callout.heading>Booking issue</flux:callout.heading>
            <flux:callout.text>{{ $this->errorMessage }}</flux:callout.text>
        </flux:callout>
    @endif

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,0.8fr)]">
        <flux:card class="space-y-6">
            <div class="flex flex-col gap-2">
                <flux:heading size="lg">Customer details</flux:heading>
                <flux:text>We’ll send your confirmation here.</flux:text>
            </div>

            <form wire:submit="createBooking" class="grid gap-4">
                <flux:field>
                    <flux:label>Name</flux:label>
                    <flux:input wire:model.live="customerName" placeholder="Jane Doe" />
                    <flux:error name="customerName" />
                </flux:field>

                <flux:field>
                    <flux:label>Email</flux:label>
                    <flux:input type="email" wire:model.live="customerEmail" placeholder="jane@example.com" />
                    <flux:error name="customerEmail" />
                </flux:field>

                <flux:field>
                    <flux:label>Phone (optional)</flux:label>
                    <flux:input wire:model.live="customerPhone" placeholder="+353" />
                </flux:field>

                <flux:field>
                    <flux:label>Coupon code (optional)</flux:label>
                    <flux:input wire:model.live="couponCode" placeholder="SUMMER10" />
                </flux:field>

                <div class="rounded-2xl border border-slate-200/70 bg-slate-50 p-4 text-sm text-slate-600 dark:border-slate-700/70 dark:bg-slate-900/70 dark:text-slate-300">
                    <label class="flex items-start gap-3">
                        <input type="checkbox" wire:model.live="acceptTerms" class="mt-1">
                        <span>I agree to the terms for version {{ $termsVersion }}.</span>
                    </label>
                    <flux:error name="acceptTerms" />
                </div>

                <flux:button
                    type="submit"
                    variant="primary"
                    class="w-full data-loading:pointer-events-none data-loading:opacity-60"
                    @if ($this->isExpired) disabled @endif
                >
                    Continue to confirmation
                </flux:button>
            </form>
        </flux:card>

        <flux:card class="space-y-5">
            <flux:heading size="lg">Booking summary</flux:heading>
            <div class="space-y-3 text-sm text-slate-600 dark:text-slate-300">
                <div>
                    <flux:text class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-slate-400">Partner</flux:text>
                    <div class="font-medium text-slate-800 dark:text-slate-100">{{ $partner?->name ?? 'Partner' }}</div>
                </div>
                <div>
                    <flux:text class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-slate-400">Experience</flux:text>
                    <div class="font-medium text-slate-800 dark:text-slate-100">{{ $product?->name ?? 'Experience' }}</div>
                </div>
                <div>
                    <flux:text class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-slate-400">Dates</flux:text>
                    <div class="font-medium text-slate-800 dark:text-slate-100">
                        {{ $hold->starts_on?->format('M d, Y') ?? '—' }}
                        @if ($hold->ends_on)
                            – {{ $hold->ends_on?->format('M d, Y') }}
                        @endif
                    </div>
                </div>
                <div>
                    <flux:text class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-slate-400">Location</flux:text>
                    <div class="font-medium text-slate-800 dark:text-slate-100">
                        {{ $location?->name ?? 'Atlantic Coast' }}
                        @if ($location?->city)
                            · {{ $location->city }}
                        @endif
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3 border-t border-slate-200/70 pt-4 text-sm dark:border-slate-700/70">
                    <div>
                        <flux:text class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-slate-400">Guests</flux:text>
                        <div class="text-lg font-semibold text-slate-900 dark:text-white">
                            {{ $hold->quantity ?? 1 }}
                        </div>
                    </div>
                    <div>
                        <flux:text class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-slate-400">Estimated total</flux:text>
                        <div class="text-lg font-semibold text-slate-900 dark:text-white">
                            {{ $currency }} {{ $estimateTotal }}
                        </div>
                    </div>
                </div>
            </div>
        </flux:card>
    </div>
</div>
