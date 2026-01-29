<?php

use App\Http\Requests\Partner\UpdateProductRequest;
use App\Models\Location;
use App\Models\Partner;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::app'), Title('Product Details')] class extends Component
{
    public Partner $partner;

    public Product $product;

    public string $name = '';

    public string $slug = '';

    public string $description = '';

    public ?string $capacityTotal = null;

    public ?string $defaultCurrency = null;

    public string $status = 'active';

    public string $visibility = 'public';

    public ?string $leadTimeMinutes = null;

    public ?string $cutoffMinutes = null;

    public ?string $locationId = null;

    public ?string $savedMessage = null;

    public function mount(Product $product): void
    {
        $this->partner = $this->resolvePartner();

        if ($product->partner_id !== $this->partner->id) {
            abort(404);
        }

        $this->product = $product->load('location');

        $this->fillForm();
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

    public function statusColor(string $status): string
    {
        return match ($status) {
            'active' => 'green',
            'inactive' => 'red',
            default => 'zinc',
        };
    }

    public function visibilityColor(string $visibility): string
    {
        return match ($visibility) {
            'public' => 'green',
            'unlisted' => 'amber',
            'private' => 'red',
            default => 'zinc',
        };
    }

    public function updateProduct(): void
    {
        $this->savedMessage = null;

        $payload = $this->productPayload();

        $validated = Validator::make(
            $payload,
            UpdateProductRequest::rulesFor($this->partner, $this->product),
            UpdateProductRequest::messagesFor()
        )->validate();

        $this->product->update($validated);
        $this->product->refresh()->load('location');

        $this->savedMessage = 'Product details updated.';
        $this->resetValidation();
    }

    protected function fillForm(): void
    {
        $this->name = $this->product->name;
        $this->slug = $this->product->slug;
        $this->description = $this->product->description ?? '';
        $this->capacityTotal = $this->product->capacity_total !== null
            ? (string) $this->product->capacity_total
            : null;
        $this->defaultCurrency = $this->product->default_currency;
        $this->status = $this->product->status;
        $this->visibility = $this->product->visibility;
        $this->leadTimeMinutes = $this->product->lead_time_minutes !== null
            ? (string) $this->product->lead_time_minutes
            : null;
        $this->cutoffMinutes = $this->product->cutoff_minutes !== null
            ? (string) $this->product->cutoff_minutes
            : null;
        $this->locationId = $this->product->location_id;
    }

    /**
     * @return array<string, mixed>
     */
    protected function productPayload(): array
    {
        $slug = trim($this->slug);

        return [
            'name' => trim($this->name),
            'slug' => $slug !== '' ? $slug : $this->product->slug,
            'description' => trim($this->description) !== '' ? trim($this->description) : null,
            'capacity_total' => $this->normalizeInteger($this->capacityTotal),
            'default_currency' => $this->normalizeString($this->defaultCurrency),
            'status' => $this->normalizeString($this->status),
            'visibility' => $this->normalizeString($this->visibility),
            'lead_time_minutes' => $this->normalizeInteger($this->leadTimeMinutes),
            'cutoff_minutes' => $this->normalizeInteger($this->cutoffMinutes),
            'location_id' => $this->normalizeString($this->locationId),
        ];
    }

    protected function normalizeInteger(?string $value): ?int
    {
        $value = $value !== null ? trim($value) : '';

        if ($value === '') {
            return null;
        }

        return (int) $value;
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
