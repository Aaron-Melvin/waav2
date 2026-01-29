@php
    $partners = $this->partners;
    $locations = $this->locations;
    $products = $this->products;
    $results = $this->results;
    $hasRange = $this->hasRange;
@endphp

<div class="flex flex-col gap-10">
    <section class="relative overflow-hidden rounded-3xl border border-slate-200/70 bg-white/80 p-8 shadow-sm backdrop-blur dark:border-slate-700/70 dark:bg-slate-900/70">
        <x-placeholder-pattern
            class="pointer-events-none absolute inset-0 h-full w-full text-slate-200/70 dark:text-slate-700/60"
            stroke="currentColor"
        />
        <div class="relative z-10 grid gap-8 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,0.8fr)]">
            <div class="flex flex-col gap-5">
                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-300">
                    <flux:badge color="blue">Coastal escapes</flux:badge>
                    <flux:badge color="amber">Live availability</flux:badge>
                </div>
                <flux:heading size="xl">Find your next adventure.</flux:heading>
                <flux:text class="text-base text-slate-600 dark:text-slate-300">
                    Browse real-time spaces across surf, hikes, paddling, and Atlantic stays. Lock in your dates and
                    discover local partners in minutes.
                </flux:text>
            </div>
            <div class="flex flex-col gap-4 rounded-2xl border border-slate-200/70 bg-white/90 p-6 shadow-sm dark:border-slate-700/70 dark:bg-slate-950/70">
                <flux:text class="text-sm uppercase tracking-[0.2em] text-slate-500 dark:text-slate-300">
                    What you can do
                </flux:text>
                <div class="grid gap-3 text-sm text-slate-600 dark:text-slate-300">
                    <div class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-emerald-500"></span>
                        <span>Filter by partner, product, and date range.</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-sky-500"></span>
                        <span>See live pricing, capacity, and availability.</span>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-1 h-2 w-2 rounded-full bg-amber-500"></span>
                        <span>Start a hold or booking from the results.</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <flux:card class="space-y-6">
        <div class="flex flex-col gap-2">
            <flux:heading size="lg">Search availability</flux:heading>
            <flux:text>Choose a partner and product to explore availability windows.</flux:text>
        </div>

        <form wire:submit="search" class="grid gap-4 lg:grid-cols-10">
            <flux:field class="lg:col-span-2">
                <flux:label>Partner</flux:label>
                <flux:select wire:model.live="partnerId">
                    <flux:select.option value="">All partners</flux:select.option>
                    @foreach ($partners as $partner)
                        <flux:select.option value="{{ $partner->id }}" wire:key="partner-{{ $partner->id }}">
                            {{ $partner->name }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:field>

            <flux:field class="lg:col-span-2">
                <flux:label>Location</flux:label>
                <flux:select wire:model.live="locationId">
                    <flux:select.option value="">All locations</flux:select.option>
                    @foreach ($locations as $location)
                        <flux:select.option value="{{ $location->id }}" wire:key="location-{{ $location->id }}">
                            {{ $location->name }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:field>

            <flux:field class="lg:col-span-2">
                <flux:label>Type</flux:label>
                <flux:select wire:model.live="productType">
                    <flux:select.option value="">All types</flux:select.option>
                    <flux:select.option value="event">Event</flux:select.option>
                    <flux:select.option value="accommodation">Accommodation</flux:select.option>
                </flux:select>
            </flux:field>

            <flux:field class="lg:col-span-2">
                <flux:label>Product (optional)</flux:label>
                <flux:select wire:model.live="productId">
                    <flux:select.option value="">Select a product</flux:select.option>
                    @forelse ($products as $product)
                        <flux:select.option value="{{ $product->id }}" wire:key="product-{{ $product->id }}">
                            {{ $product->name }}
                        </flux:select.option>
                    @empty
                        <flux:select.option disabled>No products available</flux:select.option>
                    @endforelse
                </flux:select>
            </flux:field>

            <div class="lg:col-span-3 lg:row-start-2">
                <flux:date-picker
                    wire:model.live="dateRange"
                    mode="range"
                    label="Dates"
                    placeholder="Select dates"
                    with-inputs
                    with-presets
                    presets="today last7Days thisMonth"
                    min="today"
                    clearable
                />
            </div>

            <flux:field class="lg:row-start-2">
                <flux:label>Guests</flux:label>
                <flux:input type="number" min="1" wire:model.live="quantity" />
            </flux:field>

            <flux:field class="lg:row-start-2">
                <flux:label>Sort</flux:label>
                <flux:select wire:model.live="sort">
                    <flux:select.option value="date">Soonest first</flux:select.option>
                    <flux:select.option value="price_low">Lowest price</flux:select.option>
                    <flux:select.option value="price_high">Highest price</flux:select.option>
                    <flux:select.option value="availability">Most available</flux:select.option>
                </flux:select>
            </flux:field>

            <div class="lg:col-span-10">
                <flux:button
                    type="submit"
                    variant="primary"
                    class="w-full data-loading:pointer-events-none data-loading:opacity-60"
                >
                    <span class="in-data-loading:hidden">Search availability</span>
                    <span class="hidden in-data-loading:flex">Searching…</span>
                </flux:button>
            </div>
        </form>
    </flux:card>

    <section class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <flux:heading size="lg">Availability</flux:heading>
            @if ($this->hasSearched && $results->isNotEmpty())
                <flux:text class="text-sm text-slate-500 dark:text-slate-300">
                    {{ $results->count() }} options found
                </flux:text>
            @endif
        </div>

        @if ($this->holdMessage)
            <flux:callout icon="clock">
                <flux:callout.heading>Hold status</flux:callout.heading>
                <flux:callout.text>
                    {{ $this->holdMessage }}
                    @if ($this->lastHoldId)
                        <flux:callout.link href="{{ route('front.booking.details', $this->lastHoldId) }}" wire:navigate>
                            Continue booking
                        </flux:callout.link>
                    @endif
                </flux:callout.text>
            </flux:callout>
        @endif

        @if (! $this->hasSearched)
            <flux:card class="p-6 text-center">
                <flux:text class="text-sm text-slate-500 dark:text-slate-300">
                    Set your filters and run a search to see live availability.
                </flux:text>
            </flux:card>
        @elseif (! $hasRange)
            <flux:card class="p-6 text-center">
                <flux:text class="text-sm text-slate-500 dark:text-slate-300">
                    Select a date range to view matching availability windows.
                </flux:text>
            </flux:card>
        @elseif ($results->isEmpty())
            <flux:card class="p-6 text-center">
                <flux:text class="text-sm text-slate-500 dark:text-slate-300">
                    No availability matched those dates. Try adjusting your range or guest count.
                </flux:text>
            </flux:card>
        @else
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($results as $result)
                    @php
                        $product = $result->product;
                        $partner = $result->partner;
                        $location = $result->location;
                        $currency = strtoupper($result->currency ?? 'EUR');
                        $priceMin = $result->price_min ? number_format((float) $result->price_min, 2) : null;
                        $priceMax = $result->price_max ? number_format((float) $result->price_max, 2) : null;
                        $capacity = $result->capacity_available ?? null;
                    @endphp
                    <flux:card class="flex h-full flex-col gap-4" wire:key="availability-{{ $result->id }}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="space-y-1">
                                <flux:heading size="lg">{{ $product?->name ?? 'Experience' }}</flux:heading>
                                <flux:text class="text-sm text-slate-500 dark:text-slate-300">
                                    {{ $partner?->name ?? 'Partner' }}
                                </flux:text>
                            </div>
                            <flux:badge color="blue">{{ ucfirst($product?->type ?? 'event') }}</flux:badge>
                        </div>

                        <div class="space-y-2 text-sm text-slate-600 dark:text-slate-300">
                            <div>
                                <span class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-slate-400">Dates</span>
                                <div class="font-medium text-slate-700 dark:text-slate-200">
                                    {{ $result->starts_on?->format('M d, Y') ?? '—' }}
                                    @if ($result->ends_on)
                                        – {{ $result->ends_on?->format('M d, Y') }}
                                    @endif
                                </div>
                            </div>
                            <div>
                                <span class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-slate-400">Location</span>
                                <div class="font-medium text-slate-700 dark:text-slate-200">
                                    {{ $location?->name ?? 'Atlantic Coast' }}
                                    @if ($location?->city)
                                        · {{ $location->city }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mt-auto flex items-end justify-between gap-4 border-t border-slate-200/70 pt-4 text-sm dark:border-slate-700/70">
                            <div>
                                <flux:text class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-slate-400">
                                    Starting from
                                </flux:text>
                                <div class="text-lg font-semibold text-slate-900 dark:text-white">
                                    {{ $priceMin ? "{$currency} {$priceMin}" : 'Contact for price' }}
                                </div>
                                @if ($priceMax && $priceMax !== $priceMin)
                                    <flux:text class="text-xs text-slate-500 dark:text-slate-400">
                                        Up to {{ $currency }} {{ $priceMax }}
                                    </flux:text>
                                @endif
                            </div>
                            <div class="text-right">
                                <flux:text class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-slate-400">
                                    Spaces
                                </flux:text>
                                <div class="text-lg font-semibold text-slate-900 dark:text-white">
                                    {{ $capacity ?? 'Open' }}
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <flux:button
                                type="button"
                                variant="filled"
                                class="w-full data-loading:pointer-events-none data-loading:opacity-60"
                                wire:click="createHold('{{ $result->id }}', true)"
                            >
                                Start booking
                            </flux:button>
                            <flux:button
                                type="button"
                                variant="ghost"
                                class="w-full data-loading:pointer-events-none data-loading:opacity-60"
                                wire:click="createHold('{{ $result->id }}')"
                            >
                                Hold for 15 minutes
                            </flux:button>
                        </div>
                    </flux:card>
                @endforeach
            </div>
        @endif
    </section>
</div>
