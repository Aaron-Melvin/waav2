<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:partner-admin|partner-staff', 'resolve.partner', 'require.partner'])
    ->prefix('partner')
    ->name('partner.')
    ->group(function (): void {
        Route::livewire('catalog/products', 'pages::partner.catalog.products.index')->name('catalog.products.index');
        Route::livewire('catalog/accommodations/create', 'pages::partner.catalog.accommodations.create')
            ->name('catalog.accommodations.create');
        Route::livewire('catalog/products/{product}', 'pages::partner.catalog.products.show')->name('catalog.products.show');
        Route::livewire('catalog/products/{product}/rate-plans', 'pages::partner.catalog.products.rate-plans.index')
            ->name('catalog.products.rate-plans.index');
        Route::livewire('catalog/products/{product}/rate-plans/{ratePlan}', 'pages::partner.catalog.products.rate-plans.show')
            ->name('catalog.products.rate-plans.show');
        Route::livewire('catalog/products/{product}/units', 'pages::partner.catalog.products.units.index')
            ->name('catalog.products.units.index');
        Route::livewire('catalog/products/{product}/units/{unit}', 'pages::partner.catalog.products.units.show')
            ->name('catalog.products.units.show');
        Route::livewire('catalog/locations', 'pages::partner.catalog.locations.index')->name('catalog.locations.index');
        Route::livewire('catalog/locations/{location}', 'pages::partner.catalog.locations.show')->name('catalog.locations.show');
        Route::livewire('availability/events', 'pages::partner.availability.events.index')->name('availability.events.index');
        Route::livewire('availability/events/{event}', 'pages::partner.availability.events.show')->name('availability.events.show');
        Route::livewire('availability/events/{event}/overrides', 'pages::partner.availability.events.overrides')
            ->name('availability.events.overrides');
        Route::livewire('availability/blackouts', 'pages::partner.availability.blackouts.index')
            ->name('availability.blackouts.index');
        Route::livewire('availability/blackouts/{blackout}', 'pages::partner.availability.blackouts.show')
            ->name('availability.blackouts.show');
        Route::livewire('policies/cancellation', 'pages::partner.policies.cancellation.index')
            ->name('policies.cancellation.index');
        Route::livewire('policies/taxes', 'pages::partner.policies.taxes.index')
            ->name('policies.taxes.index');
        Route::livewire('policies/fees', 'pages::partner.policies.fees.index')
            ->name('policies.fees.index');
    });
