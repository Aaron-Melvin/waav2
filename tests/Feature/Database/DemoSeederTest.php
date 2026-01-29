<?php

use App\Models\ApiClient;
use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\BookingStatusHistory;
use App\Models\CalendarSyncAccount;
use App\Models\CalendarSyncEvent;
use App\Models\CancellationPolicy;
use App\Models\Coupon;
use App\Models\CustomerAccessToken;
use App\Models\EligibilityRule;
use App\Models\Event;
use App\Models\EventSeries;
use App\Models\Fee;
use App\Models\GdprErasureQueue;
use App\Models\Hold;
use App\Models\IcalFeed;
use App\Models\IdempotencyKey;
use App\Models\InventoryLedger;
use App\Models\Invoice;
use App\Models\Location;
use App\Models\NotificationEvent;
use App\Models\NotificationQueue;
use App\Models\NotificationTemplate;
use App\Models\Partner;
use App\Models\Payment;
use App\Models\PlatformUser;
use App\Models\Product;
use App\Models\RatePlan;
use App\Models\RatePlanPrice;
use App\Models\ReportsCache;
use App\Models\SearchIndex;
use App\Models\SmsMessage;
use App\Models\SmsProvider;
use App\Models\StaffInvitation;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\UnitCalendar;
use App\Models\UnitHoldLock;
use App\Models\User;
use App\Models\Webhook;
use App\Models\WebhookDelivery;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('seeds demo data with partners, catalog, pricing, integrations, and bookings', function () {
    $this->seed(\Database\Seeders\DemoSeeder::class);

    expect(Partner::count())->toBeGreaterThan(0);
    expect(Location::count())->toBeGreaterThan(0);
    expect(Product::count())->toBeGreaterThan(0);
    expect(EventSeries::count())->toBeGreaterThan(0);
    expect(Event::count())->toBeGreaterThan(0);
    expect(Unit::count())->toBeGreaterThan(0);
    expect(UnitCalendar::count())->toBeGreaterThan(0);
    expect(Hold::count())->toBeGreaterThan(0);
    expect(UnitHoldLock::count())->toBeGreaterThan(0);
    expect(InventoryLedger::count())->toBeGreaterThan(0);
    expect(IdempotencyKey::count())->toBeGreaterThan(0);
    expect(Booking::count())->toBeGreaterThan(0);
    expect(BookingItem::count())->toBeGreaterThan(0);
    expect(BookingStatusHistory::count())->toBeGreaterThan(0);
    expect(ApiClient::count())->toBeGreaterThan(0);
    expect(User::query()->whereNotNull('partner_id')->count())->toBeGreaterThan(0);

    expect(CancellationPolicy::count())->toBeGreaterThan(0);
    expect(EligibilityRule::count())->toBeGreaterThan(0);
    expect(RatePlan::count())->toBeGreaterThan(0);
    expect(RatePlanPrice::count())->toBeGreaterThan(0);
    expect(Tax::count())->toBeGreaterThan(0);
    expect(Fee::count())->toBeGreaterThan(0);
    expect(Coupon::count())->toBeGreaterThan(0);

    expect(Payment::count())->toBeGreaterThan(0);
    expect(Invoice::count())->toBeGreaterThan(0);

    expect(NotificationTemplate::count())->toBeGreaterThan(0);
    expect(NotificationQueue::count())->toBeGreaterThan(0);
    expect(NotificationEvent::count())->toBeGreaterThan(0);
    expect(SmsProvider::count())->toBeGreaterThan(0);
    expect(SmsMessage::count())->toBeGreaterThan(0);

    expect(Webhook::count())->toBeGreaterThan(0);
    expect(WebhookDelivery::count())->toBeGreaterThan(0);
    expect(IcalFeed::count())->toBeGreaterThan(0);
    expect(CalendarSyncAccount::count())->toBeGreaterThan(0);
    expect(CalendarSyncEvent::count())->toBeGreaterThan(0);

    expect(SearchIndex::count())->toBeGreaterThan(0);
    expect(ReportsCache::count())->toBeGreaterThan(0);

    expect(AuditLog::count())->toBeGreaterThan(0);
    expect(GdprErasureQueue::count())->toBeGreaterThan(0);
    expect(StaffInvitation::count())->toBeGreaterThan(0);
    expect(CustomerAccessToken::count())->toBeGreaterThan(0);
    expect(PlatformUser::count())->toBeGreaterThan(0);
});
