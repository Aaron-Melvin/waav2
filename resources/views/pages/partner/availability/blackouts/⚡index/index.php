<?php

use App\Http\Requests\Partner\UpdateEventBlackoutRequest;
use App\Models\EventBlackout;
use App\Models\Location;
use App\Models\Partner;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::app'), Title('Event Blackouts')] class extends Component
{
    use WithPagination;

    public Partner $partner;

    public string $search = '';

    public string $filterStatus = 'all';

    public int $perPage = 15;

    public ?string $filterProductId = null;

    public ?string $filterLocationId = null;

    public ?string $filterDateFrom = null;

    public ?string $filterDateTo = null;

    public ?string $productId = null;

    public ?string $locationId = null;

    public ?string $startsAt = null;

    public ?string $endsAt = null;

    public ?string $reason = null;

    public string $status = 'active';

    public ?string $savedMessage = null;

    public function mount(): void
    {
        $this->partner = $this->resolvePartner();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function updatedFilterProductId(): void
    {
        $this->resetPage();
    }

    public function updatedFilterLocationId(): void
    {
        $this->resetPage();
    }

    public function updatedFilterDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatedFilterDateTo(): void
    {
        $this->resetPage();
    }

    /**
     * @return LengthAwarePaginator<EventBlackout>
     */
    public function getBlackoutsProperty(): LengthAwarePaginator
    {
        $query = EventBlackout::query()
            ->where('partner_id', $this->partner->id)
            ->with(['product', 'location']);

        $this->applySearchFilter($query);
        $this->applyStatusFilter($query);
        $this->applyProductFilter($query);
        $this->applyLocationFilter($query);
        $this->applyDateFilter($query);

        return $query
            ->orderByDesc('starts_at')
            ->paginate($this->perPage);
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProductsProperty(): Collection
    {
        return Product::query()
            ->where('partner_id', $this->partner->id)
            ->orderBy('name')
            ->get();
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

    public function createBlackout(): void
    {
        $this->savedMessage = null;

        $payload = $this->blackoutPayload();

        $validated = Validator::make(
            $payload,
            UpdateEventBlackoutRequest::rulesFor($this->partner),
            UpdateEventBlackoutRequest::messagesFor()
        )->validate();

        $validated['partner_id'] = $this->partner->id;

        EventBlackout::create($validated);

        $this->savedMessage = 'Blackout created.';
        $this->reset(['productId', 'locationId', 'startsAt', 'endsAt', 'reason', 'status']);
        $this->status = 'active';
        $this->resetValidation();
    }

    protected function blackoutPayload(): array
    {
        return [
            'product_id' => $this->normalizeString($this->productId),
            'location_id' => $this->normalizeString($this->locationId),
            'starts_at' => $this->normalizeString($this->startsAt),
            'ends_at' => $this->normalizeString($this->endsAt),
            'reason' => $this->normalizeString($this->reason),
            'status' => $this->normalizeString($this->status) ?? 'active',
        ];
    }

    protected function applySearchFilter(Builder $query): void
    {
        $search = trim($this->search);

        if ($search === '') {
            return;
        }

        $query->where(function (Builder $builder) use ($search): void {
            $builder
                ->whereHas('product', function (Builder $productQuery) use ($search): void {
                    $productQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                })
                ->orWhereHas('location', function (Builder $locationQuery) use ($search): void {
                    $locationQuery->where('name', 'like', "%{$search}%");
                });
        });
    }

    protected function applyStatusFilter(Builder $query): void
    {
        if ($this->filterStatus === 'all') {
            return;
        }

        $query->where('status', $this->filterStatus);
    }

    protected function applyProductFilter(Builder $query): void
    {
        if (! $this->filterProductId) {
            return;
        }

        $query->where('product_id', $this->filterProductId);
    }

    protected function applyLocationFilter(Builder $query): void
    {
        if (! $this->filterLocationId) {
            return;
        }

        $query->where('location_id', $this->filterLocationId);
    }

    protected function applyDateFilter(Builder $query): void
    {
        if ($this->filterDateFrom) {
            $query->whereDate('ends_at', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo) {
            $query->whereDate('starts_at', '<=', $this->filterDateTo);
        }
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
}
