<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:super-admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::livewire('partners', 'pages::admin.partners.index')->name('partners.index');
        Route::livewire('partners/{partner}', 'pages::admin.partners.show')->name('partners.show');
        Route::livewire('bookings', 'pages::admin.bookings.index')->name('bookings.index');
        Route::livewire('bookings/{booking}', 'pages::admin.bookings.show')->name('bookings.show');
        Route::livewire('payments', 'pages::admin.payments.index')->name('payments.index');
        Route::livewire('payments/{payment}', 'pages::admin.payments.show')->name('payments.show');
        Route::livewire('locations', 'pages::admin.locations.index')->name('locations.index');
        Route::livewire('locations/{location}', 'pages::admin.locations.show')->name('locations.show');
        Route::livewire('products', 'pages::admin.products.index')->name('products.index');
        Route::livewire('products/{product}', 'pages::admin.products.show')->name('products.show');
    });
