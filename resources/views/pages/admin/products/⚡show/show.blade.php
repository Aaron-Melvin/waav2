@php
    $product = $this->product;
@endphp

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('admin.products.index') }}" wire:navigate>
                Products
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ $product->name }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <flux:heading size="lg">{{ $product->name }}</flux:heading>
        <flux:text>Catalog details, rate plans, and media coverage.</flux:text>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <flux:card class="space-y-4 lg:col-span-2">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Type</flux:text>
                    <flux:text>{{ ucfirst($product->type) }}</flux:text>
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Status</flux:text>
                    <flux:badge :color="$this->statusColor($product->status)">
                        {{ ucfirst($product->status) }}
                    </flux:badge>
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Visibility</flux:text>
                    <flux:badge :color="$this->visibilityColor($product->visibility)">
                        {{ ucfirst($product->visibility) }}
                    </flux:badge>
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Partner</flux:text>
                    @if ($product->partner)
                        <flux:link href="{{ route('admin.partners.show', $product->partner) }}" wire:navigate>
                            {{ $product->partner->name }}
                        </flux:link>
                    @else
                        <flux:text>—</flux:text>
                    @endif
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Location</flux:text>
                    @if ($product->location)
                        <flux:link href="{{ route('admin.locations.show', $product->location) }}" wire:navigate>
                            {{ $product->location->name }}
                        </flux:link>
                    @else
                        <flux:text>—</flux:text>
                    @endif
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Capacity</flux:text>
                    <flux:text>{{ $product->capacity_total ?? '—' }}</flux:text>
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Currency</flux:text>
                    <flux:text>{{ $product->default_currency ?? '—' }}</flux:text>
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Slug</flux:text>
                    <flux:text>{{ $product->slug }}</flux:text>
                </div>
            </div>

            <div class="h-px bg-zinc-200 dark:bg-zinc-700"></div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Lead time</flux:text>
                    <flux:text>{{ $product->lead_time_minutes ? $product->lead_time_minutes.' min' : '—' }}</flux:text>
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Cutoff</flux:text>
                    <flux:text>{{ $product->cutoff_minutes ? $product->cutoff_minutes.' min' : '—' }}</flux:text>
                </div>
            </div>

            @if ($product->description)
                <div class="h-px bg-zinc-200 dark:bg-zinc-700"></div>
                <div class="space-y-2">
                    <flux:heading size="sm">Description</flux:heading>
                    <flux:text>{{ $product->description }}</flux:text>
                </div>
            @endif
        </flux:card>

        <flux:card class="space-y-4">
            <flux:heading size="sm">Coverage</flux:heading>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <flux:text>Event series</flux:text>
                    <flux:badge color="zinc">{{ $product->event_series_count }}</flux:badge>
                </div>
                <div class="flex items-center justify-between">
                    <flux:text>Events</flux:text>
                    <flux:badge color="zinc">{{ $product->events_count }}</flux:badge>
                </div>
                <div class="flex items-center justify-between">
                    <flux:text>Units</flux:text>
                    <flux:badge color="zinc">{{ $product->units_count }}</flux:badge>
                </div>
                <div class="flex items-center justify-between">
                    <flux:text>Rate plans</flux:text>
                    <flux:badge color="zinc">{{ $product->rate_plans_count }}</flux:badge>
                </div>
                <div class="flex items-center justify-between">
                    <flux:text>Media assets</flux:text>
                    <flux:badge color="zinc">{{ $product->media_count }}</flux:badge>
                </div>
                <div class="flex items-center justify-between">
                    <flux:text>Eligibility rules</flux:text>
                    <flux:badge color="zinc">{{ $product->eligibility_rules_count }}</flux:badge>
                </div>
            </div>
        </flux:card>
    </div>

    <flux:card class="space-y-4">
        <div class="flex flex-col gap-1">
            <flux:heading size="sm">Rate plans</flux:heading>
            <flux:text class="text-sm">Pricing configurations linked to this product.</flux:text>
        </div>

        <flux:table>
            <thead data-flux-columns>
                <flux:table.row>
                    <flux:table.column>Plan</flux:table.column>
                    <flux:table.column>Code</flux:table.column>
                    <flux:table.column>Model</flux:table.column>
                    <flux:table.column>Currency</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                </flux:table.row>
            </thead>
            <tbody data-flux-rows>
                @forelse ($product->ratePlans as $ratePlan)
                    <flux:table.row :key="$ratePlan->id">
                        <flux:table.cell variant="strong">{{ $ratePlan->name }}</flux:table.cell>
                        <flux:table.cell>{{ $ratePlan->code ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ ucfirst($ratePlan->pricing_model ?? '—') }}</flux:table.cell>
                        <flux:table.cell>{{ $ratePlan->currency ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ ucfirst($ratePlan->status ?? '—') }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5">
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                No rate plans configured yet.
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </tbody>
        </flux:table>
    </flux:card>

    <flux:card class="space-y-4">
        <div class="flex flex-col gap-1">
            <flux:heading size="sm">Units</flux:heading>
            <flux:text class="text-sm">Accommodation units connected to this product.</flux:text>
        </div>

        <flux:table>
            <thead data-flux-columns>
                <flux:table.row>
                    <flux:table.column>Name</flux:table.column>
                    <flux:table.column>Code</flux:table.column>
                    <flux:table.column align="end">Adults</flux:table.column>
                    <flux:table.column align="end">Children</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                </flux:table.row>
            </thead>
            <tbody data-flux-rows>
                @forelse ($product->units as $unit)
                    <flux:table.row :key="$unit->id">
                        <flux:table.cell variant="strong">{{ $unit->name }}</flux:table.cell>
                        <flux:table.cell>{{ $unit->code ?? '—' }}</flux:table.cell>
                        <flux:table.cell align="end">{{ $unit->occupancy_adults }}</flux:table.cell>
                        <flux:table.cell align="end">{{ $unit->occupancy_children }}</flux:table.cell>
                        <flux:table.cell>{{ ucfirst($unit->status ?? '—') }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5">
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                No units configured for this product.
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </tbody>
        </flux:table>
    </flux:card>

    <flux:card class="space-y-4">
        <div class="flex flex-col gap-1">
            <flux:heading size="sm">Media library</flux:heading>
            <flux:text class="text-sm">Media assets published for this product.</flux:text>
        </div>

        <flux:table>
            <thead data-flux-columns>
                <flux:table.row>
                    <flux:table.column>URL</flux:table.column>
                    <flux:table.column>Kind</flux:table.column>
                    <flux:table.column align="end">Sort</flux:table.column>
                </flux:table.row>
            </thead>
            <tbody data-flux-rows>
                @forelse ($product->media as $media)
                    <flux:table.row :key="$media->id">
                        <flux:table.cell variant="strong">{{ $media->url }}</flux:table.cell>
                        <flux:table.cell>{{ ucfirst($media->kind ?? '—') }}</flux:table.cell>
                        <flux:table.cell align="end">{{ $media->sort ?? '—' }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="3">
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                No media assets uploaded yet.
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </tbody>
        </flux:table>
    </flux:card>
</div>
