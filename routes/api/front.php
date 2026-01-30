<?php

use App\Http\Controllers\Api\Front\AvailabilityController;
use App\Http\Controllers\Api\Front\BookingController;
use App\Http\Controllers\Api\Front\HoldController;
use App\Http\Controllers\Api\Front\PartnerController;
use App\Http\Controllers\Api\Front\PartnerSignupController;
use Illuminate\Support\Facades\Route;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

Route::prefix('v1/front')
    ->name('api.front.')
    ->group(function (): void {
        Route::post('partners/signup', [PartnerSignupController::class, 'store'])
            ->name('partners.signup');

        Route::middleware(['api.client', 'require.partner', 'idempotency'])->group(function (): void {
            Route::get('partner', [PartnerController::class, 'show'])->name('partner.show');

            Route::post('availability/search', [AvailabilityController::class, 'search'])
                ->name('availability.search');
            Route::post('holds', [HoldController::class, 'store'])->name('holds.store');
            Route::post('bookings', [BookingController::class, 'store'])->name('bookings.store');
            Route::post('bookings/{booking}/confirm', [BookingController::class, 'confirm'])
                ->name('bookings.confirm');
            Route::post('bookings/{booking}/cancel', [BookingController::class, 'cancel'])
                ->name('bookings.cancel');

            Route::prefix('events')
                ->middleware(EnsureFeaturesAreActive::using('partner-event-accommodation-split'))
                ->name('events.')
                ->group(function (): void {
                    //
                });

            Route::prefix('accommodations')
                ->middleware(EnsureFeaturesAreActive::using('partner-event-accommodation-split'))
                ->name('accommodations.')
                ->group(function (): void {
                    //
                });
        });
    });
