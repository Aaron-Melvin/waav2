<?php

use Illuminate\Support\Facades\Route;

Route::prefix('front')
    ->name('front.')
    ->group(function (): void {
        Route::livewire('/', 'pages::front.search.index')->name('search');
        Route::livewire('booking/{hold}', 'pages::front.booking.details')->name('booking.details');
        Route::livewire('booking/{booking}/confirm', 'pages::front.booking.confirm')->name('booking.confirm');
    });
