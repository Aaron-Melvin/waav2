<?php
    $product = $this->product;
    $ratePlan = $this->ratePlan;
    $prices = $this->prices;
    $cancellationPolicies = $this->cancellationPolicies;
?>

<div class="flex flex-col gap-6">
    <div class="flex flex-col gap-2">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item href="<?php echo e(route('partner.catalog.products.index')); ?>" wire:navigate>
                Catalog products
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="<?php echo e(route('partner.catalog.products.show', $product)); ?>" wire:navigate>
                <?php echo e($product->name); ?>

            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item href="<?php echo e(route('partner.catalog.products.rate-plans.index', $product)); ?>" wire:navigate>
                Rate plans
            </flux:breadcrumbs.item>
            <flux:breadcrumbs.item><?php echo e($ratePlan->name); ?></flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <flux:heading size="lg"><?php echo e($ratePlan->name); ?></flux:heading>
        <flux:text>Update rate plan settings and pricing windows.</flux:text>
    </div>

    <flux:card class="space-y-4">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Status</flux:text>
                <flux:badge :color="$ratePlan->status === 'active' ? 'green' : 'red'">
                    <?php echo e(ucfirst($ratePlan->status ?? '—')); ?>

                </flux:badge>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Pricing model</flux:text>
                <flux:text><?php echo e(ucfirst($ratePlan->pricing_model ?? '—')); ?></flux:text>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Currency</flux:text>
                <flux:text><?php echo e($ratePlan->currency ?? '—'); ?></flux:text>
            </div>
            <div class="space-y-1">
                <flux:text class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-300">Cancellation policy</flux:text>
                <flux:text><?php echo e($ratePlan->cancellationPolicy?->name ?? '—'); ?></flux:text>
            </div>
        </div>
    </flux:card>

    <flux:card class="space-y-5">
        <div class="flex flex-col gap-1">
            <flux:heading size="sm">Rate plan settings</flux:heading>
            <flux:text class="text-sm">Adjust pricing model, currency, and status.</flux:text>
        </div>

        <?php if($savedMessage): ?>
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:border-emerald-500/40 dark:bg-emerald-500/10 dark:text-emerald-100">
                <?php echo e($savedMessage); ?>

            </div>
        <?php endif; ?>

        <form wire:submit="updateRatePlan" class="space-y-6">
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label>Name</flux:label>
                    <flux:input wire:model.live="name" />
                    <flux:error name="name" />
                </flux:field>

                <flux:field>
                    <flux:label>Code</flux:label>
                    <flux:input wire:model.live="code" />
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
                    <flux:input wire:model.live="currency" />
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
                        <?php $__currentLoopData = $cancellationPolicies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $policy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <flux:select.option value="<?php echo e($policy->id); ?>">
                                <?php echo e($policy->name); ?>

                            </flux:select.option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </flux:select>
                    <flux:error name="cancellation_policy_id" />
                </flux:field>
            </div>

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">Save changes</flux:button>
            </div>
        </form>
    </flux:card>

    <flux:card class="space-y-5">
        <div class="flex flex-col gap-1">
            <flux:heading size="sm">Price windows</flux:heading>
            <flux:text class="text-sm">Add nightly pricing for specific date ranges.</flux:text>
        </div>

        <?php if($priceMessage): ?>
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:border-emerald-500/40 dark:bg-emerald-500/10 dark:text-emerald-100">
                <?php echo e($priceMessage); ?>

            </div>
        <?php endif; ?>

        <?php if($editMessage): ?>
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 dark:border-emerald-500/40 dark:bg-emerald-500/10 dark:text-emerald-100">
                <?php echo e($editMessage); ?>

            </div>
        <?php endif; ?>

        <?php if($editingPriceId): ?>
            <form wire:submit="updatePrice" class="space-y-6">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <flux:field>
                        <flux:label>Starts on</flux:label>
                        <flux:input type="date" wire:model.live="editStartsOn" />
                        <flux:error name="starts_on" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Ends on</flux:label>
                        <flux:input type="date" wire:model.live="editEndsOn" />
                        <flux:error name="ends_on" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Nightly price</flux:label>
                        <flux:input type="number" step="0.01" min="0" wire:model.live="editPrice" />
                        <flux:error name="price" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Extra adult</flux:label>
                        <flux:input type="number" step="0.01" min="0" wire:model.live="editExtraAdult" />
                        <flux:error name="extra_adult" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Extra child</flux:label>
                        <flux:input type="number" step="0.01" min="0" wire:model.live="editExtraChild" />
                        <flux:error name="extra_child" />
                    </flux:field>

                    <flux:field class="lg:col-span-3">
                        <flux:label>Restrictions (JSON)</flux:label>
                        <flux:textarea wire:model.live="editRestrictions" rows="3" placeholder='{"min_stay":2,"max_stay":5}' />
                        <flux:error name="restrictions" />
                    </flux:field>
                </div>

                <div class="flex justify-end gap-3">
                    <flux:button type="button" variant="ghost" wire:click="cancelEditingPrice">
                        Cancel
                    </flux:button>
                    <flux:button type="submit" variant="primary">Update price window</flux:button>
                </div>
            </form>
        <?php else: ?>
            <form wire:submit="addPrice" class="space-y-6">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <flux:field>
                        <flux:label>Starts on</flux:label>
                        <flux:input type="date" wire:model.live="startsOn" />
                        <flux:error name="starts_on" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Ends on</flux:label>
                        <flux:input type="date" wire:model.live="endsOn" />
                        <flux:error name="ends_on" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Nightly price</flux:label>
                        <flux:input type="number" step="0.01" min="0" wire:model.live="price" />
                        <flux:error name="price" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Extra adult</flux:label>
                        <flux:input type="number" step="0.01" min="0" wire:model.live="extraAdult" />
                        <flux:error name="extra_adult" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Extra child</flux:label>
                        <flux:input type="number" step="0.01" min="0" wire:model.live="extraChild" />
                        <flux:error name="extra_child" />
                    </flux:field>

                    <flux:field class="lg:col-span-3">
                        <flux:label>Restrictions (JSON)</flux:label>
                        <flux:textarea wire:model.live="restrictions" rows="3" placeholder='{"min_stay":2,"max_stay":5}' />
                        <flux:error name="restrictions" />
                    </flux:field>
                </div>

                <div class="flex justify-end">
                    <flux:button type="submit" variant="primary">Add price window</flux:button>
                </div>
            </form>
        <?php endif; ?>

        <div class="h-px bg-zinc-200 dark:bg-zinc-700"></div>

        <flux:table>
            <thead data-flux-columns>
                <flux:table.row>
                    <flux:table.column>Range</flux:table.column>
                    <flux:table.column align="end">Nightly</flux:table.column>
                    <flux:table.column align="end">Extra adult</flux:table.column>
                    <flux:table.column align="end">Extra child</flux:table.column>
                    <flux:table.column>Restrictions</flux:table.column>
                    <flux:table.column align="end">Actions</flux:table.column>
                </flux:table.row>
            </thead>
            <tbody data-flux-rows>
                <?php $__empty_1 = true; $__currentLoopData = $prices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ratePrice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <flux:table.row :key="$ratePrice->id">
                        <flux:table.cell variant="strong">
                            <?php echo e($ratePrice->starts_on?->format('M d, Y')); ?> - <?php echo e($ratePrice->ends_on?->format('M d, Y')); ?>

                        </flux:table.cell>
                        <flux:table.cell align="end"><?php echo e(number_format((float) $ratePrice->price, 2)); ?></flux:table.cell>
                        <flux:table.cell align="end"><?php echo e($ratePrice->extra_adult !== null ? number_format((float) $ratePrice->extra_adult, 2) : '—'); ?></flux:table.cell>
                        <flux:table.cell align="end"><?php echo e($ratePrice->extra_child !== null ? number_format((float) $ratePrice->extra_child, 2) : '—'); ?></flux:table.cell>
                        <flux:table.cell>
                            <flux:text class="text-xs text-zinc-500 dark:text-zinc-300 whitespace-pre-wrap">
                                <?php echo e($this->formatRestrictions($ratePrice->restrictions)); ?>

                            </flux:text>
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            <div class="flex justify-end gap-2">
                                <flux:button
                                    variant="ghost"
                                    size="sm"
                                    wire:click="startEditingPrice('<?php echo e($ratePrice->id); ?>')"
                                >
                                    Edit
                                </flux:button>
                                <flux:button
                                    variant="ghost"
                                    size="sm"
                                    wire:click="deletePrice('<?php echo e($ratePrice->id); ?>')"
                                >
                                    Delete
                                </flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <flux:table.row>
                        <flux:table.cell colspan="6">
                            <flux:text class="text-sm text-zinc-500 dark:text-zinc-300">
                                No pricing windows added yet.
                            </flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                <?php endif; ?>
            </tbody>
        </flux:table>
    </flux:card>
</div><?php /**PATH /home/kevin/Projects/waav2/storage/framework/views/livewire-testing/views/9a172672.blade.php ENDPATH**/ ?>