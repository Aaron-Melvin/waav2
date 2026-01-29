<flux:dropdown position="bottom" align="start">
    <flux:sidebar.profile
        <?php echo e($attributes->only('name')); ?>

        :initials="auth()->user()->initials()"
        icon:trailing="chevrons-up-down"
        data-test="sidebar-menu-button"
    />

    <flux:menu>
        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
            <flux:avatar
                :name="auth()->user()->name"
                :initials="auth()->user()->initials()"
            />
            <div class="grid flex-1 text-start text-sm leading-tight">
                <flux:heading class="truncate"><?php echo e(auth()->user()->name); ?></flux:heading>
                <flux:text class="truncate"><?php echo e(auth()->user()->email); ?></flux:text>
            </div>
        </div>
        <flux:menu.separator />
        <flux:menu.radio.group>
            <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                <?php echo e(__('Settings')); ?>

            </flux:menu.item>
            <form method="POST" action="<?php echo e(route('logout')); ?>" class="w-full">
                <?php echo csrf_field(); ?>
                <flux:menu.item
                    as="button"
                    type="submit"
                    icon="arrow-right-start-on-rectangle"
                    class="w-full cursor-pointer"
                    data-test="logout-button"
                >
                    <?php echo e(__('Log Out')); ?>

                </flux:menu.item>
            </form>
        </flux:menu.radio.group>
    </flux:menu>
</flux:dropdown>
<?php /**PATH /home/kevin/Projects/waav2/resources/views/components/desktop-user-menu.blade.php ENDPATH**/ ?>