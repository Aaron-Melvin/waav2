@php
    $products = $this->products;
@endphp

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <flux:heading size="lg">Products</flux:heading>
        <flux:text>Review partner catalog listings across events and accommodations.</flux:text>
    </div>

    <flux:card class="space-y-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
            <flux:field class="flex-1">
                <flux:label>Search</flux:label>
                <flux:input
                    wire:model.live="search"
                    placeholder="Name, slug, partner, or location"
                />
            </flux:field>

            <flux:field class="w-full lg:w-44">
                <flux:label>Type</flux:label>
                <flux:select wire:model.live="type">
                    <flux:select.option value="all">All</flux:select.option>
                    <flux:select.option value="event">Event</flux:select.option>
                    <flux:select.option value="accommodation">Accommodation</flux:select.option>
                </flux:select>
            </flux:field>

            <flux:field class="w-full lg:w-44">
                <flux:label>Status</flux:label>
                <flux:select wire:model.live="status">
                    <flux:select.option value="all">All</flux:select.option>
                    <flux:select.option value="active">Active</flux:select.option>
                    <flux:select.option value="inactive">Inactive</flux:select.option>
                </flux:select>
            </flux:field>

            <flux:field class="w-full lg:w-44">
                <flux:label>Visibility</flux:label>
                <flux:select wire:model.live="visibility">
                    <flux:select.option value="all">All</flux:select.option>
                    <flux:select.option value="public">Public</flux:select.option>
                    <flux:select.option value="unlisted">Unlisted</flux:select.option>
                    <flux:select.option value="private">Private</flux:select.option>
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

        <flux:table :paginate="$products">
            <thead data-flux-columns>
                <flux:table.row>
                    <flux:table.column>Product</flux:table.column>
                    <flux:table.column>Type</flux:table.column>
                    <flux:table.column>Partner</flux:table.column>
                    <flux:table.column>Location</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Visibility</flux:table.column>
                    <flux:table.column align="end">Capacity</flux:table.column>
                    <flux:table.column>Created</flux:table.column>
                </flux:table.row>
            </thead>
            <tbody data-flux-rows>
                @forelse ($products as $product)
                    <flux:table.row :key="$product->id">
                        <flux:table.cell variant="strong">
                            <flux:link href="{{ route('admin.products.show', $product) }}" wire:navigate>
                                {{ $product->name }}
                            </flux:link>
                        </flux:table.cell>
                        <flux:table.cell>{{ ucfirst($product->type) }}</flux:table.cell>
                        <flux:table.cell>{{ $product->partner?->name }}</flux:table.cell>
                        <flux:table.cell>{{ $product->location?->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$this->statusColor($product->status)">
                                {{ ucfirst($product->status) }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$this->visibilityColor($product->visibility)">
                                {{ ucfirst($product->visibility) }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            {{ $product->capacity_total ?? '—' }}
                        </flux:table.cell>
                        <flux:table.cell>{{ $product->created_at?->format('M d, Y') }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="8">
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                No products match the current filters.
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </tbody>
        </flux:table>
    </flux:card>
</div>
