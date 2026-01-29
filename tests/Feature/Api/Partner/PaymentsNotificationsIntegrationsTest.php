<?php

use App\Models\ApiClient;
use App\Models\Booking;
use App\Models\CalendarSyncAccount;
use App\Models\Invoice;
use App\Models\IcalFeed;
use App\Models\Location;
use App\Models\NotificationTemplate;
use App\Models\Partner;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Refund;
use App\Models\StaffInvitation;
use App\Models\Unit;
use App\Models\Webhook;
use App\Models\WebhookDelivery;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

if (! function_exists('partnerHeaders')) {
    function partnerHeaders(ApiClient $client, string $secret): array
    {
        return [
            'X-Client-Id' => $client->client_id,
            'X-Client-Secret' => $secret,
        ];
    }
}

it('allows partners to list and view payments', function () {
    $partner = Partner::factory()->create();
    $booking = Booking::factory()->for($partner)->create();
    $payment = Payment::factory()->create([
        'partner_id' => $partner->id,
        'booking_id' => $booking->id,
    ]);
    $refund = Refund::factory()->create([
        'payment_id' => $payment->id,
        'amount' => 25,
    ]);

    $apiClient = ApiClient::factory()
        ->for($partner)
        ->withSecret('partner-secret-123')
        ->create(['client_id' => 'partner_payments']);

    $headers = partnerHeaders($apiClient, 'partner-secret-123');

    $this->getJson('/api/v1/partner/payments', $headers)
        ->assertSuccessful()
        ->assertJsonPath('data.0.id', $payment->id);

    $this->getJson("/api/v1/partner/payments/{$payment->id}", $headers)
        ->assertSuccessful()
        ->assertJsonPath('data.id', $payment->id)
        ->assertJsonPath('data.refunds.0.id', $refund->id);
});

it('allows partners to list and view invoices', function () {
    $partner = Partner::factory()->create();
    $booking = Booking::factory()->for($partner)->create();
    $invoice = Invoice::factory()->create([
        'partner_id' => $partner->id,
        'booking_id' => $booking->id,
    ]);

    $apiClient = ApiClient::factory()
        ->for($partner)
        ->withSecret('partner-secret-234')
        ->create(['client_id' => 'partner_invoices']);

    $headers = partnerHeaders($apiClient, 'partner-secret-234');

    $this->getJson('/api/v1/partner/invoices', $headers)
        ->assertSuccessful()
        ->assertJsonPath('data.0.id', $invoice->id);

    $this->getJson("/api/v1/partner/invoices/{$invoice->id}", $headers)
        ->assertSuccessful()
        ->assertJsonPath('data.id', $invoice->id);
});

it('allows partners to manage notification templates', function () {
    $partner = Partner::factory()->create();
    $template = NotificationTemplate::factory()->create([
        'partner_id' => $partner->id,
        'channel' => 'email',
    ]);

    $apiClient = ApiClient::factory()
        ->for($partner)
        ->withSecret('partner-secret-345')
        ->create(['client_id' => 'partner_templates']);

    $headers = partnerHeaders($apiClient, 'partner-secret-345');

    $this->getJson('/api/v1/partner/notification-templates', $headers)
        ->assertSuccessful()
        ->assertJsonPath('data.0.id', $template->id);

    $this->postJson('/api/v1/partner/notification-templates', [
        'name' => 'Booking Confirmation',
        'channel' => 'email',
        'subject' => 'Your booking is confirmed',
        'body' => 'Thanks for booking with us.',
        'status' => 'active',
    ], $headers)
        ->assertCreated()
        ->assertJsonPath('data.channel', 'email');

    $this->patchJson("/api/v1/partner/notification-templates/{$template->id}", [
        'name' => 'Updated Template',
        'status' => 'inactive',
    ], $headers)
        ->assertSuccessful()
        ->assertJsonPath('data.name', 'Updated Template');
});

