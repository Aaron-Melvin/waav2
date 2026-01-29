<?php

use App\Http\Requests\Partner\StoreRatePlanPriceRequest;
use App\Http\Requests\Partner\UpdateRatePlanPriceRequest;
use App\Http\Requests\Partner\UpdateRatePlanRequest;
use App\Models\CancellationPolicy;
use App\Models\Partner;
use App\Models\Product;
use App\Models\RatePlan;
use App\Models\RatePlanPrice;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::app'), Title('Rate Plan Details')] class extends Component
{
    public Partner $partner;

    public Product $product;

    public RatePlan $ratePlan;

    public string $name = '';

    public ?string $code = null;

    public string $pricingModel = 'per_night';

    public string $currency = 'EUR';

    public string $status = 'active';

    public ?string $cancellationPolicyId = null;

    public ?string $savedMessage = null;

    public ?string $startsOn = null;

    public ?string $endsOn = null;

    public ?string $price = null;

    public ?string $extraAdult = null;

    public ?string $extraChild = null;

    public ?string $restrictions = null;

    public ?string $priceMessage = null;

    public ?string $editingPriceId = null;

    public ?string $editStartsOn = null;

    public ?string $editEndsOn = null;

    public ?string $editPrice = null;

    public ?string $editExtraAdult = null;

    public ?string $editExtraChild = null;

    public ?string $editRestrictions = null;

    public ?string $editMessage = null;

    public function mount(Product $product, RatePlan $ratePlan): void
    {
        $this->partner = $this->resolvePartner();

        if ($product->partner_id !== $this->partner->id) {
            abort(404);
        }

        if ($product->type !== 'accommodation') {
            abort(404);
        }

        if ($ratePlan->product_id !== $product->id) {
            abort(404);
        }

        $this->product = $product;
        $this->ratePlan = $ratePlan->load('cancellationPolicy');

        $this->fillForm();
    }

    /**
     * @return Collection<int, RatePlanPrice>
     */
    public function getPricesProperty(): Collection
    {
        return RatePlanPrice::query()
            ->where('rate_plan_id', $this->ratePlan->id)
            ->orderByDesc('starts_on')
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

    public function updateRatePlan(): void
    {
        $this->savedMessage = null;

        $payload = $this->ratePlanPayload();

        $validated = Validator::make(
            $payload,
            UpdateRatePlanRequest::rulesFor($this->partner, $this->product, $this->ratePlan),
            UpdateRatePlanRequest::messagesFor()
        )->validate();

        $this->ratePlan->update($validated);
        $this->ratePlan->refresh()->load('cancellationPolicy');

        $this->savedMessage = 'Rate plan updated.';
        $this->resetValidation();
    }

    public function addPrice(): void
    {
        $this->priceMessage = null;
        $this->editMessage = null;

        $payload = $this->pricePayload();

        $validated = Validator::make(
            $payload,
            StoreRatePlanPriceRequest::rulesFor(),
            StoreRatePlanPriceRequest::messagesFor()
        )->validate();

        $validated['rate_plan_id'] = $this->ratePlan->id;
        $validated['restrictions'] = $this->normalizeJson($validated['restrictions'] ?? null);

        RatePlanPrice::create($validated);

        $this->priceMessage = 'Price window added.';
        $this->resetPriceForm();
        $this->resetValidation();
    }

    public function startEditingPrice(string $priceId): void
    {
        $price = RatePlanPrice::query()
            ->where('rate_plan_id', $this->ratePlan->id)
            ->where('id', $priceId)
            ->first();

        if (! $price) {
            abort(404);
        }

        $this->priceMessage = null;
        $this->editingPriceId = $price->id;
        $this->editStartsOn = $price->starts_on?->format('Y-m-d');
        $this->editEndsOn = $price->ends_on?->format('Y-m-d');
        $this->editPrice = $price->price !== null ? (string) $price->price : null;
        $this->editExtraAdult = $price->extra_adult !== null ? (string) $price->extra_adult : null;
        $this->editExtraChild = $price->extra_child !== null ? (string) $price->extra_child : null;
        $this->editRestrictions = $price->restrictions
            ? json_encode($price->restrictions, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            : null;
        $this->editMessage = null;
        $this->resetValidation();
    }

    public function cancelEditingPrice(): void
    {
        $this->editingPriceId = null;
        $this->editStartsOn = null;
        $this->editEndsOn = null;
        $this->editPrice = null;
        $this->editExtraAdult = null;
        $this->editExtraChild = null;
        $this->editRestrictions = null;
        $this->editMessage = null;
        $this->resetValidation();
    }

    public function updatePrice(): void
    {
        $this->editMessage = null;

        if (! $this->editingPriceId) {
            return;
        }

        $payload = $this->editPricePayload();

        $validated = Validator::make(
            $payload,
            UpdateRatePlanPriceRequest::rulesFor(),
            UpdateRatePlanPriceRequest::messagesFor()
        )->validate();

        $validated['restrictions'] = $this->normalizeJson($validated['restrictions'] ?? null);

        $price = RatePlanPrice::query()
            ->where('rate_plan_id', $this->ratePlan->id)
            ->where('id', $this->editingPriceId)
            ->first();

        if (! $price) {
            abort(404);
        }

        $price->update($validated);

        $this->cancelEditingPrice();
        $this->editMessage = 'Price window updated.';
    }

    public function deletePrice(string $priceId): void
    {
        RatePlanPrice::query()
            ->where('rate_plan_id', $this->ratePlan->id)
            ->where('id', $priceId)
            ->delete();

        if ($this->editingPriceId === $priceId) {
            $this->cancelEditingPrice();
        }
    }

    public function formatRestrictions(?array $restrictions): string
    {
        if (! $restrictions) {
            return '—';
        }

        return json_encode($restrictions, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '—';
    }

    protected function fillForm(): void
    {
        $this->name = $this->ratePlan->name;
        $this->code = $this->ratePlan->code;
        $this->pricingModel = $this->ratePlan->pricing_model ?? 'per_night';
        $this->currency = $this->ratePlan->currency ?? 'EUR';
        $this->status = $this->ratePlan->status ?? 'active';
        $this->cancellationPolicyId = $this->ratePlan->cancellation_policy_id;
    }

    /**
     * @return array<string, mixed>
     */
    protected function ratePlanPayload(): array
    {
        $currency = $this->normalizeString($this->currency);

        return [
            'name' => trim($this->name),
            'code' => $this->normalizeString($this->code),
            'pricing_model' => $this->normalizeString($this->pricingModel) ?? 'per_night',
            'currency' => $currency ? strtoupper($currency) : 'EUR',
            'status' => $this->normalizeString($this->status) ?? 'active',
            'cancellation_policy_id' => $this->normalizeString($this->cancellationPolicyId),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function pricePayload(): array
    {
        return [
            'starts_on' => $this->normalizeString($this->startsOn),
            'ends_on' => $this->normalizeString($this->endsOn),
            'price' => $this->normalizeDecimal($this->price),
            'extra_adult' => $this->normalizeDecimal($this->extraAdult),
            'extra_child' => $this->normalizeDecimal($this->extraChild),
            'restrictions' => $this->normalizeString($this->restrictions),
        ];
    }

    protected function resetPriceForm(): void
    {
        $this->startsOn = null;
        $this->endsOn = null;
        $this->price = null;
        $this->extraAdult = null;
        $this->extraChild = null;
        $this->restrictions = null;
    }

    /**
     * @return array<string, mixed>
     */
    protected function editPricePayload(): array
    {
        return [
            'starts_on' => $this->normalizeString($this->editStartsOn),
            'ends_on' => $this->normalizeString($this->editEndsOn),
            'price' => $this->normalizeDecimal($this->editPrice),
            'extra_adult' => $this->normalizeDecimal($this->editExtraAdult),
            'extra_child' => $this->normalizeDecimal($this->editExtraChild),
            'restrictions' => $this->normalizeString($this->editRestrictions),
        ];
    }

    protected function normalizeDecimal(?string $value): ?float
    {
        $value = $value !== null ? trim($value) : '';

        if ($value === '') {
            return null;
        }

        return (float) $value;
    }

    protected function normalizeJson(?string $value): ?array
    {
        $value = $value !== null ? trim($value) : '';

        if ($value === '') {
            return null;
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : null;
    }

    protected function normalizeString(?string $value): ?string
    {
        $value = $value !== null ? trim($value) : '';

        return $value !== '' ? $value : null;
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
