<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-amber-50 text-slate-950 antialiased dark:from-slate-950 dark:via-slate-900 dark:to-slate-900 dark:text-slate-100">
        <div class="relative">
            <header class="relative z-10">
                <div class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-6">
                    <a href="{{ route('home') }}" class="flex items-center gap-3" wire:navigate>
                        <span class="flex size-11 items-center justify-center rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/80 dark:bg-slate-900 dark:ring-slate-700/70">
                            <x-app-logo-icon class="size-7 text-slate-900 dark:text-white" />
                        </span>
                        <span class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-600 dark:text-slate-300">
                            Wild Atlantic Adventures
                        </span>
                    </a>
                    <nav class="hidden items-center gap-6 text-sm font-medium text-slate-600 dark:text-slate-300 md:flex">
                        <flux:link href="{{ route('front.search') }}" wire:navigate>Search</flux:link>
                        <flux:link href="{{ route('home') }}" wire:navigate>Home</flux:link>
                    </nav>
                </div>
            </header>

            <main class="relative mx-auto w-full max-w-6xl px-6 pb-16">
                {{ $slot }}
            </main>

            <footer class="border-t border-slate-200/70 py-10 text-center text-xs text-slate-500 dark:border-slate-700/60 dark:text-slate-400">
                <span>Handcrafted coastal adventures along Ireland’s Wild Atlantic Way.</span>
            </footer>
        </div>
        @fluxScripts
    </body>
</html>
