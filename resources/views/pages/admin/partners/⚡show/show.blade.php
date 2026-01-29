@php
    $partner = $this->partner;
@endphp

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="{{ route('admin.partners.index') }}" wire:navigate>
                Partners
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item>{{ $partner->name }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <flux:heading size="lg">{{ $partner->name }}</flux:heading>
        <flux:text>Account status, catalog coverage, and API access keys.</flux:text>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <flux:card class="space-y-4 lg:col-span-2">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Slug</flux:text>
                    <flux:text>{{ $partner->slug }}</flux:text>
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Status</flux:text>
                    <flux:badge :color="$this->statusColor($partner->status)">
                        {{ ucfirst($partner->status) }}
                    </flux:badge>
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Billing email</flux:text>
                    <flux:text>{{ $partner->billing_email ?? '—' }}</flux:text>
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Currency</flux:text>
                    <flux:text>{{ $partner->currency ?? '—' }}</flux:text>
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Timezone</flux:text>
                    <flux:text>{{ $partner->timezone ?? '—' }}</flux:text>
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Created</flux:text>
                    <flux:text>{{ $partner->created_at?->format('M d, Y') }}</flux:text>
                </div>
            </div>
        </flux:card>

        <flux:card class="space-y-4">
            <flux:heading size="sm">Coverage</flux:heading>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <flux:text>Locations</flux:text>
                    <flux:badge color="zinc">{{ $partner->locations_count }}</flux:badge>
                </div>
                <div class="flex items-center justify-between">
                    <flux:text>Products</flux:text>
                    <flux:badge color="zinc">{{ $partner->products_count }}</flux:badge>
                </div>
                <div class="flex items-center justify-between">
                    <flux:text>Bookings</flux:text>
                    <flux:badge color="zinc">{{ $partner->bookings_count }}</flux:badge>
                </div>
                <div class="flex items-center justify-between">
                    <flux:text>Staff users</flux:text>
                    <flux:badge color="zinc">{{ $partner->users_count }}</flux:badge>
                </div>
                <div class="flex items-center justify-between">
                    <flux:text>API clients</flux:text>
                    <flux:badge color="zinc">{{ $partner->api_clients_count }}</flux:badge>
                </div>
            </div>
        </flux:card>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <flux:card class="space-y-4 lg:col-span-2">
            <div class="flex flex-col gap-1">
                <flux:heading size="sm">API clients</flux:heading>
                <flux:text class="text-sm">Active credentials issued for this partner.</flux:text>
            </div>

            <flux:table>
                <thead data-flux-columns>
                    <flux:table.row>
                        <flux:table.column>Client ID</flux:table.column>
                        <flux:table.column>Status</flux:table.column>
                        <flux:table.column>Scopes</flux:table.column>
                        <flux:table.column>Last used</flux:table.column>
                        <flux:table.column>Created</flux:table.column>
                    </flux:table.row>
                </thead>
                <tbody data-flux-rows>
                    @forelse ($partner->apiClients as $apiClient)
                        <flux:table.row :key="$apiClient->id">
                            <flux:table.cell variant="strong">{{ $apiClient->client_id }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$this->statusColor($apiClient->status)">
                                    {{ ucfirst($apiClient->status) }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex flex-wrap gap-2">
                                    @forelse ($apiClient->scopes ?? [] as $scope)
                                        <flux:badge size="sm" color="zinc">{{ $scope }}</flux:badge>
                                    @empty
                                        <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">—</flux:text>
                                    @endforelse
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>{{ $apiClient->last_used_at?->format('M d, Y H:i') ?? '—' }}</flux:table.cell>
                            <flux:table.cell>{{ $apiClient->created_at?->format('M d, Y') }}</flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5">
                                <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                    No API clients issued yet.
                                </flux:text>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </tbody>
            </flux:table>
        </flux:card>

        <flux:card class="space-y-4">
            <div class="flex flex-col gap-1">
                <flux:heading size="sm">Issue API client</flux:heading>
                <flux:text class="text-sm">Generate credentials for partner integrations.</flux:text>
            </div>

            @if ($issuedSecret)
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-900 dark:border-emerald-500/40 dark:bg-emerald-500/10 dark:text-emerald-100">
                    <div class="font-semibold">Client secret issued</div>
                    <div class="mt-1 break-all font-mono text-xs">{{ $issuedSecret }}</div>
                </div>
            @endif

            <form wire:submit="issueApiClient" class="space-y-4">
                <flux:field>
                    <flux:label>Client ID</flux:label>
                    <flux:input wire:model.live="clientId" placeholder="partner-app" />
                    <flux:error name="client_id" />
                </flux:field>

                <flux:field>
                    <flux:label>Client secret (optional)</flux:label>
                    <flux:input wire:model.live="clientSecret" placeholder="Leave blank to auto-generate" />
                    <flux:error name="client_secret" />
                </flux:field>

                <flux:field>
                    <flux:label>Scopes</flux:label>
                    <flux:input wire:model.live="scopes" placeholder="bookings:read, bookings:write" />
                    <flux:text class="text-xs text-zinc-500 dark:text-zinc-300">Comma-separated list of scopes.</flux:text>
                    <flux:error name="scopes" />
                    <flux:error name="scopes.*" />
                </flux:field>

                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select wire:model.live="status">
                        <flux:select.option value="active">Active</flux:select.option>
                        <flux:select.option value="inactive">Inactive</flux:select.option>
                    </flux:select>
                    <flux:error name="status" />
                </flux:field>

                <flux:button type="submit" variant="primary">Issue API client</flux:button>
            </form>
        </flux:card>
    </div>
</div>
