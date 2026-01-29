<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partner extends Model
{
    /** @use HasFactory<\Database\Factories\PartnerFactory> */
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'billing_email',
        'currency',
        'timezone',
        'status',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @return HasMany<ApiClient>
     */
    public function apiClients(): HasMany
    {
        return $this->hasMany(ApiClient::class);
    }

    /**
     * @return HasMany<User>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return HasMany<Location>
     */
    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    /**
     * @return HasMany<Product>
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * @return HasMany<EventSeries>
     */
    public function eventSeries(): HasMany
    {
        return $this->hasMany(EventSeries::class);
    }

    /**
     * @return HasMany<Event>
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * @return HasMany<EventBlackout>
     */
    public function eventBlackouts(): HasMany
    {
        return $this->hasMany(EventBlackout::class);
    }

    /**
     * @return HasMany<CancellationPolicy>
     */
    public function cancellationPolicies(): HasMany
    {
        return $this->hasMany(CancellationPolicy::class);
    }

    /**
     * @return HasMany<EligibilityRule>
     */
    public function eligibilityRules(): HasMany
    {
        return $this->hasMany(EligibilityRule::class);
    }

    /**
     * @return HasMany<RatePlan>
     */
    public function ratePlans(): HasMany
    {
        return $this->hasMany(RatePlan::class);
    }

    /**
     * @return HasMany<Tax>
     */
    public function taxes(): HasMany
    {
        return $this->hasMany(Tax::class);
    }

    /**
     * @return HasMany<Fee>
     */
    public function fees(): HasMany
    {
        return $this->hasMany(Fee::class);
    }

    /**
     * @return HasMany<Coupon>
     */
    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }

    /**
     * @return HasMany<Unit>
     */
    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    /**
     * @return HasMany<UnitCalendar>
     */
    public function unitCalendars(): HasMany
    {
        return $this->hasMany(UnitCalendar::class);
    }

    /**
     * @return HasMany<Hold>
     */
    public function holds(): HasMany
    {
        return $this->hasMany(Hold::class);
    }

    /**
     * @return HasMany<InventoryLedger>
     */
    public function inventoryLedgers(): HasMany
    {
        return $this->hasMany(InventoryLedger::class);
    }

    /**
     * @return HasMany<IdempotencyKey>
     */
    public function idempotencyKeys(): HasMany
    {
        return $this->hasMany(IdempotencyKey::class);
    }

    /**
     * @return HasMany<Customer>
     */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    /**
     * @return HasMany<CustomerAccessToken>
     */
    public function customerAccessTokens(): HasMany
    {
        return $this->hasMany(CustomerAccessToken::class);
    }

    /**
     * @return HasMany<Booking>
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * @return HasMany<Payment>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * @return HasMany<Invoice>
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * @return HasMany<NotificationTemplate>
     */
    public function notificationTemplates(): HasMany
    {
        return $this->hasMany(NotificationTemplate::class);
    }

    /**
     * @return HasMany<NotificationEvent>
     */
    public function notificationEvents(): HasMany
    {
        return $this->hasMany(NotificationEvent::class);
    }

    /**
     * @return HasMany<NotificationQueue>
     */
    public function notificationQueue(): HasMany
    {
        return $this->hasMany(NotificationQueue::class);
    }

    /**
     * @return HasMany<SmsProvider>
     */
    public function smsProviders(): HasMany
    {
        return $this->hasMany(SmsProvider::class);
    }

    /**
     * @return HasMany<SmsMessage>
     */
    public function smsMessages(): HasMany
    {
        return $this->hasMany(SmsMessage::class);
    }

    /**
     * @return HasMany<Webhook>
     */
    public function webhooks(): HasMany
    {
        return $this->hasMany(Webhook::class);
    }

    /**
     * @return HasMany<IcalFeed>
     */
    public function icalFeeds(): HasMany
    {
        return $this->hasMany(IcalFeed::class);
    }

    /**
     * @return HasMany<CalendarSyncAccount>
     */
    public function calendarSyncAccounts(): HasMany
    {
        return $this->hasMany(CalendarSyncAccount::class);
    }

    /**
     * @return HasMany<SearchIndex>
     */
    public function searchIndex(): HasMany
    {
        return $this->hasMany(SearchIndex::class);
    }

    /**
     * @return HasMany<ReportsCache>
     */
    public function reportsCache(): HasMany
    {
        return $this->hasMany(ReportsCache::class);
    }

    /**
     * @return HasMany<AuditLog>
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * @return HasMany<GdprErasureQueue>
     */
    public function gdprErasureQueue(): HasMany
    {
        return $this->hasMany(GdprErasureQueue::class);
    }

    /**
     * @return HasMany<StaffInvitation>
     */
    public function staffInvitations(): HasMany
    {
        return $this->hasMany(StaffInvitation::class);
    }
}
