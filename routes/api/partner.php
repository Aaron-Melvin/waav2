<?php

use App\Http\Controllers\Api\Partner\ApiClientController;
use App\Http\Controllers\Api\Partner\BookingController;
use App\Http\Controllers\Api\Partner\CalendarSyncAccountController;
use App\Http\Controllers\Api\Partner\CancellationPolicyController;
use App\Http\Controllers\Api\Partner\EventBlackoutController;
use App\Http\Controllers\Api\Partner\EventController;
use App\Http\Controllers\Api\Partner\EventOverrideController;
use App\Http\Controllers\Api\Partner\EventSeriesController;
use App\Http\Controllers\Api\Partner\EligibilityRuleController;
use App\Http\Controllers\Api\Partner\CouponController;
use App\Http\Controllers\Api\Partner\FeeController;
use App\Http\Controllers\Api\Partner\IcalFeedController;
use App\Http\Controllers\Api\Partner\InvoiceController;
use App\Http\Controllers\Api\Partner\LocationController;
use App\Http\Controllers\Api\Partner\NotificationTemplateController;
use App\Http\Controllers\Api\Partner\PartnerController;
use App\Http\Controllers\Api\Partner\PaymentController;
use App\Http\Controllers\Api\Partner\ProductController;
use App\Http\Controllers\Api\Partner\RatePlanController;
use App\Http\Controllers\Api\Partner\RatePlanPriceController;
use App\Http\Controllers\Api\Partner\StaffInvitationController;
use App\Http\Controllers\Api\Partner\TaxController;
use App\Http\Controllers\Api\Partner\UnitCalendarController;
use App\Http\Controllers\Api\Partner\UnitController;
use App\Http\Controllers\Api\Partner\WebhookController;
use App\Http\Controllers\Api\Partner\WebhookDeliveryController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/partner')
    ->middleware(['api.client', 'require.partner', 'idempotency'])
    ->name('api.partner.')
    ->group(function (): void {
        Route::get('partner', [PartnerController::class, 'show'])->name('partner.show');
        Route::post('api-clients', [ApiClientController::class, 'store'])->name('api-clients.store');

        Route::get('locations', [LocationController::class, 'index'])->name('locations.index');
        Route::post('locations', [LocationController::class, 'store'])->name('locations.store');
        Route::get('locations/{location}', [LocationController::class, 'show'])->name('locations.show');
        Route::patch('locations/{location}', [LocationController::class, 'update'])->name('locations.update');

        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        Route::post('products', [ProductController::class, 'store'])->name('products.store');
        Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
        Route::patch('products/{product}', [ProductController::class, 'update'])->name('products.update');

        Route::get('products/{product}/units', [UnitController::class, 'index'])->name('units.index');
        Route::post('products/{product}/units', [UnitController::class, 'store'])->name('units.store');
        Route::get('products/{product}/units/{unit}', [UnitController::class, 'show'])->name('units.show');
        Route::patch('products/{product}/units/{unit}', [UnitController::class, 'update'])->name('units.update');
        Route::get('products/{product}/units/{unit}/calendar', [UnitCalendarController::class, 'index'])
            ->name('units.calendar.index');
        Route::post('products/{product}/units/{unit}/calendar', [UnitCalendarController::class, 'store'])
            ->name('units.calendar.store');

        Route::get('products/{product}/rate-plans', [RatePlanController::class, 'index'])->name('rate-plans.index');
        Route::post('products/{product}/rate-plans', [RatePlanController::class, 'store'])->name('rate-plans.store');
        Route::get('products/{product}/rate-plans/{ratePlan}', [RatePlanController::class, 'show'])
            ->name('rate-plans.show');
        Route::patch('products/{product}/rate-plans/{ratePlan}', [RatePlanController::class, 'update'])
            ->name('rate-plans.update');
        Route::get('products/{product}/rate-plans/{ratePlan}/prices', [RatePlanPriceController::class, 'index'])
            ->name('rate-plans.prices.index');
        Route::post('products/{product}/rate-plans/{ratePlan}/prices', [RatePlanPriceController::class, 'store'])
            ->name('rate-plans.prices.store');
        Route::patch('products/{product}/rate-plans/{ratePlan}/prices/{ratePlanPrice}', [RatePlanPriceController::class, 'update'])
            ->name('rate-plans.prices.update');

        Route::get('event-series', [EventSeriesController::class, 'index'])->name('event-series.index');
        Route::post('event-series', [EventSeriesController::class, 'store'])->name('event-series.store');
        Route::get('event-series/{eventSeries}', [EventSeriesController::class, 'show'])->name('event-series.show');
        Route::patch('event-series/{eventSeries}', [EventSeriesController::class, 'update'])->name('event-series.update');
        Route::post('event-series/{eventSeries}/generate', [EventSeriesController::class, 'generate'])
            ->name('event-series.generate');

        Route::get('events', [EventController::class, 'index'])->name('events.index');
        Route::post('events', [EventController::class, 'store'])->name('events.store');
        Route::get('events/{event}', [EventController::class, 'show'])->name('events.show');
        Route::patch('events/{event}', [EventController::class, 'update'])->name('events.update');
        Route::get('events/{event}/overrides', [EventOverrideController::class, 'index'])
            ->name('events.overrides.index');
        Route::post('events/{event}/overrides', [EventOverrideController::class, 'store'])
            ->name('events.overrides.store');

        Route::get('blackouts', [EventBlackoutController::class, 'index'])->name('blackouts.index');
        Route::post('blackouts', [EventBlackoutController::class, 'store'])->name('blackouts.store');
        Route::get('blackouts/{blackout}', [EventBlackoutController::class, 'show'])->name('blackouts.show');
        Route::patch('blackouts/{blackout}', [EventBlackoutController::class, 'update'])->name('blackouts.update');

        Route::get('cancellation-policies', [CancellationPolicyController::class, 'index'])
            ->name('cancellation-policies.index');
        Route::post('cancellation-policies', [CancellationPolicyController::class, 'store'])
            ->name('cancellation-policies.store');
        Route::get('cancellation-policies/{cancellationPolicy}', [CancellationPolicyController::class, 'show'])
            ->name('cancellation-policies.show');
        Route::patch('cancellation-policies/{cancellationPolicy}', [CancellationPolicyController::class, 'update'])
            ->name('cancellation-policies.update');

        Route::get('eligibility-rules', [EligibilityRuleController::class, 'index'])
            ->name('eligibility-rules.index');
        Route::post('eligibility-rules', [EligibilityRuleController::class, 'store'])
            ->name('eligibility-rules.store');
        Route::get('eligibility-rules/{eligibilityRule}', [EligibilityRuleController::class, 'show'])
            ->name('eligibility-rules.show');
        Route::patch('eligibility-rules/{eligibilityRule}', [EligibilityRuleController::class, 'update'])
            ->name('eligibility-rules.update');

        Route::get('coupons', [CouponController::class, 'index'])->name('coupons.index');
        Route::post('coupons', [CouponController::class, 'store'])->name('coupons.store');
        Route::get('coupons/{coupon}', [CouponController::class, 'show'])->name('coupons.show');
        Route::patch('coupons/{coupon}', [CouponController::class, 'update'])->name('coupons.update');

        Route::get('taxes', [TaxController::class, 'index'])->name('taxes.index');
        Route::post('taxes', [TaxController::class, 'store'])->name('taxes.store');
        Route::get('taxes/{tax}', [TaxController::class, 'show'])->name('taxes.show');
        Route::patch('taxes/{tax}', [TaxController::class, 'update'])->name('taxes.update');

        Route::get('fees', [FeeController::class, 'index'])->name('fees.index');
        Route::post('fees', [FeeController::class, 'store'])->name('fees.store');
        Route::get('fees/{fee}', [FeeController::class, 'show'])->name('fees.show');
        Route::patch('fees/{fee}', [FeeController::class, 'update'])->name('fees.update');

        Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::patch('bookings/{booking}', [BookingController::class, 'update'])->name('bookings.update');

        Route::get('staff-invitations', [StaffInvitationController::class, 'index'])
            ->name('staff-invitations.index');
        Route::post('staff-invitations', [StaffInvitationController::class, 'store'])
            ->name('staff-invitations.store');

        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');

        Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');

        Route::get('notification-templates', [NotificationTemplateController::class, 'index'])
            ->name('notification-templates.index');
        Route::post('notification-templates', [NotificationTemplateController::class, 'store'])
            ->name('notification-templates.store');
        Route::patch('notification-templates/{template}', [NotificationTemplateController::class, 'update'])
            ->name('notification-templates.update');

        Route::get('webhooks', [WebhookController::class, 'index'])->name('webhooks.index');
        Route::post('webhooks', [WebhookController::class, 'store'])->name('webhooks.store');
        Route::patch('webhooks/{webhook}', [WebhookController::class, 'update'])->name('webhooks.update');
        Route::get('webhooks/{webhook}/deliveries', [WebhookDeliveryController::class, 'index'])
            ->name('webhooks.deliveries.index');

        Route::get('ical-feeds', [IcalFeedController::class, 'index'])->name('ical-feeds.index');
        Route::post('ical-feeds', [IcalFeedController::class, 'store'])->name('ical-feeds.store');

        Route::get('calendar-sync-accounts', [CalendarSyncAccountController::class, 'index'])
            ->name('calendar-sync-accounts.index');
        Route::post('calendar-sync-accounts', [CalendarSyncAccountController::class, 'store'])
            ->name('calendar-sync-accounts.store');
    });
