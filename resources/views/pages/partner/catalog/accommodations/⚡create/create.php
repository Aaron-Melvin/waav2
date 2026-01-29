<?php

use App\Http\Requests\Partner\StoreProductRequest;
use App\Http\Requests\Partner\StoreRatePlanPriceRequest;
use App\Http\Requests\Partner\StoreUnitRequest;
use App\Http\Requests\Partner\UpdateRatePlanRequest;
use App\Models\CancellationPolicy;
use App\Models\Location;
use App\Models\Partner;
use App\Models\Product;
use App\Models\RatePlan;
use App\Models\RatePlanPrice;
use App\Models\Unit;
use App\Models\UnitCalendar;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::app'), Title('Create Accommodation')] class extends Component
{
    public Partner $partner;

    public string $productName = '';

    public ?string $productSlug = null;

    public ?string $productDescription = null;

    public ?string $productCapacityTotal = null;

    public ?string $productDefaultCurrency = null;

    public string $productStatus = 'active';

    public string $productVisibility = 'public';

    public ?string $productLeadTimeMinutes = null;

    public ?string $productCutoffMinutes = null;

    public ?string $productLocationId = null;

    public string $unitName = '';

    public ?string $unitCode = null;

    public ?string $unitOccupancyAdults = null;

    public ?string $unitOccupancyChildren = null;

    public string $unitStatus = 'active';

    public string $unitHousekeepingRequired = '0';

    public ?string $unitMetaJson = null;

    public string $ratePlanName = '';

    public ?string $ratePlanCode = null;

    public string $ratePlanPricingModel = 'per_night';

    public string $ratePlanCurrency = 'EUR';

    public string $ratePlanStatus = 'active';

    public ?string $ratePlanCancellationPolicyId = null;

    public ?string $priceStartsOn = null;

    public ?string $priceEndsOn = null;

    public ?string $priceAmount = null;

    public ?string $priceExtraAdult = null;

    public ?string $priceExtraChild = null;

    public ?string $priceRestrictions = null;

    public ?string $availabilityStart = null;

    public ?string $availabilityEnd = null;

    public string $availabilityIsAvailable = '1';

    public ?string $availabilityMinStay = null;

    public ?string $availabilityMaxStay = null;

    public ?string $availabilityReason = null;

    public ?string $savedMessage = null;

    public ?string $createdProductId = null;

    public function mount(): void
    {
        $this->partner = $this->resolvePartner();
        $this->productDefaultCurrency = $this->partner->currency ?? 'EUR';
        $this->ratePlanCurrency = $this->productDefaultCurrency;
    }

    /**
     * @return Collection<int, Location>
     */
    public function getLocationsProperty(): Collection
    {
        return Location::query()
            ->where('partner_id', $this->partner->id)
            ->orderBy('name')
            ->get();
    }

    /**
     * @return Collection<int, CancellationPolicy>
     */
    public function getCancellationPoliciesProperty(): Collection
    {
        return CancellationPolicy::query()
            ->where('partner_id', $this->partner->id)
            ->orderBy('name')
            ->get();
    }

    public function createAccommodation(): void
    {
        $this->savedMessage = null;
        $this->createdProductId = null;
        $this->resetValidation();

        $productPayload = $this->productPayload();
        $productValidator = Validator::make(
            $productPayload,
            StoreProductRequest::rulesFor($this->partner, 'accommodation'),
            StoreProductRequest::messagesFor()
        );

        if ($productValidator->fails()) {
            throw ValidationException::withMessages(
                $this->mapValidationErrors($productValidator->errors()->toArray(), $this->productErrorMap())
            );
        }

        $pricePayload = $this->pricePayload();
        $priceValidator = Validator::make(
            $pricePayload,
            StoreRatePlanPriceRequest::rulesFor(),
            StoreRatePlanPriceRequest::messagesFor()
        );

        if ($priceValidator->fails()) {
            throw ValidationException::withMessages(
                $this->mapValidationErrors($priceValidator->errors()->toArray(), $this->priceErrorMap())
            );
        }

        $availabilityPayload = $this->availabilityPayload();
        $availabilityValidated = null;

        if ($this->hasAvailabilityRange($availabilityPayload)) {
            $availabilityValidator = Validator::make(
                $availabilityPayload,
                $this->availabilityRules()
            );

            if ($availabilityValidator->fails()) {
                throw ValidationException::withMessages(
                    $this->mapValidationErrors($availabilityValidator->errors()->toArray(), $this->availabilityErrorMap())
                );
            }

            $availabilityValidated = $availabilityValidator->validated();

            if ($availabilityValidated['min_stay_nights'] !== null
                && $availabilityValidated['max_stay_nights'] !== null
                && $availabilityValidated['max_stay_nights'] < $availabilityValidated['min_stay_nights']) {
                throw ValidationException::withMessages([
                    'availabilityMaxStay' => ['Max stay must be greater than or equal to min stay.'],
                ]);
            }
        }

        $createdProduct = null;

        DB::transaction(function () use (
            $productPayload,
            $priceValidator,
            $availabilityValidated,
            &$createdProduct
        ): void {
            $baseSlug = $productPayload['slug'] ?? Str::slug($productPayload['name']);
            $slug = $this->uniqueSlug(
                $this->partner->id,
                $baseSlug !== '' ? $baseSlug : Str::slug($productPayload['name'])
            );

            $product = Product::query()->create([
                'partner_id' => $this->partner->id,
                'location_id' => $productPayload['location_id'] ?? null,
                'name' => $productPayload['name'],
                'type' => 'accommodation',
                'slug' => $slug,
                'description' => $productPayload['description'] ?? null,
                'capacity_total' => $productPayload['capacity_total'] ?? null,
                'default_currency' => $productPayload['default_currency'] ?? $this->partner->currency ?? 'EUR',
                'status' => $productPayload['status'] ?? 'active',
                'visibility' => $productPayload['visibility'] ?? 'public',
                'lead_time_minutes' => $productPayload['lead_time_minutes'] ?? null,
                'cutoff_minutes' => $productPayload['cutoff_minutes'] ?? null,
            ]);

            $unitMeta = $this->decodeJsonField($this->unitMetaJson, 'unitMetaJson');

            $unitPayload = $this->unitPayload();
            $unitPayload['meta'] = $unitMeta;

            $unitValidator = Validator::make(
                $unitPayload,
                StoreUnitRequest::rulesFor($product),
                StoreUnitRequest::messagesFor()
            );

            if ($unitValidator->fails()) {
                throw ValidationException::withMessages(
                    $this->mapValidationErrors($unitValidator->errors()->toArray(), $this->unitErrorMap())
                );
            }

            $unitValidated = $unitValidator->validated();

            $unit = Unit::query()->create([
                'partner_id' => $this->partner->id,
                'product_id' => $product->id,
                'name' => $unitValidated['name'],
                'code' => $unitValidated['code'] ?? null,
                'occupancy_adults' => $unitValidated['occupancy_adults'],
                'occupancy_children' => $unitValidated['occupancy_children'],
                'status' => $unitValidated['status'],
                'housekeeping_required' => $unitValidated['housekeeping_required'],
                'meta' => $unitValidated['meta'] ?? null,
            ]);

            $ratePlanPayload = $this->ratePlanPayload();
            $ratePlanValidator = Validator::make(
                $ratePlanPayload,
                UpdateRatePlanRequest::rulesForCreate($this->partner, $product),
                UpdateRatePlanRequest::messagesFor()
            );

            if ($ratePlanValidator->fails()) {
                throw ValidationException::withMessages(
                    $this->mapValidationErrors($ratePlanValidator->errors()->toArray(), $this->ratePlanErrorMap())
                );
            }

            $ratePlanValidated = $ratePlanValidator->validated();

            $ratePlan = RatePlan::query()->create([
                'partner_id' => $this->partner->id,
                'product_id' => $product->id,
                'name' => $ratePlanValidated['name'],
                'code' => $ratePlanValidated['code'] ?? null,
                'pricing_model' => $ratePlanValidated['pricing_model'],
                'currency' => $ratePlanValidated['currency'],
                'status' => $ratePlanValidated['status'],
                'cancellation_policy_id' => $ratePlanValidated['cancellation_policy_id'] ?? null,
            ]);

            $priceValidated = $priceValidator->validated();
            $priceRestrictions = $this->decodeJsonField($priceValidated['restrictions'] ?? null, 'priceRestrictions');

            RatePlanPrice::query()->create([
                'rate_plan_id' => $ratePlan->id,
                'starts_on' => $priceValidated['starts_on'],
                'ends_on' => $priceValidated['ends_on'],
                'price' => $priceValidated['price'],
                'extra_adult' => $priceValidated['extra_adult'] ?? null,
                'extra_child' => $priceValidated['extra_child'] ?? null,
                'restrictions' => $priceRestrictions,
            ]);

            if ($availabilityValidated) {
                $start = CarbonImmutable::parse($availabilityValidated['start']);
                $end = CarbonImmutable::parse($availabilityValidated['end']);

                for ($date = $start; $date->lessThanOrEqualTo($end); $date = $date->addDay()) {
                    UnitCalendar::query()->updateOrCreate(
                        [
                            'unit_id' => $unit->id,
                            'date' => $date->toDateString(),
                        ],
                        [
                            'partner_id' => $this->partner->id,
                            'is_available' => $availabilityValidated['is_available'],
                            'min_stay_nights' => $availabilityValidated['min_stay_nights'],
                            'max_stay_nights' => $availabilityValidated['max_stay_nights'],
                            'reason' => $availabilityValidated['reason'],
                        ]
                    );
                }
            }

            $createdProduct = $product;
        });

        $this->savedMessage = 'Accommodation created with a starter unit, rate plan, and pricing.';
        $this->createdProductId = $createdProduct?->id;
        $this->resetForm();
    }

    /**
     * @return array<string, mixed>
     */
    protected function productPayload(): array
    {
        return [
            'name' => $this->normalizeString($this->productName),
            'type' => 'accommodation',
            'slug' => $this->normalizeString($this->productSlug),
            'description' => $this->normalizeString($this->productDescription),
            'capacity_total' => $this->normalizeInteger($this->productCapacityTotal),
            'default_currency' => $this->normalizeString($this->productDefaultCurrency),
            'status' => $this->normalizeString($this->productStatus),
            'visibility' => $this->normalizeString($this->productVisibility),
            'lead_time_minutes' => $this->normalizeInteger($this->productLeadTimeMinutes),
            'cutoff_minutes' => $this->normalizeInteger($this->productCutoffMinutes),
            'location_id' => $this->normalizeString($this->productLocationId),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function unitPayload(): array
    {
        return [
            'name' => $this->normalizeString($this->unitName),
            'code' => $this->normalizeString($this->unitCode),
            'occupancy_adults' => $this->normalizeInteger($this->unitOccupancyAdults),
            'occupancy_children' => $this->normalizeInteger($this->unitOccupancyChildren),
            'status' => $this->normalizeString($this->unitStatus) ?? 'active',
            'housekeeping_required' => $this->normalizeBoolean($this->unitHousekeepingRequired),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function ratePlanPayload(): array
    {
        $currency = $this->normalizeString($this->ratePlanCurrency);

        return [
            'name' => $this->normalizeString($this->ratePlanName),
            'code' => $this->normalizeString($this->ratePlanCode),
            'pricing_model' => $this->normalizeString($this->ratePlanPricingModel) ?? 'per_night',
            'currency' => $currency ? strtoupper($currency) : 'EUR',
            'status' => $this->normalizeString($this->ratePlanStatus) ?? 'active',
            'cancellation_policy_id' => $this->normalizeString($this->ratePlanCancellationPolicyId),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function pricePayload(): array
    {
        return [
            'starts_on' => $this->normalizeString($this->priceStartsOn),
            'ends_on' => $this->normalizeString($this->priceEndsOn),
            'price' => $this->normalizeFloat($this->priceAmount),
            'extra_adult' => $this->normalizeFloat($this->priceExtraAdult),
            'extra_child' => $this->normalizeFloat($this->priceExtraChild),
            'restrictions' => $this->normalizeString($this->priceRestrictions),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function availabilityPayload(): array
    {
        return [
            'start' => $this->normalizeString($this->availabilityStart),
            'end' => $this->normalizeString($this->availabilityEnd),
            'is_available' => $this->normalizeBoolean($this->availabilityIsAvailable),
            'min_stay_nights' => $this->normalizeInteger($this->availabilityMinStay),
            'max_stay_nights' => $this->normalizeInteger($this->availabilityMaxStay),
            'reason' => $this->normalizeString($this->availabilityReason),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function availabilityRules(): array
    {
        return [
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after_or_equal:start'],
            'is_available' => ['required', 'boolean'],
            'min_stay_nights' => ['nullable', 'integer', 'min:1'],
            'max_stay_nights' => ['nullable', 'integer', 'min:1'],
            'reason' => ['nullable', 'string', 'max:150'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function productErrorMap(): array
    {
        return [
            'name' => 'productName',
            'slug' => 'productSlug',
            'description' => 'productDescription',
            'capacity_total' => 'productCapacityTotal',
            'default_currency' => 'productDefaultCurrency',
            'status' => 'productStatus',
            'visibility' => 'productVisibility',
            'lead_time_minutes' => 'productLeadTimeMinutes',
            'cutoff_minutes' => 'productCutoffMinutes',
            'location_id' => 'productLocationId',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function unitErrorMap(): array
    {
        return [
            'name' => 'unitName',
            'code' => 'unitCode',
            'occupancy_adults' => 'unitOccupancyAdults',
            'occupancy_children' => 'unitOccupancyChildren',
            'status' => 'unitStatus',
            'housekeeping_required' => 'unitHousekeepingRequired',
            'meta' => 'unitMetaJson',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function ratePlanErrorMap(): array
    {
        return [
            'name' => 'ratePlanName',
            'code' => 'ratePlanCode',
            'pricing_model' => 'ratePlanPricingModel',
            'currency' => 'ratePlanCurrency',
            'status' => 'ratePlanStatus',
            'cancellation_policy_id' => 'ratePlanCancellationPolicyId',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function priceErrorMap(): array
    {
        return [
            'starts_on' => 'priceStartsOn',
            'ends_on' => 'priceEndsOn',
            'price' => 'priceAmount',
            'extra_adult' => 'priceExtraAdult',
            'extra_child' => 'priceExtraChild',
            'restrictions' => 'priceRestrictions',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function availabilityErrorMap(): array
    {
        return [
            'start' => 'availabilityStart',
            'end' => 'availabilityEnd',
            'is_available' => 'availabilityIsAvailable',
            'min_stay_nights' => 'availabilityMinStay',
            'max_stay_nights' => 'availabilityMaxStay',
            'reason' => 'availabilityReason',
        ];
    }

    /**
     * @param  array<string, array<int, string>>  $errors
     * @param  array<string, string>  $map
     * @return array<string, array<int, string>>
     */
    protected function mapValidationErrors(array $errors, array $map): array
    {
        $mapped = [];

        foreach ($errors as $key => $messages) {
            $mappedKey = $map[$key] ?? $key;
            $mapped[$mappedKey] = $messages;
        }

        return $mapped;
    }

    protected function hasAvailabilityRange(array $payload): bool
    {
        return $payload['start'] !== null || $payload['end'] !== null;
    }

    protected function uniqueSlug(string $partnerId, string $baseSlug): string
    {
        $baseSlug = $baseSlug !== '' ? $baseSlug : Str::random(8);
        $slug = $baseSlug;
        $suffix = 1;

        while (Product::query()
            ->where('partner_id', $partnerId)
            ->where('type', 'accommodation')
            ->where('slug', $slug)
            ->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function decodeJsonField(?string $value, string $field): ?array
    {
        $value = $this->normalizeString($value);

        if ($value === null) {
            return null;
        }

        $decoded = json_decode($value, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            throw ValidationException::withMessages([
                $field => ['Value must be valid JSON.'],
            ]);
        }

        return $decoded;
    }

    protected function normalizeInteger(?string $value): ?int
    {
        $value = $value !== null ? trim($value) : '';

        if ($value === '') {
            return null;
        }

        return (int) $value;
    }

    protected function normalizeFloat(?string $value): ?float
    {
        $value = $value !== null ? trim($value) : '';

        if ($value === '') {
            return null;
        }

        return (float) $value;
    }

    protected function normalizeBoolean(string $value): bool
    {
        return $value === '1';
    }

    protected function normalizeString(?string $value): ?string
    {
        $value = $value !== null ? trim($value) : '';

        return $value !== '' ? $value : null;
    }

    protected function resetForm(): void
    {
        $this->reset([
            'productName',
            'productSlug',
            'productDescription',
            'productCapacityTotal',
            'productLeadTimeMinutes',
            'productCutoffMinutes',
            'productLocationId',
            'unitName',
            'unitCode',
            'unitOccupancyAdults',
            'unitOccupancyChildren',
            'unitMetaJson',
            'ratePlanName',
            'ratePlanCode',
            'ratePlanCancellationPolicyId',
            'priceStartsOn',
            'priceEndsOn',
            'priceAmount',
            'priceExtraAdult',
            'priceExtraChild',
            'priceRestrictions',
            'availabilityStart',
            'availabilityEnd',
            'availabilityMinStay',
            'availabilityMaxStay',
            'availabilityReason',
        ]);

        $this->productStatus = 'active';
        $this->productVisibility = 'public';
        $this->productDefaultCurrency = $this->partner->currency ?? 'EUR';
        $this->ratePlanPricingModel = 'per_night';
        $this->ratePlanCurrency = $this->partner->currency ?? 'EUR';
        $this->ratePlanStatus = 'active';
        $this->unitStatus = 'active';
        $this->unitHousekeepingRequired = '0';
        $this->availabilityIsAvailable = '1';
    }

    protected function resolvePartner(): Partner
    {
        $partner = request()->attributes->get('currentPartner') ?? auth()->user()?->partner;

        if (! $partner instanceof Partner) {
            abort(403);
        }

        return $partner;
    }
};