it('allows partners to manage webhooks and deliveries', function () {
    $partner = Partner::factory()->create();
    $webhook = Webhook::factory()->create([
        'partner_id' => $partner->id,
    ]);
    $delivery = WebhookDelivery::factory()->create([
        'webhook_id' => $webhook->id,
    ]);

    $apiClient = ApiClient::factory()
        ->for($partner)
        ->withSecret('partner-secret-456')
        ->create(['client_id' => 'partner_webhooks']);

    $headers = partnerHeaders($apiClient, 'partner-secret-456');

    $this->getJson('/api/v1/partner/webhooks', $headers)
        ->assertSuccessful()
        ->assertJsonPath('data.0.id', $webhook->id);

    $this->postJson('/api/v1/partner/webhooks', [
        'name' => 'Booking Updates',
        'url' => 'https://example.test/hooks',
        'events' => ['booking.confirmed'],
    ], $headers)
        ->assertCreated()
        ->assertJsonPath('data.events.0', 'booking.confirmed');

    $this->patchJson("/api/v1/partner/webhooks/{$webhook->id}", [
        'name' => 'Updated Webhook',
    ], $headers)
        ->assertSuccessful()
        ->assertJsonPath('data.name', 'Updated Webhook');

    $this->getJson("/api/v1/partner/webhooks/{$webhook->id}/deliveries", $headers)
        ->assertSuccessful()
        ->assertJsonPath('data.0.id', $delivery->id);
});

it('allows partners to manage iCal feeds', function () {
    $partner = Partner::factory()->create();
    $product = Product::factory()->for($partner)->create();
    $feed = IcalFeed::factory()->create([
        'partner_id' => $partner->id,
        'product_id' => $product->id,
    ]);

    $apiClient = ApiClient::factory()
        ->for($partner)
        ->withSecret('partner-secret-567')
        ->create(['client_id' => 'partner_ical']);

    $headers = partnerHeaders($apiClient, 'partner-secret-567');

    $this->getJson('/api/v1/partner/ical-feeds', $headers)
        ->assertSuccessful()
        ->assertJsonPath('data.0.id', $feed->id);

    $this->postJson('/api/v1/partner/ical-feeds', [
        'name' => 'Event Feed',
        'product_id' => $product->id,
    ], $headers)
        ->assertCreated()
        ->assertJsonPath('data.product_id', $product->id);
});

it('allows partners to manage calendar sync accounts', function () {
    $partner = Partner::factory()->create();
    $account = CalendarSyncAccount::factory()->create([
        'partner_id' => $partner->id,
        'provider' => 'google',
    ]);

    $apiClient = ApiClient::factory()
        ->for($partner)
        ->withSecret('partner-secret-678')
        ->create(['client_id' => 'partner_calendar']);

    $headers = partnerHeaders($apiClient, 'partner-secret-678');

    $this->getJson('/api/v1/partner/calendar-sync-accounts', $headers)
        ->assertSuccessful()
        ->assertJsonPath('data.0.id', $account->id);

    $this->postJson('/api/v1/partner/calendar-sync-accounts', [
        'provider' => 'google',
        'email' => 'sync@example.test',
    ], $headers)
        ->assertCreated()
        ->assertJsonPath('data.provider', 'google');
});

