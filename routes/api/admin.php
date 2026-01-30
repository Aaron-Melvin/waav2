<?php

use App\Http\Controllers\Api\Admin\CancellationPolicyController;
use App\Http\Controllers\Api\Admin\FeeController;
use App\Http\Controllers\Api\Admin\PartnerApiClientController;
use App\Http\Controllers\Api\Admin\PartnerController;
use App\Http\Controllers\Api\Admin\PaymentRefundController;
use App\Http\Controllers\Api\Admin\TaxController;
use Illuminate\Support\Facades\Route;
use Laravel\Pennant\Middleware\EnsureFeaturesAreActive;

Route::prefix('v1/admin')
    ->middleware(['auth', 'role:super-admin', 'idempotency'])
    ->name('api.admin.')
    ->group(function (): void {
        Route::get('partners', [PartnerController::class, 'index'])->name('partners.index');
        Route::get('partners/pending', [PartnerController::class, 'pending'])->name('partners.pending');
        Route::post('partners', [PartnerController::class, 'store'])->name('partners.store');
        Route::patch('partners/{partner}/status', [PartnerController::class, 'updateStatus'])
            ->name('partners.status.update');
        Route::post('partners/{partner}/api-clients', [PartnerApiClientController::class, 'store'])
            ->name('partners.api-clients.store');

        Route::post('payments/{payment}/refunds', [PaymentRefundController::class, 'store'])
            ->name('payments.refunds.store');

        Route::get('taxes', [TaxController::class, 'index'])->name('taxes.index');
        Route::post('taxes', [TaxController::class, 'store'])->name('taxes.store');
        Route::get('taxes/{tax}', [TaxController::class, 'show'])->name('taxes.show');
        Route::patch('taxes/{tax}', [TaxController::class, 'update'])->name('taxes.update');

        Route::get('fees', [FeeController::class, 'index'])->name('fees.index');
        Route::post('fees', [FeeController::class, 'store'])->name('fees.store');
        Route::get('fees/{fee}', [FeeController::class, 'show'])->name('fees.show');
        Route::patch('fees/{fee}', [FeeController::class, 'update'])->name('fees.update');

        Route::get('cancellation-policies', [CancellationPolicyController::class, 'index'])
            ->name('cancellation-policies.index');
        Route::post('cancellation-policies', [CancellationPolicyController::class, 'store'])
            ->name('cancellation-policies.store');
        Route::get('cancellation-policies/{cancellationPolicy}', [CancellationPolicyController::class, 'show'])
            ->name('cancellation-policies.show');
        Route::patch('cancellation-policies/{cancellationPolicy}', [CancellationPolicyController::class, 'update'])
            ->name('cancellation-policies.update');

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
