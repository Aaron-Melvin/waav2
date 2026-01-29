<?php

use App\Http\Requests\Partner\StoreProductRequest;
use App\Models\Location;
use App\Models\Partner;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::app'), Title('Catalog Products')] class extends Component
{
    use WithPagination;

    public Partner $partner;

    public string $status = 'all';

    public string $type = 'all';

    public string $visibility = 'all';

    public string $search = '';

    public int $perPage = 15;

    public ?string $createName = null;

    public string $createType = 'event';

    public ?string $createSlug = null;

    public ?string $createLocationId = null;

    public ?string $createDescription = null;

    public ?string $createCapacityTotal = null;

    public ?string $createDefaultCurrency = null;

    public string $createStatus = 'active';

    public string $createVisibility = 'public';

    public ?string $createLeadTimeMinutes = null;

    public ?string $createCutoffMinutes = null;

    public ?string $savedMessage = null;

    public function mount(): void
    {
        $this->partner = $this->resolvePartner();
        $this->createDefaultCurrency = $this->partner->currency;
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedType(): void
    {
        $this->resetPage();
    }

    public function updatedVisibility(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    /**
     * @return LengthAwarePaginator<Product>
     */
    public function getProductsProperty(): LengthAwarePaginator
    {
        $query = Product::query()
            ->where('partner_id', $this->partner->id)
            ->with('location');

        $this->applyStatusFilter($query);
        $this->applyTypeFilter($query);
        $this->applyVisibilityFilter($query);
        $this->applySearchFilter($query);

        return $query
            ->orderBy('name')
            ->paginate($this->perPage);
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

    public function createProduct(): void
    {
        $this->savedMessage = null;

        $payload = $this->productPayload();

        $validated = Validator::make(
            $payload,
            StoreProductRequest::rulesFor($this->partner, $payload['type'] ?? null),
            StoreProductRequest::messagesFor()
        )->validate();

        $baseSlug = $validated['slug'] ?? Str::slug($validated['name']);
        $slug = $this->uniqueSlug($this->partner->id, $validated['type'], $baseSlug);

        Product::create([
            'partner_id' => $this->partner->id,
            'location_id' => $validated['location_id'] ?? null,
            'name' => $validated['name'],
            'type' => $validated['type'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'capacity_total' => $validated['capacity_total'] ?? null,
            'default_currency' => $validated['default_currency'] ?? $this->partner->currency ?? 'EUR',
            'status' => $validated['status'] ?? 'active',
            'visibility' => $validated['visibility'] ?? 'public',
            'lead_time_minutes' => $validated['lead_time_minutes'] ?? null,
            'cutoff_minutes' => $validated['cutoff_minutes'] ?? null,
            'meta' => $validated['meta'] ?? null,
        ]);

        $this->savedMessage = 'Product created.';
        $this->reset([
            'createName',
            'createSlug',
            'createLocationId',
            'createDescription',
            'createCapacityTotal',
            'createDefaultCurrency',
            'createLeadTimeMinutes',
            'createCutoffMinutes',
        ]);
        $this->createType = 'event';
        $this->createStatus = 'active';
        $this->createVisibility = 'public';
        $this->createDefaultCurrency = $this->partner->currency;
        $this->resetValidation();
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

    protected function applyStatusFilter(Builder $query): void
    {
        if ($this->status === 'all') {
            return;
        }

        $query->where('status', $this->status);
    }

    protected function applyTypeFilter(Builder $query): void
    {
        if ($this->type === 'all') {
            return;
        }

        $query->where('type', $this->type);
    }

    protected function applyVisibilityFilter(Builder $query): void
    {
        if ($this->visibility === 'all') {
            return;
        }

        $query->where('visibility', $this->visibility);
    }

    protected function applySearchFilter(Builder $query): void
    {
        $search = trim($this->search);

        if ($search === '') {
            return;
        }

        $query->where(function (Builder $innerQuery) use ($search): void {
            $innerQuery
                ->where('name', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%")
                ->orWhereHas('location', function (Builder $locationQuery) use ($search): void {
                    $locationQuery->where('name', 'like', "%{$search}%");
                });
        });
    }

    /**
     * @return array<string, mixed>
     */
    protected function productPayload(): array
    {
        $currency = $this->normalizeString($this->createDefaultCurrency);

        return [
            'name' => $this->normalizeString($this->createName),
            'type' => $this->normalizeString($this->createType) ?? 'event',
            'slug' => $this->normalizeString($this->createSlug),
            'description' => $this->normalizeString($this->createDescription),
            'capacity_total' => $this->normalizeString($this->createCapacityTotal),
            'default_currency' => $currency ? strtoupper($currency) : null,
            'status' => $this->normalizeString($this->createStatus) ?? 'active',
            'visibility' => $this->normalizeString($this->createVisibility) ?? 'public',
            'lead_time_minutes' => $this->normalizeString($this->createLeadTimeMinutes),
            'cutoff_minutes' => $this->normalizeString($this->createCutoffMinutes),
            'location_id' => $this->normalizeString($this->createLocationId),
        ];
    }

    protected function normalizeString(?string $value): ?string
    {
        $value = $value !== null ? trim($value) : '';

        return $value !== '' ? $value : null;
    }

    protected function uniqueSlug(string $partnerId, string $type, string $baseSlug): string
    {
        $baseSlug = $baseSlug !== '' ? $baseSlug : Str::random(8);
        $slug = $baseSlug;
        $suffix = 1;

        while (Product::query()
            ->where('partner_id', $partnerId)
            ->where('type', $type)
            ->where('slug', $slug)
            ->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
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
