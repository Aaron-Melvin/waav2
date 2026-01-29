@php
    $product = $this->product;
    $units = $this->units;
@endphp

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('partner.catalog.products.index') }}" wire:navigate>
                Catalog products
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="{{ route('partner.catalog.products.show', $product) }}" wire:navigate>
                {{ $product->name }}
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Units</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <flux:heading size="lg">Units</flux:heading>
        <flux:text>Manage accommodation units and occupancy settings.</flux:text>
    </div>

    <flux:card class="space-y-4">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Product</flux:text>
                <flux:text>{{ $product->name }}</flux:text>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Type</flux:text>
                <flux:text>{{ ucfirst($product->type) }}</flux:text>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Units</flux:text>
                <flux:text>{{ $units->total() }}</flux:text>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Location</flux:text>
                <flux:text>{{ $product->location?->name ?? '—' }}</flux:text>
            </div>
        </div>
    </flux:card>

    <flux:card class="space-y-5">
        <div class="flex items-center justify-between gap-4">
            <div>
                <flux:heading size="lg">Add unit</flux:heading>
                <flux:text>Create a new room or unit for this accommodation.</flux:text>
            </div>
        </div>

        @if ($this->savedMessage)
            <flux:callout icon="check-circle">
                <flux:callout.heading>{{ $this->savedMessage }}</flux:callout.heading>
            </flux:callout>
        @endif

        <form wire:submit="createUnit" class="grid gap-4 lg:grid-cols-6">
            <flux:field class="lg:col-span-2">
                <flux:label>Name</flux:label>
                <flux:input wire:model.live="createName" placeholder="Ocean View Suite" />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>Code</flux:label>
                <flux:input wire:model.live="createCode" placeholder="OVS-1" />
                <flux:error name="code" />
            </flux:field>

            <flux:field>
                <flux:label>Adults</flux:label>
                <flux:input type="number" min="1" wire:model.live="createOccupancyAdults" />
                <flux:error name="occupancy_adults" />
            </flux:field>

            <flux:field>
                <flux:label>Children</flux:label>
                <flux:input type="number" min="0" wire:model.live="createOccupancyChildren" />
                <flux:error name="occupancy_children" />
            </flux:field>

            <flux:field>
                <flux:label>Status</flux:label>
                <flux:select wire:model.live="createStatus">
                    <flux:select.option value="active">Active</flux:select.option>
                    <flux:select.option value="inactive">Inactive</flux:select.option>
                </flux:select>
                <flux:error name="status" />
            </flux:field>

            <flux:field>
                <flux:label>Housekeeping</flux:label>
                <flux:select wire:model.live="createHousekeepingRequired">
                    <flux:select.option value="1">Required</flux:select.option>
                    <flux:select.option value="0">Optional</flux:select.option>
                </flux:select>
                <flux:error name="housekeeping_required" />
            </flux:field>

            <flux:field class="lg:col-span-6">
                <flux:label>Amenities / metadata (JSON)</flux:label>
                <flux:textarea
                    rows="3"
                    wire:model.live="createMetaJson"
                    placeholder='{"amenities":["wifi","parking","breakfast"]}'
                />
                <flux:error name="meta" />
            </flux:field>

            <div class="lg:col-span-6">
                <flux:button type="submit" variant="primary">Create unit</flux:button>
            </div>
        </form>
    </flux:card>

    <flux:card class="space-y-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
            <flux:field class="flex-1">
                <flux:label>Search</flux:label>
                <flux:input wire:model.live="search" placeholder="Name or unit code" />
            </flux:field>

            <flux:field class="w-full lg:w-44">
                <flux:label>Status</flux:label>
                <flux:select wire:model.live="status">
                    <flux:select.option value="all">All</flux:select.option>
                    <flux:select.option value="active">Active</flux:select.option>
                    <flux:select.option value="inactive">Inactive</flux:select.option>
                </flux:select>
            </flux:field>

            <flux:field class="w-full lg:w-40">
                <flux:label>Per page</flux:label>
                <flux:select wire:model.live="perPage">
                    <flux:select.option value="15">15</flux:select.option>
                    <flux:select.option value="30">30</flux:select.option>
                    <flux:select.option value="50">50</flux:select.option>
                </flux:select>
            </flux:field>
        </div>

        <flux:table :paginate="$units">
            <thead data-flux-columns>
                <flux:table.row>
                    <flux:table.column>Name</flux:table.column>
                    <flux:table.column>Code</flux:table.column>
                    <flux:table.column align="end">Adults</flux:table.column>
                    <flux:table.column align="end">Children</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Housekeeping</flux:table.column>
                </flux:table.row>
            </thead>
            <tbody data-flux-rows>
                @forelse ($units as $unit)
                    <flux:table.row :key="$unit->id">
                        <flux:table.cell variant="strong">
                            <flux:link
                                href="{{ route('partner.catalog.products.units.show', [$product, $unit]) }}"
                                wire:navigate
                            >
                                {{ $unit->name }}
                            </flux:link>
                        </flux:table.cell>
                        <flux:table.cell>{{ $unit->code ?? '—' }}</flux:table.cell>
                        <flux:table.cell align="end">{{ $unit->occupancy_adults }}</flux:table.cell>
                        <flux:table.cell align="end">{{ $unit->occupancy_children }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$unit->status === 'active' ? 'green' : 'red'">
                                {{ ucfirst($unit->status) }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>{{ $unit->housekeeping_required ? 'Required' : 'Optional' }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6">
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                No units match the current filters.
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </tbody>
        </flux:table>
    </flux:card>
</div>
