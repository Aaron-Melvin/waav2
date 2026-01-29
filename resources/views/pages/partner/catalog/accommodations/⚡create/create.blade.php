@php
    $locations = $this->locations;
    $cancellationPolicies = $this->cancellationPolicies;
@endphp

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('partner.catalog.products.index') }}" wire:navigate>
                Catalog products
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Create accommodation</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <flux:heading size="lg">Create accommodation</flux:heading>
        <flux:text>Set up an accommodation with a starter unit, rate plan, price window, and availability.</flux:text>
    </div>

    @if ($savedMessage)
        <flux:callout icon="check-circle">
            <flux:callout.heading>{{ $savedMessage }}</flux:callout.heading>
            @if ($createdProductId)
                <flux:callout.text>
                    <flux:link href="{{ route('partner.catalog.products.show', $createdProductId) }}" wire:navigate>
                        View accommodation details
                    </flux:link>
                </flux:callout.text>
            @endif
        </flux:callout>
    @endif

    <form wire:submit="createAccommodation" class="space-y-6">
        <flux:card class="space-y-4">
            <flux:heading size="sm">Accommodation basics</flux:heading>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <flux:field class="sm:col-span-2">
                    <flux:label>Name</flux:label>
                    <flux:input wire:model.live="productName" placeholder="Seaside Retreat" />
                    <flux:error name="productName" />
                </flux:field>

                <flux:field>
                    <flux:label>Slug (optional)</flux:label>
                    <flux:input wire:model.live="productSlug" placeholder="seaside-retreat" />
                    <flux:error name="productSlug" />
                </flux:field>

                <flux:field class="sm:col-span-2 lg:col-span-3">
                    <flux:label>Description</flux:label>
                    <flux:textarea wire:model.live="productDescription" rows="3" placeholder="Short description for guests." />
                    <flux:error name="productDescription" />
                </flux:field>

                <flux:field>
                    <flux:label>Location</flux:label>
                    <flux:select wire:model.live="productLocationId">
                        <flux:select.option value="">No location</flux:select.option>
                        @foreach ($locations as $location)
                            <flux:select.option value="{{ $location->id }}" wire:key="acc-location-{{ $location->id }}">
                                {{ $location->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="productLocationId" />
                </flux:field>

                <flux:field>
                    <flux:label>Capacity</flux:label>
                    <flux:input type="number" min="1" wire:model.live="productCapacityTotal" />
                    <flux:error name="productCapacityTotal" />
                </flux:field>

                <flux:field>
                    <flux:label>Default currency</flux:label>
                    <flux:input wire:model.live="productDefaultCurrency" placeholder="EUR" />
                    <flux:error name="productDefaultCurrency" />
                </flux:field>

                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select wire:model.live="productStatus">
                        <flux:select.option value="active">Active</flux:select.option>
                        <flux:select.option value="inactive">Inactive</flux:select.option>
                    </flux:select>
                    <flux:error name="productStatus" />
                </flux:field>

                <flux:field>
                    <flux:label>Visibility</flux:label>
                    <flux:select wire:model.live="productVisibility">
                        <flux:select.option value="public">Public</flux:select.option>
                        <flux:select.option value="unlisted">Unlisted</flux:select.option>
                        <flux:select.option value="private">Private</flux:select.option>
                    </flux:select>
                    <flux:error name="productVisibility" />
                </flux:field>

                <flux:field>
                    <flux:label>Lead time (minutes)</flux:label>
                    <flux:input type="number" min="0" wire:model.live="productLeadTimeMinutes" />
                    <flux:error name="productLeadTimeMinutes" />
                </flux:field>

                <flux:field>
                    <flux:label>Cutoff (minutes)</flux:label>
                    <flux:input type="number" min="0" wire:model.live="productCutoffMinutes" />
                    <flux:error name="productCutoffMinutes" />
                </flux:field>
            </div>
        </flux:card>

        <flux:card class="space-y-4">
            <flux:heading size="sm">Starter unit</flux:heading>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <flux:field class="sm:col-span-2">
                    <flux:label>Unit name</flux:label>
                    <flux:input wire:model.live="unitName" placeholder="Ocean View Suite" />
                    <flux:error name="unitName" />
                </flux:field>

                <flux:field>
                    <flux:label>Unit code</flux:label>
                    <flux:input wire:model.live="unitCode" placeholder="OVS-01" />
                    <flux:error name="unitCode" />
                </flux:field>

                <flux:field>
                    <flux:label>Adults</flux:label>
                    <flux:input type="number" min="1" wire:model.live="unitOccupancyAdults" />
                    <flux:error name="unitOccupancyAdults" />
                </flux:field>

                <flux:field>
                    <flux:label>Children</flux:label>
                    <flux:input type="number" min="0" wire:model.live="unitOccupancyChildren" />
                    <flux:error name="unitOccupancyChildren" />
                </flux:field>

                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select wire:model.live="unitStatus">
                        <flux:select.option value="active">Active</flux:select.option>
                        <flux:select.option value="inactive">Inactive</flux:select.option>
                    </flux:select>
                    <flux:error name="unitStatus" />
                </flux:field>

                <flux:field>
                    <flux:label>Housekeeping required</flux:label>
                    <flux:select wire:model.live="unitHousekeepingRequired">
                        <flux:select.option value="1">Required</flux:select.option>
                        <flux:select.option value="0">Not required</flux:select.option>
                    </flux:select>
                    <flux:error name="unitHousekeepingRequired" />
                </flux:field>

                <flux:field class="sm:col-span-2 lg:col-span-3">
                    <flux:label>Unit metadata (JSON)</flux:label>
                    <flux:textarea
                        wire:model.live="unitMetaJson"
                        rows="3"
                        placeholder='{"beds":2,"amenities":["wifi","parking"]}'
                    />
                    <flux:error name="unitMetaJson" />
                </flux:field>
            </div>
        </flux:card>

        <flux:card class="space-y-4">
            <flux:heading size="sm">Starter rate plan</flux:heading>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <flux:field class="sm:col-span-2">
                    <flux:label>Rate plan name</flux:label>
                    <flux:input wire:model.live="ratePlanName" placeholder="Standard rate" />
                    <flux:error name="ratePlanName" />
                </flux:field>

                <flux:field>
                    <flux:label>Rate plan code</flux:label>
                    <flux:input wire:model.live="ratePlanCode" placeholder="STD" />
                    <flux:error name="ratePlanCode" />
                </flux:field>

                <flux:field>
                    <flux:label>Pricing model</flux:label>
                    <flux:select wire:model.live="ratePlanPricingModel">
                        <flux:select.option value="per_night">Per night</flux:select.option>
                        <flux:select.option value="per_person">Per person</flux:select.option>
                    </flux:select>
                    <flux:error name="ratePlanPricingModel" />
                </flux:field>

                <flux:field>
                    <flux:label>Currency</flux:label>
                    <flux:input wire:model.live="ratePlanCurrency" placeholder="EUR" />
                    <flux:error name="ratePlanCurrency" />
                </flux:field>

                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select wire:model.live="ratePlanStatus">
                        <flux:select.option value="active">Active</flux:select.option>
                        <flux:select.option value="inactive">Inactive</flux:select.option>
                    </flux:select>
                    <flux:error name="ratePlanStatus" />
                </flux:field>

                <flux:field>
                    <flux:label>Cancellation policy</flux:label>
                    <flux:select wire:model.live="ratePlanCancellationPolicyId">
                        <flux:select.option value="">No policy</flux:select.option>
                        @foreach ($cancellationPolicies as $policy)
                            <flux:select.option value="{{ $policy->id }}" wire:key="acc-policy-{{ $policy->id }}">
                                {{ $policy->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="ratePlanCancellationPolicyId" />
                </flux:field>
            </div>
        </flux:card>

        <flux:card class="space-y-4">
            <flux:heading size="sm">Starter price window</flux:heading>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <flux:field>
                    <flux:label>Starts on</flux:label>
                    <flux:input type="date" wire:model.live="priceStartsOn" />
                    <flux:error name="priceStartsOn" />
                </flux:field>

                <flux:field>
                    <flux:label>Ends on</flux:label>
                    <flux:input type="date" wire:model.live="priceEndsOn" />
                    <flux:error name="priceEndsOn" />
                </flux:field>

                <flux:field>
                    <flux:label>Nightly price</flux:label>
                    <flux:input type="number" min="0" step="0.01" wire:model.live="priceAmount" />
                    <flux:error name="priceAmount" />
                </flux:field>

                <flux:field>
                    <flux:label>Extra adult</flux:label>
                    <flux:input type="number" min="0" step="0.01" wire:model.live="priceExtraAdult" />
                    <flux:error name="priceExtraAdult" />
                </flux:field>

                <flux:field>
                    <flux:label>Extra child</flux:label>
                    <flux:input type="number" min="0" step="0.01" wire:model.live="priceExtraChild" />
                    <flux:error name="priceExtraChild" />
                </flux:field>

                <flux:field class="sm:col-span-2 lg:col-span-3">
                    <flux:label>Restrictions (JSON)</flux:label>
                    <flux:textarea wire:model.live="priceRestrictions" rows="3" placeholder='{"min_stay":2}' />
                    <flux:error name="priceRestrictions" />
                </flux:field>
            </div>
        </flux:card>

        <flux:card class="space-y-4">
            <div class="flex items-center justify-between">
                <flux:heading size="sm">Initial availability</flux:heading>
                <flux:text class="text-xs text-zinc-500 dark:text-zinc-300">
                    Optional: leave blank to skip.
                </flux:text>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <flux:field>
                    <flux:label>Start date</flux:label>
                    <flux:input type="date" wire:model.live="availabilityStart" />
                    <flux:error name="availabilityStart" />
                </flux:field>

                <flux:field>
                    <flux:label>End date</flux:label>
                    <flux:input type="date" wire:model.live="availabilityEnd" />
                    <flux:error name="availabilityEnd" />
                </flux:field>

                <flux:field>
                    <flux:label>Availability</flux:label>
                    <flux:select wire:model.live="availabilityIsAvailable">
                        <flux:select.option value="1">Available</flux:select.option>
                        <flux:select.option value="0">Unavailable</flux:select.option>
                    </flux:select>
                    <flux:error name="availabilityIsAvailable" />
                </flux:field>

                <flux:field>
                    <flux:label>Min stay (nights)</flux:label>
                    <flux:input type="number" min="1" wire:model.live="availabilityMinStay" />
                    <flux:error name="availabilityMinStay" />
                </flux:field>

                <flux:field>
                    <flux:label>Max stay (nights)</flux:label>
                    <flux:input type="number" min="1" wire:model.live="availabilityMaxStay" />
                    <flux:error name="availabilityMaxStay" />
                </flux:field>

                <flux:field class="sm:col-span-2 lg:col-span-3">
                    <flux:label>Reason (optional)</flux:label>
                    <flux:textarea wire:model.live="availabilityReason" rows="2" placeholder="Seasonal availability" />
                    <flux:error name="availabilityReason" />
                </flux:field>
            </div>
        </flux:card>

        <div class="flex justify-end">
            <flux:button type="submit" variant="primary">Create accommodation</flux:button>
        </div>
    </form>
</div>
