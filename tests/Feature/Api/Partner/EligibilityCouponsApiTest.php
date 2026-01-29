<?php

use App\Models\ApiClient;
use App\Models\Coupon;
use App\Models\EligibilityRule;
use App\Models\Partner;
use App\Models\Product;
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

it('allows partners to manage eligibility rules', function () {
    $partner = Partner::factory()->create();
    $product = Product::factory()->for($partner)->create();
    $rule = EligibilityRule::factory()->create([
        'partner_id' => $partner->id,
        'product_id' => $product->id,
    ]);

    $apiClient = ApiClient::factory()
        ->for($partner)
        ->withSecret('partner-secret-eligibility')
        ->create(['client_id' => 'partner_eligibility']);

    $headers = partnerHeaders($apiClient, 'partner-secret-eligibility');

    $this->getJson('/api/v1/partner/eligibility-rules', $headers)
        ->assertSuccessful()
        ->assertJsonPath('data.0.id', $rule->id);

    $this->postJson('/api/v1/partner/eligibility-rules', [
        'name' => 'Adults only',
        'kind' => 'age_gate',
        'config' => ['min_age' => 18],
        'product_id' => $product->id,
    ], $headers)
        ->assertCreated()
        ->assertJsonPath('data.kind', 'age_gate');

    $this->patchJson("/api/v1/partner/eligibility-rules/{$rule->id}", [
        'name' => 'Updated Rule',
        'kind' => $rule->kind,
        'config' => $rule->config,
        'status' => 'inactive',
        'product_id' => $product->id,
    ], $headers)
        ->assertSuccessful()
        ->assertJsonPath('data.status', 'inactive');
});

it('allows partners to manage coupons', function () {
    $partner = Partner::factory()->create();
    $coupon = Coupon::factory()->create(['partner_id' => $partner->id]);

    $apiClient = ApiClient::factory()
        ->for($partner)
        ->withSecret('partner-secret-coupons')
        ->create(['client_id' => 'partner_coupons']);

    $headers = partnerHeaders($apiClient, 'partner-secret-coupons');

    $this->getJson('/api/v1/partner/coupons', $headers)
        ->assertSuccessful()
        ->assertJsonPath('data.0.id', $coupon->id);

    $this->postJson('/api/v1/partner/coupons', [
        'code' => 'WELCOME10',
        'discount_type' => 'percent',
        'discount_value' => 10,
        'status' => 'active',
    ], $headers)
        ->assertCreated()
        ->assertJsonPath('data.code', 'WELCOME10');

    $this->patchJson("/api/v1/partner/coupons/{$coupon->id}", [
        'code' => $coupon->code,
        'discount_type' => $coupon->discount_type,
        'discount_value' => $coupon->discount_value,
        'status' => 'inactive',
    ], $headers)
        ->assertSuccessful()
        ->assertJsonPath('data.status', 'inactive');
});
