@php
    $location = $this->location;
@endphp

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('partner.catalog.locations.index') }}" wire:navigate>
                Catalog locations
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ $location->name }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <flux:heading size="lg">{{ $location->name }}</flux:heading>
        <flux:text>Update location address, timezone, and status.</flux:text>
    </div>

    <flux:card class="space-y-4">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Status</flux:text>
                <flux:badge :color="$this->statusColor($location->status)">
                    {{ ucfirst($location->status) }}
                </flux:badge>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">City</flux:text>
                <flux:text>{{ $location->city ?? '—' }}</flux:text>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Region</flux:text>
                <flux:text>{{ $location->region ?? '—' }}</flux:text>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Timezone</flux:text>
                <flux:text>{{ $location->timezone }}</flux:text>
            </div>
        </div>
    </flux:card>

    <flux:card class="space-y-5">
        <div class="flex flex-col gap-1">
            <flux:heading size="sm">Location settings</flux:heading>
            <flux:text class="text-sm">Keep address and timezone data current.</flux:text>
        </div>

        @if ($savedMessage)
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:border-emerald-500/40 dark:bg-emerald-500/10 dark:text-emerald-100">
                {{ $savedMessage }}
            </div>
        @endif

        <form wire:submit="updateLocation" class="space-y-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label>Name</flux:label>
                    <flux:input wire:model.live="name" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select wire:model.live="status">
                        <flux:select.option value="active">Active</flux:select.option>
                        <flux:select.option value="inactive">Inactive</flux:select.option>
                    </flux:select>
                    <flux:error name="status" />
                </flux:field>

                <flux:field class="sm:col-span-2">
                    <flux:label>Address line 1</flux:label>
                    <flux:input wire:model.live="addressLine1" />
                    <flux:error name="address_line_1" />
                </flux:field>

                <flux:field class="sm:col-span-2">
                    <flux:label>Address line 2</flux:label>
                    <flux:input wire:model.live="addressLine2" />
                    <flux:error name="address_line_2" />
                </flux:field>

                <flux:field>
                    <flux:label>City</flux:label>
                    <flux:input wire:model.live="city" />
                    <flux:error name="city" />
                </flux:field>

                <flux:field>
                    <flux:label>Region</flux:label>
                    <flux:input wire:model.live="region" />
                    <flux:error name="region" />
                </flux:field>

                <flux:field>
                    <flux:label>Postal code</flux:label>
                    <flux:input wire:model.live="postalCode" />
                    <flux:error name="postal_code" />
                </flux:field>

                <flux:field>
                    <flux:label>Country code</flux:label>
                    <flux:input wire:model.live="countryCode" placeholder="IE" />
                    <flux:error name="country_code" />
                </flux:field>

                <flux:field>
                    <flux:label>Latitude</flux:label>
                    <flux:input wire:model.live="latitude" />
                    <flux:error name="latitude" />
                </flux:field>

                <flux:field>
                    <flux:label>Longitude</flux:label>
                    <flux:input wire:model.live="longitude" />
                    <flux:error name="longitude" />
                </flux:field>

                <flux:field>
                    <flux:label>Timezone</flux:label>
                    <flux:input wire:model.live="timezone" placeholder="Europe/Dublin" />
                    <flux:error name="timezone" />
                </flux:field>
            </div>

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">Save changes</flux:button>
            </div>
        </form>
    </flux:card>
</div>
