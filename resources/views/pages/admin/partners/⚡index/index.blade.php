@php
    $partners = $this->partners;
@endphp

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <flux:heading size="lg">Partner approvals</flux:heading>
        <flux:text>Review new partner signups, activate accounts, and manage API access readiness.</flux:text>
    </div>

    <flux:card class="space-y-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
            <flux:field class="flex-1">
                <flux:label>Search</flux:label>
                <flux:input
                    wire:model.live="search"
                    placeholder="Name, slug, or billing email"
                />
            </flux:field>

            <flux:field class="w-full lg:w-56">
                <flux:label>Status</flux:label>
                <flux:select wire:model.live="status">
                    <flux:select.option value="pending">Pending</flux:select.option>
                    <flux:select.option value="active">Active</flux:select.option>
                    <flux:select.option value="inactive">Inactive</flux:select.option>
                    <flux:select.option value="all">All</flux:select.option>
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

        <flux:table :paginate="$partners">
            <thead data-flux-columns>
                <flux:table.row>
                    <flux:table.column>Partner</flux:table.column>
                    <flux:table.column>Slug</flux:table.column>
                    <flux:table.column>Billing Email</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Signed Up</flux:table.column>
                    <flux:table.column align="end">Actions</flux:table.column>
                </flux:table.row>
            </thead>
            <tbody data-flux-rows>
                @forelse ($partners as $partner)
                    <flux:table.row :key="$partner->id">
                        <flux:table.cell variant="strong">
                            <flux:link href="{{ route('admin.partners.show', $partner) }}" wire:navigate>
                                {{ $partner->name }}
                            </flux:link>
                        </flux:table.cell>
                        <flux:table.cell>{{ $partner->slug }}</flux:table.cell>
                        <flux:table.cell>{{ $partner->billing_email }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="$this->statusColor($partner->status)">
                                {{ ucfirst($partner->status) }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>{{ $partner->created_at?->format('M d, Y') }}</flux:table.cell>
                        <flux:table.cell align="end">
                            <div class="flex items-center justify-end gap-2">
                                @if ($partner->status !== 'active')
                                    <flux:button
                                        variant="primary"
                                        wire:click="setStatus('{{ $partner->id }}', 'active')"
                                        wire:loading.attr="disabled"
                                    >
                                        Activate
                                    </flux:button>
                                @endif

                                @if ($partner->status === 'active')
                                    <flux:button
                                        variant="ghost"
                                        wire:click="setStatus('{{ $partner->id }}', 'inactive')"
                                        wire:loading.attr="disabled"
                                    >
                                        Deactivate
                                    </flux:button>
                                @endif
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6">
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                No partners match the current filters.
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </tbody>
        </flux:table>
    </flux:card>
</div>
