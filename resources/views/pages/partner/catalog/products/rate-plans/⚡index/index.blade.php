@php
    $product = $this->product;
    $ratePlans = $this->ratePlans;
    $cancellationPolicies = $this->cancellationPolicies;
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
            <flux:breadcrumbs.item>Rate plans</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <flux:heading size="lg">Rate plans</flux:heading>
        <flux:text>Define pricing models and cancellation policies for this product.</flux:text>
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
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Currency</flux:text>
                <flux:text>{{ $product->default_currency ?? $this->partner->currency }}</flux:text>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Location</flux:text>
                <flux:text>{{ $product->location?->name ?? '—' }}</flux:text>
            </div>
        </div>
    </flux:card>

    <flux:card class="space-y-5">
        <div class="flex flex-col gap-1">
            <flux:heading size="sm">Create rate plan</flux:heading>
            <flux:text class="text-sm">Add a new pricing plan for this product.</flux:text>
        </div>

        @if ($savedMessage)
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:border-emerald-500/40 dark:bg-emerald-500/10 dark:text-emerald-100">
                {{ $savedMessage }}
            </div>
        @endif

        <form wire:submit="createRatePlan" class="space-y-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label>Name</flux:label>
                    <flux:input wire:model.live="name" placeholder="Standard rate" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Code</flux:label>
                    <flux:input wire:model.live="code" placeholder="STD" />
                    <flux:error name="code" />
                </flux:field>

                <flux:field>
                    <flux:label>Pricing model</flux:label>
                    <flux:select wire:model.live="pricingModel">
                        <flux:select.option value="per_night">Per night</flux:select.option>
                        <flux:select.option value="per_person">Per person</flux:select.option>
                    </flux:select>
                    <flux:error name="pricing_model" />
                </flux:field>

                <flux:field>
                    <flux:label>Currency</flux:label>
                    <flux:input wire:model.live="currency" placeholder="EUR" />
                    <flux:error name="currency" />
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
                    <flux:label>Cancellation policy</flux:label>
                    <flux:select wire:model.live="cancellationPolicyId">
                        <flux:select.option value="">No policy</flux:select.option>
                        @foreach ($cancellationPolicies as $policy)
                            <flux:select.option value="{{ $policy->id }}">
                                {{ $policy->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="cancellation_policy_id" />
                </flux:field>
            </div>

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">Create rate plan</flux:button>
            </div>
        </form>
    </flux:card>

    <flux:card class="space-y-4">
        <div class="flex flex-col gap-1">
            <flux:heading size="sm">Existing rate plans</flux:heading>
            <flux:text class="text-sm">Review or update pricing plans for this product.</flux:text>
        </div>

        <flux:table>
            <thead data-flux-columns>
                <flux:table.row>
                    <flux:table.column>Plan</flux:table.column>
                    <flux:table.column>Code</flux:table.column>
                    <flux:table.column>Model</flux:table.column>
                    <flux:table.column>Currency</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column align="end">Price windows</flux:table.column>
                </flux:table.row>
            </thead>
            <tbody data-flux-rows>
                @forelse ($ratePlans as $ratePlan)
                    <flux:table.row :key="$ratePlan->id">
                        <flux:table.cell variant="strong">
                            <flux:link
                                href="{{ route('partner.catalog.products.rate-plans.show', [$product, $ratePlan]) }}"
                                wire:navigate
                            >
                                {{ $ratePlan->name }}
                            </flux:link>
                        </flux:table.cell>
                        <flux:table.cell>{{ $ratePlan->code ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ ucfirst($ratePlan->pricing_model ?? '—') }}</flux:table.cell>
                        <flux:table.cell>{{ $ratePlan->currency ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ ucfirst($ratePlan->status ?? '—') }}</flux:table.cell>
                        <flux:table.cell align="end">{{ $ratePlan->prices_count }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6">
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                No rate plans configured yet.
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </tbody>
        </flux:table>
    </flux:card>
</div>