it('requires api credentials for partner endpoints', function () {
    $partner = Partner::factory()->create();
    $booking = Booking::factory()->for($partner)->create();
    $payment = Payment::factory()->create([
        'partner_id' => $partner->id,
        'booking_id' => $booking->id,
    ]);
    $invoice = Invoice::factory()->create([
        'partner_id' => $partner->id,
        'booking_id' => $booking->id,
    ]);
    $template = NotificationTemplate::factory()->create([
        'partner_id' => $partner->id,
    ]);
    $webhook = Webhook::factory()->create([
        'partner_id' => $partner->id,
    ]);
    $product = Product::factory()->for($partner)->create();
    $location = Location::factory()->for($partner)->create();
    $unit = Unit::factory()->create([
        'partner_id' => $partner->id,
        'product_id' => $product->id,
    ]);
    $invitation = StaffInvitation::factory()->create([
        'partner_id' => $partner->id,
    ]);

    $endpoints = [
        ['GET', '/api/v1/partner/locations'],
        ['POST', '/api/v1/partner/locations', [
            'name' => 'Location',
            'timezone' => 'Europe/Dublin',
        ]],
        ['PATCH', "/api/v1/partner/locations/{$location->id}", [
            'name' => 'Updated Location',
            'timezone' => $location->timezone,
        ]],
        ['GET', '/api/v1/partner/payments'],
        ['GET', "/api/v1/partner/payments/{$payment->id}"],
        ['GET', '/api/v1/partner/invoices'],
        ['GET', "/api/v1/partner/invoices/{$invoice->id}"],
        ['GET', '/api/v1/partner/notification-templates'],
        ['POST', '/api/v1/partner/notification-templates', [
            'name' => 'Template',
            'channel' => 'email',
            'subject' => 'Hello',
            'body' => 'Test',
        ]],
        ['PATCH', "/api/v1/partner/notification-templates/{$template->id}", [
            'name' => 'Updated',
        ]],
        ['GET', '/api/v1/partner/webhooks'],
        ['POST', '/api/v1/partner/webhooks', [
            'name' => 'Webhook',
            'url' => 'https://example.test/hook',
            'events' => ['booking.confirmed'],
        ]],
        ['PATCH', "/api/v1/partner/webhooks/{$webhook->id}", [
            'name' => 'Updated Webhook',
        ]],
        ['GET', "/api/v1/partner/webhooks/{$webhook->id}/deliveries"],
        ['GET', '/api/v1/partner/ical-feeds'],
        ['POST', '/api/v1/partner/ical-feeds', [
            'name' => 'Feed',
            'product_id' => $product->id,
        ]],
        ['GET', '/api/v1/partner/event-series'],
        ['POST', '/api/v1/partner/event-series', [
            'product_id' => $product->id,
            'name' => 'Series',
            'starts_at' => '09:00',
            'ends_at' => '10:00',
            'timezone' => 'Europe/Dublin',
        ]],
        ['POST', '/api/v1/partner/event-series/sample-id/generate', [
            'date_range' => [
                'from' => now()->toDateString(),
                'to' => now()->addDays(7)->toDateString(),
            ],
        ]],
        ['GET', '/api/v1/partner/taxes'],
        ['POST', '/api/v1/partner/taxes', [
            'name' => 'VAT',
            'rate' => 0.2,
            'applies_to' => 'booking',
        ]],
        ['GET', '/api/v1/partner/fees'],
        ['POST', '/api/v1/partner/fees', [
            'name' => 'Fee',
            'amount' => 10,
            'applies_to' => 'booking',
        ]],
        ['GET', '/api/v1/partner/cancellation-policies'],
        ['POST', '/api/v1/partner/cancellation-policies', [
            'name' => 'Policy',
            'rules' => [
                ['window_hours' => 24, 'fee_percent' => 10],
            ],
        ]],
        ['GET', '/api/v1/partner/eligibility-rules'],
        ['POST', '/api/v1/partner/eligibility-rules', [
            'name' => 'Adults only',
            'kind' => 'age_gate',
            'config' => ['min_age' => 18],
        ]],
        ['GET', '/api/v1/partner/coupons'],
        ['POST', '/api/v1/partner/coupons', [
            'code' => 'WELCOME10',
            'discount_type' => 'percent',
            'discount_value' => 10,
        ]],
        ['GET', "/api/v1/partner/products/{$product->id}/units"],
        ['POST', "/api/v1/partner/products/{$product->id}/units", [
            'name' => 'Unit',
            'occupancy_adults' => 2,
            'occupancy_children' => 0,
            'status' => 'active',
            'housekeeping_required' => true,
        ]],
        ['GET', "/api/v1/partner/products/{$product->id}/units/{$unit->id}"],
        ['POST', "/api/v1/partner/products/{$product->id}/units/{$unit->id}/calendar", [
            'date' => now()->toDateString(),
            'is_available' => true,
        ]],
        ['GET', '/api/v1/partner/staff-invitations'],
        ['POST', '/api/v1/partner/staff-invitations', [
            'email' => 'staff@example.test',
        ]],
        ['GET', '/api/v1/partner/calendar-sync-accounts'],
        ['POST', '/api/v1/partner/calendar-sync-accounts', [
            'provider' => 'google',
            'email' => 'sync@example.test',
        ]],
    ];

    foreach ($endpoints as $endpoint) {
        $method = $endpoint[0];
        $uri = $endpoint[1];
        $payload = $endpoint[2] ?? [];

        $this->json($method, $uri, $payload)->assertUnauthorized();
    }
});
