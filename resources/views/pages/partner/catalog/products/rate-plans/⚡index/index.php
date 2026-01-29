<?php

use App\Http\Requests\Partner\UpdateRatePlanRequest;
use App\Models\CancellationPolicy;
use App\Models\Partner;
use App\Models\Product;
use App\Models\RatePlan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::app'), Title('Rate Plans')] class extends Component
{
    public Partner $partner;

    public Product $product;

    public string $name = '';

    public ?string $code = null;

    public string $pricingModel = 'per_night';

    public string $currency = 'EUR';

    public string $status = 'active';

    public ?string $cancellationPolicyId = null;

    public ?string $savedMessage = null;

    public function mount(Product $product): void
    {
        $this->partner = $this->resolvePartner();

        if ($product->partner_id !== $this->partner->id) {
            abort(404);
        }

        if ($product->type !== 'accommodation') {
            abort(404);
        }

        $this->product = $product->load('location');
        $this->currency = $product->default_currency ?? $this->partner->currency ?? 'EUR';
    }

    /**
     * @return Collection<int, RatePlan>
     */
    public function getRatePlansProperty(): Collection
    {
        return RatePlan::query()
            ->where('partner_id', $this->partner->id)
            ->where('product_id', $this->product->id)
            ->withCount('prices')
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

    public function createRatePlan(): void
    {
        $this->savedMessage = null;

        $payload = $this->ratePlanPayload();

        $validated = Validator::make(
            $payload,
            UpdateRatePlanRequest::rulesForCreate($this->partner, $this->product),
            UpdateRatePlanRequest::messagesFor()
        )->validate();

        $validated['partner_id'] = $this->partner->id;
        $validated['product_id'] = $this->product->id;

        RatePlan::create($validated);

        $this->savedMessage = 'Rate plan created.';
        $this->reset(['name', 'code', 'pricingModel', 'status', 'cancellationPolicyId']);
        $this->currency = $this->product->default_currency ?? $this->partner->currency ?? 'EUR';
        $this->resetValidation();
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
