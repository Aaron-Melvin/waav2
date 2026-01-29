@php
    $product = $this->product;
    $locations = $this->locations;
@endphp

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('partner.catalog.products.index') }}" wire:navigate>
                Catalog products
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ $product->name }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <flux:heading size="lg">{{ $product->name }}</flux:heading>
        <flux:text>Update catalog settings, visibility, and lead times.</flux:text>
    </div>

    @if ($product->type === 'accommodation')
        <flux:card>
            <div class="flex flex-wrap gap-4 text-sm">
                <flux:link href="{{ route('partner.catalog.products.rate-plans.index', $product) }}" wire:navigate>
                    Manage rate plans
                </flux:link>
                <flux:link href="{{ route('partner.catalog.products.units.index', $product) }}" wire:navigate>
                    Manage units
                </flux:link>
            </div>
        </flux:card>
    @endif

    <flux:card class="space-y-4">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
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
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Location</flux:text>
                <flux:text>{{ $product->location?->name ?? '—' }}</flux:text>
            </div>
        </div>
    </flux:card>

    <flux:card class="space-y-5">
        <div class="flex items-center justify-between gap-3">
            <div>
                <flux:heading size="sm">Product settings</flux:heading>
                <flux:text class="text-sm">Adjust visibility, capacity, and lead times.</flux:text>
            </div>
        </div>

        @if ($savedMessage)
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:border-emerald-500/40 dark:bg-emerald-500/10 dark:text-emerald-100">
                {{ $savedMessage }}
            </div>
        @endif

        <form wire:submit="updateProduct" class="space-y-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label>Name</flux:label>
                    <flux:input wire:model.live="name" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Slug</flux:label>
                    <flux:input wire:model.live="slug" />
                    <flux:error name="slug" />
                </flux:field>

                <flux:field class="sm:col-span-2">
                    <flux:label>Description</flux:label>
                    <flux:textarea wire:model.live="description" rows="4" />
                    <flux:error name="description" />
                </flux:field>

                <flux:field>
                    <flux:label>Location</flux:label>
                    <flux:select wire:model.live="locationId">
                        <flux:select.option value="">No location</flux:select.option>
                        @foreach ($locations as $location)
                            <flux:select.option value="{{ $location->id }}">
                                {{ $location->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="location_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select wire:model.live="status">
                        <flux:select.option value="active">Active</flux:select.option>
                        <flux:select.option value="inactive">Inactive</flux:select.option>
                    </flux:select>
                    <flux:error name="status" />
                </flux:field>

                <flux:field>
                    <flux:label>Visibility</flux:label>
                    <flux:select wire:model.live="visibility">
                        <flux:select.option value="public">Public</flux:select.option>
                        <flux:select.option value="unlisted">Unlisted</flux:select.option>
                        <flux:select.option value="private">Private</flux:select.option>
                    </flux:select>
                    <flux:error name="visibility" />
                </flux:field>

                <flux:field>
                    <flux:label>Capacity</flux:label>
                    <flux:input type="number" min="1" wire:model.live="capacityTotal" />
                    <flux:error name="capacity_total" />
                </flux:field>

                <flux:field>
                    <flux:label>Default currency</flux:label>
                    <flux:input wire:model.live="defaultCurrency" placeholder="EUR" />
                    <flux:error name="default_currency" />
                </flux:field>

                <flux:field>
                    <flux:label>Lead time (minutes)</flux:label>
                    <flux:input type="number" min="0" wire:model.live="leadTimeMinutes" />
                    <flux:error name="lead_time_minutes" />
                </flux:field>

                <flux:field>
                    <flux:label>Cutoff (minutes)</flux:label>
                    <flux:input type="number" min="0" wire:model.live="cutoffMinutes" />
                    <flux:error name="cutoff_minutes" />
                </flux:field>
            </div>

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">Save changes</flux:button>
            </div>
        </form>
    </flux:card>
</div>
