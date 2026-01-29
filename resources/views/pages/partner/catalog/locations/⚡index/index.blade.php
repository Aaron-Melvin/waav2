@php
    $locations = $this->locations;
@endphp

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <flux:heading size="lg">Catalog locations</flux:heading>
        <flux:text>Review the locations attached to your catalog listings.</flux:text>
    </div>

    <flux:card class="space-y-5">
        <div class="flex items-center justify-between gap-4">
            <div>
                <flux:heading size="lg">Add location</flux:heading>
                <flux:text>Create a new place for your products and events.</flux:text>
            </div>
        </div>

        @if ($this->savedMessage)
            <flux:callout icon="check-circle">
                <flux:callout.heading>{{ $this->savedMessage }}</flux:callout.heading>
            </flux:callout>
        @endif

        <form wire:submit="createLocation" class="grid gap-4 lg:grid-cols-6">
            <flux:field class="lg:col-span-2">
                <flux:label>Name</flux:label>
                <flux:input wire:model.live="createName" placeholder="Lahinch Basecamp" />
                <flux:error name="name" />
            </flux:field>

            <flux:field class="lg:col-span-2">
                <flux:label>Address line 1</flux:label>
                <flux:input wire:model.live="createAddressLine1" placeholder="Harbour Road" />
                <flux:error name="address_line_1" />
            </flux:field>

            <flux:field class="lg:col-span-2">
                <flux:label>Address line 2</flux:label>
                <flux:input wire:model.live="createAddressLine2" placeholder="Unit 4" />
                <flux:error name="address_line_2" />
            </flux:field>

            <flux:field>
                <flux:label>City</flux:label>
                <flux:input wire:model.live="createCity" placeholder="Lahinch" />
                <flux:error name="city" />
            </flux:field>

            <flux:field>
                <flux:label>Region</flux:label>
                <flux:input wire:model.live="createRegion" placeholder="Clare" />
                <flux:error name="region" />
            </flux:field>

            <flux:field>
                <flux:label>Postal code</flux:label>
                <flux:input wire:model.live="createPostalCode" placeholder="V95" />
                <flux:error name="postal_code" />
            </flux:field>

            <flux:field>
                <flux:label>Country code</flux:label>
                <flux:input wire:model.live="createCountryCode" placeholder="IE" />
                <flux:error name="country_code" />
            </flux:field>

            <flux:field>
                <flux:label>Latitude</flux:label>
                <flux:input wire:model.live="createLatitude" placeholder="52.93" />
                <flux:error name="latitude" />
            </flux:field>

            <flux:field>
                <flux:label>Longitude</flux:label>
                <flux:input wire:model.live="createLongitude" placeholder="-9.35" />
                <flux:error name="longitude" />
            </flux:field>

            <flux:field>
                <flux:label>Timezone</flux:label>
                <flux:input wire:model.live="createTimezone" placeholder="Europe/Dublin" />
                <flux:error name="timezone" />
            </flux:field>

            <flux:field>
                <flux:label>Status</flux:label>
                <flux:select wire:model.live="createStatus">
                    <flux:select.option value="active">Active</flux:select.option>
                    <flux:select.option value="inactive">Inactive</flux:select.option>
                </flux:select>
                <flux:error name="status" />
            </flux:field>

            <div class="lg:col-span-6">
                <flux:button type="submit" variant="primary">Create location</flux:button>
            </div>
        </form>
    </flux:card>

    <flux:card class="space-y-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
            <flux:field class="flex-1">
                <flux:label>Search</flux:label>
                <flux:input
                    wire:model.live="search"
                    placeholder="Name, city, or region"
                />
            </flux:field>

            <flux:field class="w-full lg:w-56">
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

        <flux:table :paginate="$locations">
            <thead data-flux-columns>
                <flux:table.row>
                    <flux:table.column>Location</flux:table.column>
                    <flux:table.column>City</flux:table.column>
                    <flux:table.column>Region</flux:table.column>
                    <flux:table.column>Country</flux:table.column>
                    <flux:table.column>Timezone</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                </flux:table.row>
            </thead>
            <tbody data-flux-rows>
                @forelse ($locations as $location)
                    <flux:table.row :key="$location->id">
                        <flux:table.cell variant="strong">
                            <flux:link href="{{ route('partner.catalog.locations.show', $location) }}" wire:navigate>
                                {{ $location->name }}
                            </flux:link>
                        </flux:table.cell>
                        <flux:table.cell>{{ $location->city ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $location->region ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $location->country_code ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $location->timezone ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$this->statusColor($location->status)">
                                {{ ucfirst($location->status) }}
                            </flux:badge>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6">
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                No locations match the current filters.
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </tbody>
        </flux:table>
    </flux:card>
</div>
