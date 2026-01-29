<?php
    $partner = $this->partner;
?>

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="<?php echo e(route('admin.partners.index')); ?>" wire:navigate>
                Partners
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item><?php echo e($partner->name); ?></flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <flux:heading size="lg"><?php echo e($partner->name); ?></flux:heading>
        <flux:text>Account status, catalog coverage, and API access keys.</flux:text>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <flux:card class="space-y-4 lg:col-span-2">
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Slug</flux:text>
                    <flux:text><?php echo e($partner->slug); ?></flux:text>
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Status</flux:text>
                    <flux:badge :color="$this->statusColor($partner->status)">
                        <?php echo e(ucfirst($partner->status)); ?>

                    </flux:badge>
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Billing email</flux:text>
                    <flux:text><?php echo e($partner->billing_email ?? '—'); ?></flux:text>
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Currency</flux:text>
                    <flux:text><?php echo e($partner->currency ?? '—'); ?></flux:text>
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Timezone</flux:text>
                    <flux:text><?php echo e($partner->timezone ?? '—'); ?></flux:text>
                </div>
                <div class="space-y-1">
                    <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Created</flux:text>
                    <flux:text><?php echo e($partner->created_at?->format('M d, Y')); ?></flux:text>
                </div>
            </div>
        </flux:card>

        <flux:card class="space-y-4">
            <flux:heading size="sm">Coverage</flux:heading>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <flux:text>Locations</flux:text>
                    <flux:badge color="zinc"><?php echo e($partner->locations_count); ?></flux:badge>
                </div>
                <div class="flex items-center justify-between">
                    <flux:text>Products</flux:text>
                    <flux:badge color="zinc"><?php echo e($partner->products_count); ?></flux:badge>
                </div>
                <div class="flex items-center justify-between">
                    <flux:text>Bookings</flux:text>
                    <flux:badge color="zinc"><?php echo e($partner->bookings_count); ?></flux:badge>
                </div>
                <div class="flex items-center justify-between">
                    <flux:text>Staff users</flux:text>
                    <flux:badge color="zinc"><?php echo e($partner->users_count); ?></flux:badge>
                </div>
                <div class="flex items-center justify-between">
                    <flux:text>API clients</flux:text>
                    <flux:badge color="zinc"><?php echo e($partner->api_clients_count); ?></flux:badge>
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
                    <?php $__empty_1 = true; $__currentLoopData = $partner->apiClients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $apiClient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <flux:table.row :key="$apiClient->id">
                            <flux:table.cell variant="strong"><?php echo e($apiClient->client_id); ?></flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$this->statusColor($apiClient->status)">
                                    <?php echo e(ucfirst($apiClient->status)); ?>

                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex flex-wrap gap-2">
                                    <?php $__empty_2 = true; $__currentLoopData = $apiClient->scopes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $scope): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                        <flux:badge size="sm" color="zinc"><?php echo e($scope); ?></flux:badge>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                        <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">—</flux:text>
                                    <?php endif; ?>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell><?php echo e($apiClient->last_used_at?->format('M d, Y H:i') ?? '—'); ?></flux:table.cell>
                            <flux:table.cell><?php echo e($apiClient->created_at?->format('M d, Y')); ?></flux:table.cell>
                        </flux:table.row>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <flux:table.row>
                            <flux:table.cell colspan="5">
                                <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                    No API clients issued yet.
                                </flux:text>
                            </flux:table.cell>
                        </flux:table.row>
                    <?php endif; ?>
                </tbody>
            </flux:table>
        </flux:card>

        <flux:card class="space-y-4">
            <div class="flex flex-col gap-1">
                <flux:heading size="sm">Issue API client</flux:heading>
                <flux:text class="text-sm">Generate credentials for partner integrations.</flux:text>
            </div>

            <?php if($issuedSecret): ?>
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-900 dark:border-emerald-500/40 dark:bg-emerald-500/10 dark:text-emerald-100">
                    <div class="font-semibold">Client secret issued</div>
                    <div class="mt-1 break-all font-mono text-xs"><?php echo e($issuedSecret); ?></div>
                </div>
            <?php endif; ?>

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
</div><?php /**PATH /home/kevin/Projects/waav2/storage/framework/views/livewire-testing/views/94ad6360.blade.php ENDPATH**/ ?>