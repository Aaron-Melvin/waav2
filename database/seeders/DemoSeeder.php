<?php

namespace Database\Seeders;

use App\Models\ApiClient;
use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\BookingAllocation;
use App\Models\BookingItem;
use App\Models\BookingStatusHistory;
use App\Models\CalendarSyncAccount;
use App\Models\CalendarSyncEvent;
use App\Models\CancellationPolicy;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\CustomerAccessToken;
use App\Models\EligibilityRule;
use App\Models\Event;
use App\Models\EventBlackout;
use App\Models\EventOverride;
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
use App\Models\ProductMedia;
use App\Models\RatePlan;
use App\Models\RatePlanPrice;
use App\Models\Refund;
use App\Models\ReportsCache;
use App\Models\SearchIndex;
use App\Models\SmsMessage;
use App\Models\SmsProvider;
use App\Models\StaffInvitation;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\UnitBookingLock;
use App\Models\UnitCalendar;
use App\Models\UnitHoldLock;
use App\Models\User;
use App\Models\Webhook;
use App\Models\WebhookDelivery;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedRoles();
        $this->seedPlatformUsers();

        $partnerCount = app()->environment('testing') ? 2 : 6;
        $locationsPerPartner = app()->environment('testing') ? 2 : 5;
        $productsPerPartner = app()->environment('testing') ? 6 : 20;
        $mediaPerProduct = app()->environment('testing') ? [1, 2] : [2, 4];

        Partner::factory()
            ->count($partnerCount)
            ->create()
            ->each(function (Partner $partner, int $index) use (
                $locationsPerPartner,
                $productsPerPartner,
                $mediaPerProduct
            ): void {
                $locations = Location::factory()
                    ->count($locationsPerPartner)
                    ->for($partner)
                    ->create();

                $this->seedPartnerApiClient($partner);
                $admin = $this->seedPartnerAdminUser($partner, $index + 1);

                $policies = $this->seedPoliciesAndPricing($partner);

                $eventCount = (int) floor($productsPerPartner * 0.7);
                $accommodationCount = $productsPerPartner - $eventCount;

                $eventProducts = Product::factory()
                    ->count($eventCount)
                    ->for($partner)
                    ->state(fn () => [
                        'location_id' => $locations->random()->id,
                    ])
                    ->create();

                $accommodationProducts = Product::factory()
                    ->count($accommodationCount)
                    ->accommodation()
                    ->for($partner)
                    ->state(fn () => [
                        'location_id' => $locations->random()->id,
                    ])
                    ->create();

                $eventProducts
                    ->merge($accommodationProducts)
                    ->each(function (Product $product) use ($mediaPerProduct): void {
                        $count = fake()->numberBetween($mediaPerProduct[0], $mediaPerProduct[1]);

                        for ($i = 0; $i < $count; $i++) {
                            ProductMedia::factory()
                                ->for($product)
                                ->create([
                                    'sort' => $i,
                                ]);
                        }
                    });

                $this->seedEventSeriesAndEvents($partner, $eventProducts);
                $this->seedEventBlackouts($partner, $locations, $eventProducts);
                $this->seedEligibilityRules($partner, $eventProducts, $accommodationProducts);
                $this->seedRatePlans($partner, $accommodationProducts, $policies['cancellation_policies']);
                $this->seedUnitsAndAvailability($partner, $accommodationProducts);
                $this->seedHoldsAndInventory($partner);
                $this->seedIdempotencyKeys($partner);

                $bookings = $this->seedBookings($partner, $policies['coupons']);

                $this->seedPaymentsAndInvoices($partner, $bookings);
                $this->seedNotifications($partner, $bookings);
                $this->seedIntegrations($partner, $eventProducts, $accommodationProducts);
                $this->seedSearchIndex($partner);
                $this->seedAuditAndCompliance($partner, $bookings);
                $this->seedStaffInvitations($partner, $admin);
                $this->seedCustomerAccessTokens($partner);
                $this->seedReportsCache($partner);
            });
    }

    protected function seedRoles(): void
    {
        foreach (['super-admin', 'partner-admin', 'partner-staff'] as $role) {
            Role::findOrCreate($role);
        }
    }

    protected function seedPlatformUsers(): void
    {
        if (PlatformUser::query()->exists()) {
            return;
        }

        PlatformUser::factory()
            ->count(app()->environment('testing') ? 1 : 2)
            ->create([
                'password' => 'password',
            ]);
    }

    protected function seedPartnerAdminUser(Partner $partner, int $index): User
    {
        $user = User::factory()->create([
            'name' => "{$partner->name} Admin",
            'email' => "partner{$index}@waa.test",
            'partner_id' => $partner->id,
        ]);

        $user->assignRole('partner-admin');

        return $user;
    }

    protected function seedPartnerApiClient(Partner $partner): void
    {
        $clientId = Str::slug($partner->slug);
        $clientSecret = "demo-secret-{$partner->slug}";

        ApiClient::query()->create([
            'partner_id' => $partner->id,
            'client_id' => $clientId,
            'client_secret_hash' => Hash::make($clientSecret),
            'scopes' => ['bookings:read', 'bookings:write'],
            'status' => 'active',
        ]);
    }

    /**
     * @return array{cancellation_policies: \Illuminate\Support\Collection<int, CancellationPolicy>, taxes: \Illuminate\Support\Collection<int, Tax>, fees: \Illuminate\Support\Collection<int, Fee>, coupons: \Illuminate\Support\Collection<int, Coupon>}
     */
    protected function seedPoliciesAndPricing(Partner $partner): array
    {
        $policyCount = app()->environment('testing') ? 1 : 2;
        $taxCount = app()->environment('testing') ? 1 : 2;
        $feeCount = app()->environment('testing') ? 1 : 3;
        $couponCount = app()->environment('testing') ? 1 : 3;

        $cancellationPolicies = CancellationPolicy::factory()
            ->count($policyCount)
            ->for($partner)
            ->create();

        $taxes = Tax::factory()
            ->count($taxCount)
            ->for($partner)
            ->create();

        $fees = Fee::factory()
            ->count($feeCount)
            ->for($partner)
            ->create();

        $coupons = Coupon::factory()
            ->count($couponCount)
            ->for($partner)
            ->create();

        return [
            'cancellation_policies' => $cancellationPolicies,
            'taxes' => $taxes,
            'fees' => $fees,
            'coupons' => $coupons,
        ];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Product>  $eventProducts
     * @param  \Illuminate\Support\Collection<int, Product>  $accommodationProducts
     */
    protected function seedEligibilityRules(Partner $partner, $eventProducts, $accommodationProducts): void
    {
        $targets = $eventProducts->merge($accommodationProducts)->shuffle();

        $targets->take(app()->environment('testing') ? 2 : 6)->each(function (Product $product) use ($partner): void {
            EligibilityRule::factory()->create([
                'partner_id' => $partner->id,
                'product_id' => $product->id,
            ]);
        });
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Product>  $accommodationProducts
     * @param  \Illuminate\Support\Collection<int, CancellationPolicy>  $cancellationPolicies
     */
    protected function seedRatePlans(Partner $partner, $accommodationProducts, $cancellationPolicies): void
    {
        foreach ($accommodationProducts as $product) {
            $planCount = app()->environment('testing') ? 1 : 2;

            $plans = RatePlan::factory()
                ->count($planCount)
                ->create([
                    'partner_id' => $partner->id,
                    'product_id' => $product->id,
                    'cancellation_policy_id' => $cancellationPolicies->random()->id,
                ]);

            foreach ($plans as $plan) {
                RatePlanPrice::factory()
                    ->count(app()->environment('testing') ? 2 : 4)
                    ->create([
                        'rate_plan_id' => $plan->id,
                    ]);
            }
        }
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Product>  $eventProducts
     */
    protected function seedEventSeriesAndEvents(Partner $partner, $eventProducts): void
    {
        foreach ($eventProducts as $product) {
            $seriesCount = app()->environment('testing') ? 1 : 2;

            $seriesItems = EventSeries::factory()
                ->count($seriesCount)
                ->create([
                    'partner_id' => $partner->id,
                    'product_id' => $product->id,
                    'capacity_total' => $product->capacity_total,
                    'timezone' => $partner->timezone,
                ]);

            foreach ($seriesItems as $series) {
                for ($i = 0; $i < 6; $i++) {
                    $start = CarbonImmutable::now($partner->timezone)
                        ->addDays(1 + ($i * 3))
                        ->setTimeFromTimeString($series->starts_at);

                    $end = $start->setTimeFromTimeString($series->ends_at);

                    if ($end->lessThanOrEqualTo($start)) {
                        $end = $start->addHours(2);
                    }

                    Event::factory()->create([
                        'partner_id' => $partner->id,
                        'product_id' => $product->id,
                        'event_series_id' => $series->id,
                        'starts_at' => $start,
                        'ends_at' => $end,
                        'capacity_total' => $series->capacity_total,
                        'capacity_reserved' => fake()->numberBetween(0, 6),
                        'status' => 'scheduled',
                        'publish_state' => 'published',
                        'weather_alert' => fake()->boolean(5),
                    ]);
                }
            }
        }

        $events = Event::query()->where('partner_id', $partner->id)->get();

        $events->random(min(12, $events->count()))
            ->each(function (Event $event): void {
                EventOverride::factory()->create([
                    'event_id' => $event->id,
                ]);
            });
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Location>  $locations
     * @param  \Illuminate\Support\Collection<int, Product>  $eventProducts
     */
    protected function seedEventBlackouts(Partner $partner, $locations, $eventProducts): void
    {
        if ($locations->isEmpty() || $eventProducts->isEmpty()) {
            return;
        }

        $blackoutCount = app()->environment('testing') ? 1 : 2;

        for ($i = 0; $i < $blackoutCount; $i++) {
            EventBlackout::factory()->create([
                'partner_id' => $partner->id,
                'location_id' => $locations->random()->id,
                'product_id' => $eventProducts->random()->id,
            ]);
        }
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Product>  $accommodationProducts
     */
    protected function seedUnitsAndAvailability(Partner $partner, $accommodationProducts): void
    {
        foreach ($accommodationProducts as $product) {
            $units = Unit::factory()
                ->count(app()->environment('testing') ? 2 : 5)
                ->create([
                    'partner_id' => $partner->id,
                    'product_id' => $product->id,
                ]);

            foreach ($units as $unit) {
                for ($i = 0; $i < 14; $i++) {
                    $date = CarbonImmutable::now($partner->timezone)->addDays($i)->toDateString();

                    UnitCalendar::factory()->create([
                        'partner_id' => $partner->id,
                        'unit_id' => $unit->id,
                        'date' => $date,
                        'is_available' => fake()->boolean(85),
                    ]);
                }
            }
        }
    }

    protected function seedHoldsAndInventory(Partner $partner): void
    {
        $events = Event::query()->where('partner_id', $partner->id)->get();
        $units = Unit::query()->where('partner_id', $partner->id)->get();

        $events->take(8)->each(function (Event $event) use ($partner): void {
            Hold::factory()->create([
                'partner_id' => $partner->id,
                'product_id' => $event->product_id,
                'event_id' => $event->id,
                'unit_id' => null,
                'starts_on' => $event->starts_at?->toDateString(),
                'ends_on' => $event->ends_at?->toDateString(),
            ]);
        });

        $units->take(8)->each(function (Unit $unit) use ($partner): void {
            $start = CarbonImmutable::now($partner->timezone)->addDays(fake()->numberBetween(1, 10))->toDateString();
            $end = CarbonImmutable::now($partner->timezone)->addDays(fake()->numberBetween(2, 12))->toDateString();

            $hold = Hold::factory()->create([
                'partner_id' => $partner->id,
                'product_id' => $unit->product_id,
                'event_id' => null,
                'unit_id' => $unit->id,
                'starts_on' => $start,
                'ends_on' => $end,
            ]);

            $startDate = CarbonImmutable::parse($hold->starts_on);
            $endDate = CarbonImmutable::parse($hold->ends_on);

            for ($date = $startDate; $date->lessThanOrEqualTo($endDate); $date = $date->addDay()) {
                UnitHoldLock::query()->firstOrCreate([
                    'hold_id' => $hold->id,
                    'unit_id' => $unit->id,
                    'date' => $date->toDateString(),
                ]);
            }
        });

        for ($i = 0; $i < 10; $i++) {
            InventoryLedger::factory()->create([
                'partner_id' => $partner->id,
                'product_id' => $events->isNotEmpty() ? $events->random()->product_id : null,
                'event_id' => $events->isNotEmpty() ? $events->random()->id : null,
                'unit_id' => $units->isNotEmpty() ? $units->random()->id : null,
                'hold_id' => Hold::query()->where('partner_id', $partner->id)->inRandomOrder()->value('id'),
            ]);
        }
    }

    protected function seedIdempotencyKeys(Partner $partner): void
    {
        IdempotencyKey::factory()
            ->count(5)
            ->create([
                'partner_id' => $partner->id,
            ]);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Coupon>  $coupons
     * @return \Illuminate\Support\Collection<int, Booking>
     */
    protected function seedBookings(Partner $partner, $coupons)
    {
        $customers = Customer::factory()
            ->count(app()->environment('testing') ? 6 : 20)
            ->create([
                'partner_id' => $partner->id,
            ]);

        $bookings = Booking::factory()
            ->count(app()->environment('testing') ? 10 : 30)
            ->create([
                'partner_id' => $partner->id,
                'customer_id' => $customers->random()->id,
                'coupon_id' => $coupons->isNotEmpty() && fake()->boolean(30)
                    ? $coupons->random()->id
                    : null,
            ]);

        foreach ($bookings as $booking) {
            $events = Event::query()->where('partner_id', $partner->id)->get();
            $units = Unit::query()->where('partner_id', $partner->id)->get();

            if ($events->isNotEmpty()) {
                $event = $events->random();
                $bookingItem = BookingItem::factory()->create([
                    'booking_id' => $booking->id,
                    'product_id' => $event->product_id,
                    'event_id' => $event->id,
                    'unit_id' => null,
                    'item_type' => 'event',
                    'starts_on' => $event->starts_at?->toDateString(),
                    'ends_on' => $event->ends_at?->toDateString(),
                ]);

                BookingAllocation::factory()->create([
                    'booking_id' => $booking->id,
                    'event_id' => $event->id,
                    'unit_id' => null,
                    'quantity' => $bookingItem->quantity,
                ]);
            } elseif ($units->isNotEmpty()) {
                $unit = $units->random();
                $bookingItem = BookingItem::factory()->create([
                    'booking_id' => $booking->id,
                    'product_id' => $unit->product_id,
                    'event_id' => null,
                    'unit_id' => $unit->id,
                    'item_type' => 'accommodation',
                ]);

                $start = CarbonImmutable::now($partner->timezone)->addDays(fake()->numberBetween(1, 10));
                $end = $start->addDays(fake()->numberBetween(1, 4));

                for ($date = $start; $date->lessThanOrEqualTo($end); $date = $date->addDay()) {
                    UnitBookingLock::query()->firstOrCreate([
                        'booking_id' => $booking->id,
                        'unit_id' => $unit->id,
                        'date' => $date->toDateString(),
                    ]);
                }

                BookingAllocation::factory()->create([
                    'booking_id' => $booking->id,
                    'event_id' => null,
                    'unit_id' => $unit->id,
                    'quantity' => $bookingItem->quantity,
                ]);
            }

            BookingStatusHistory::factory()
                ->count(2)
                ->create([
                    'booking_id' => $booking->id,
                ]);
        }

        return $bookings;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Booking>  $bookings
     */
    protected function seedPaymentsAndInvoices(Partner $partner, $bookings): void
    {
        $bookings->each(function (Booking $booking) use ($partner): void {
            $shouldCreate = in_array($booking->status, ['confirmed', 'completed', 'cancelled'], true);

            if (! $shouldCreate) {
                return;
            }

            $paymentStatus = $booking->status === 'cancelled'
                ? fake()->randomElement(['failed', 'refunded'])
                : 'captured';

            $payment = Payment::factory()->create([
                'partner_id' => $partner->id,
                'booking_id' => $booking->id,
                'status' => $paymentStatus,
                'amount' => $booking->total_gross,
                'captured_at' => $paymentStatus === 'captured'
                    ? CarbonImmutable::now()->subHours(2)
                    : null,
            ]);

            if ($paymentStatus === 'refunded') {
                Refund::factory()->create([
                    'payment_id' => $payment->id,
                    'amount' => $payment->amount,
                    'status' => 'succeeded',
                ]);
            }

            Invoice::factory()->create([
                'partner_id' => $partner->id,
                'booking_id' => $booking->id,
                'total_gross' => $booking->total_gross,
                'total_tax' => $booking->total_tax,
                'total_fees' => $booking->total_fees,
                'issued_at' => CarbonImmutable::now()->subDays(2),
                'due_at' => CarbonImmutable::now()->addDays(14),
            ]);
        });
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Booking>  $bookings
     */
    protected function seedNotifications(Partner $partner, $bookings): void
    {
        $templates = NotificationTemplate::factory()
            ->count(app()->environment('testing') ? 2 : 4)
            ->for($partner)
            ->create();

        $bookings->take(app()->environment('testing') ? 6 : 12)
            ->each(function (Booking $booking) use ($partner, $templates): void {
                $template = $templates->random();

                NotificationQueue::factory()->create([
                    'partner_id' => $partner->id,
                    'notification_template_id' => $template->id,
                    'channel' => $template->channel,
                    'recipient' => $template->channel === 'email'
                        ? $booking->customer?->email
                        : $booking->customer?->phone_e164,
                    'model_type' => Booking::class,
                    'model_id' => $booking->id,
                ]);

                NotificationEvent::factory()->create([
                    'partner_id' => $partner->id,
                    'notification_template_id' => $template->id,
                    'event' => 'booking.confirmed',
                    'channel' => $template->channel,
                    'recipient' => $booking->customer?->email,
                ]);
            });

        $smsProvider = SmsProvider::factory()->create([
            'partner_id' => $partner->id,
            'is_default' => true,
        ]);

        $bookings->take(app()->environment('testing') ? 4 : 10)
            ->each(function (Booking $booking) use ($partner, $smsProvider): void {
                SmsMessage::factory()->create([
                    'partner_id' => $partner->id,
                    'sms_provider_id' => $smsProvider->id,
                    'related_type' => Booking::class,
                    'related_id' => $booking->id,
                    'to' => $booking->customer?->phone_e164 ?? fake()->e164PhoneNumber(),
                ]);
            });
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Product>  $eventProducts
     * @param  \Illuminate\Support\Collection<int, Product>  $accommodationProducts
     */
    protected function seedIntegrations(Partner $partner, $eventProducts, $accommodationProducts): void
    {
        $webhooks = Webhook::factory()
            ->count(app()->environment('testing') ? 1 : 2)
            ->create([
                'partner_id' => $partner->id,
            ]);

        $webhooks->each(function (Webhook $webhook): void {
            WebhookDelivery::factory()
                ->count(app()->environment('testing') ? 2 : 4)
                ->create([
                    'webhook_id' => $webhook->id,
                ]);
        });

        $icalTargets = $eventProducts->merge($accommodationProducts)->take(4);

        $icalTargets->each(function (Product $product) use ($partner): void {
            IcalFeed::factory()->create([
                'partner_id' => $partner->id,
                'product_id' => $product->id,
                'unit_id' => null,
            ]);
        });

        $calendarAccount = CalendarSyncAccount::factory()->create([
            'partner_id' => $partner->id,
        ]);

        $events = Event::query()->where('partner_id', $partner->id)->take(4)->get();

        $events->each(function (Event $event) use ($calendarAccount): void {
            CalendarSyncEvent::factory()->create([
                'calendar_sync_account_id' => $calendarAccount->id,
                'product_id' => $event->product_id,
                'event_id' => $event->id,
            ]);
        });
    }

    protected function seedSearchIndex(Partner $partner): void
    {
        $events = Event::query()->where('partner_id', $partner->id)->take(10)->get();
        $units = Unit::query()->where('partner_id', $partner->id)->take(10)->get();

        $events->each(function (Event $event) use ($partner): void {
            SearchIndex::factory()->create([
                'partner_id' => $partner->id,
                'product_id' => $event->product_id,
                'event_id' => $event->id,
                'unit_id' => null,
                'location_id' => $event->product?->location_id,
                'starts_on' => $event->starts_at?->toDateString(),
                'ends_on' => $event->ends_at?->toDateString(),
                'capacity_total' => $event->capacity_total,
                'capacity_available' => max(0, $event->capacity_total - $event->capacity_reserved),
                'price_min' => fake()->randomFloat(2, 45, 120),
                'price_max' => fake()->randomFloat(2, 120, 240),
                'currency' => $event->product?->default_currency ?? 'EUR',
            ]);
        });

        $units->each(function (Unit $unit) use ($partner): void {
            SearchIndex::factory()->create([
                'partner_id' => $partner->id,
                'product_id' => $unit->product_id,
                'event_id' => null,
                'unit_id' => $unit->id,
                'location_id' => $unit->product?->location_id,
                'starts_on' => CarbonImmutable::today()->addDays(1),
                'ends_on' => CarbonImmutable::today()->addDays(2),
                'capacity_total' => $unit->occupancy_adults,
                'capacity_available' => max(1, $unit->occupancy_adults - 1),
                'price_min' => fake()->randomFloat(2, 80, 160),
                'price_max' => fake()->randomFloat(2, 160, 240),
                'currency' => $unit->product?->default_currency ?? 'EUR',
            ]);
        });
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Booking>  $bookings
     */
    protected function seedAuditAndCompliance(Partner $partner, $bookings): void
    {
        $bookings->take(app()->environment('testing') ? 6 : 12)
            ->each(function (Booking $booking) use ($partner): void {
                AuditLog::factory()->create([
                    'partner_id' => $partner->id,
                    'action' => 'booking.created',
                    'target_type' => Booking::class,
                    'target_id' => $booking->id,
                ]);
            });

        $customers = Customer::query()->where('partner_id', $partner->id)->take(3)->get();

        $customers->each(function (Customer $customer) use ($partner): void {
            GdprErasureQueue::factory()->create([
                'partner_id' => $partner->id,
                'customer_id' => $customer->id,
            ]);
        });
    }

    protected function seedStaffInvitations(Partner $partner, User $inviter): void
    {
        StaffInvitation::factory()
            ->count(app()->environment('testing') ? 1 : 2)
            ->create([
                'partner_id' => $partner->id,
                'inviter_id' => $inviter->id,
            ]);
    }

    protected function seedCustomerAccessTokens(Partner $partner): void
    {
        $customers = Customer::query()->where('partner_id', $partner->id)->take(4)->get();

        $customers->each(function (Customer $customer) use ($partner): void {
            CustomerAccessToken::factory()->create([
                'partner_id' => $partner->id,
                'customer_id' => $customer->id,
            ]);
        });
    }

    protected function seedReportsCache(Partner $partner): void
    {
        ReportsCache::factory()->create([
            'partner_id' => $partner->id,
            'report_key' => 'dashboard-overview',
        ]);
    }
}
