<?php

use App\Models\Partner;
use App\Models\Product;
use App\Models\Location;
use App\Models\SearchIndex;
use Flux\DateRange;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::front'), Title('Search availability')] class extends Component
{
    public ?string $partnerId = null;

    public ?string $productId = null;

    public mixed $dateRange = null;

    public ?int $quantity = 1;

    public ?string $locationId = null;

    public ?string $productType = null;

    public string $sort = 'date';

    public bool $hasSearched = false;

    public ?string $lastHoldId = null;

    public ?string $holdMessage = null;

    public function updatedPartnerId(): void
    {
        $this->productId = null;
        $this->keepResultsVisible();
    }

    public function updatedProductId(): void
    {
        $this->keepResultsVisible();
    }

    public function updatedLocationId(): void
    {
        $this->productId = null;
        $this->keepResultsVisible();
    }

    public function updatedProductType(): void
    {
        $this->productId = null;
        $this->keepResultsVisible();
    }

    public function updatedSort(): void
    {
        $this->keepResultsVisible();
    }

    public function updatedDateRange(): void
    {
        $this->keepResultsVisible();
    }

    public function updatedQuantity(): void
    {
        $this->quantity = max((int) ($this->quantity ?? 1), 1);
        $this->keepResultsVisible();
    }

    public function search(): void
    {
        $this->hasSearched = true;
    }

    public function createHold(string $resultId, bool $redirect = false): void
    {
        $this->holdMessage = null;
        $this->lastHoldId = null;

        $result = SearchIndex::query()
            ->with(['product', 'partner', 'event', 'unit'])
            ->find($resultId);

        if (! $result || ! $result->product || ! $result->partner) {
            $this->holdMessage = 'This availability window is no longer available.';
            return;
        }

        $quantity = max((int) ($this->quantity ?? 1), 1);

        if ($result->capacity_available !== null && $result->capacity_available < $quantity) {
            $this->holdMessage = 'Not enough capacity remains for that request.';
            return;
        }

        $startsOn = $result->starts_on?->toDateString();
        $endsOn = $result->ends_on?->toDateString() ?? $startsOn;

        if (! $startsOn) {
            $this->holdMessage = 'This availability window is missing a start date.';
            return;
        }

        $hold = \App\Models\Hold::query()->create([
            'partner_id' => $result->partner_id,
            'product_id' => $result->product_id,
            'event_id' => $result->event_id,
            'unit_id' => $result->unit_id,
            'starts_on' => $startsOn,
            'ends_on' => $endsOn,
            'quantity' => $quantity,
            'status' => 'active',
            'expires_at' => now()->addMinutes(15),
            'meta' => [
                'source' => 'front-ui',
            ],
        ]);

        if ($result->unit_id && $startsOn && $endsOn) {
            $startDate = CarbonImmutable::parse($startsOn);
            $endDate = CarbonImmutable::parse($endsOn);

            for ($date = $startDate; $date->lessThanOrEqualTo($endDate); $date = $date->addDay()) {
                \App\Models\UnitHoldLock::query()->firstOrCreate([
                    'hold_id' => $hold->id,
                    'unit_id' => $result->unit_id,
                    'date' => $date->toDateString(),
                ]);
            }
        }

        $this->lastHoldId = $hold->id;
        $this->holdMessage = 'Hold created. You can complete your booking before it expires.';

        if ($redirect) {
            $this->redirectRoute('front.booking.details', ['hold' => $hold->id]);
        }
    }

    /**
     * @return Collection<int, Partner>
     */
    public function getPartnersProperty(): Collection
    {
        return Partner::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /**
     * @return Collection<int, Location>
     */
    public function getLocationsProperty(): Collection
    {
        return Location::query()
            ->where('status', 'active')
            ->when($this->partnerId, fn ($query) => $query->where('partner_id', $this->partnerId))
            ->orderBy('name')
            ->get(['id', 'name', 'partner_id', 'city']);
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProductsProperty(): Collection
    {
        return Product::query()
            ->where('status', 'active')
            ->where('visibility', 'public')
            ->when($this->partnerId, fn ($query) => $query->where('partner_id', $this->partnerId))
            ->when($this->locationId, fn ($query) => $query->where('location_id', $this->locationId))
            ->when($this->productType, fn ($query) => $query->where('type', $this->productType))
            ->orderBy('name')
            ->get(['id', 'name', 'partner_id']);
    }

    /**
     * @return Collection<int, SearchIndex>
     */
    public function getResultsProperty(): Collection
    {
        if (! $this->hasSearched) {
            return new Collection();
        }

        [$from, $to] = $this->resolveDateRangeBounds();

        if (! $from || ! $to) {
            return new Collection();
        }

        $quantity = max((int) ($this->quantity ?? 1), 1);

        $query = SearchIndex::query()
            ->with(['partner', 'product', 'location', 'event', 'unit'])
            ->when($this->productId, fn ($query) => $query->where('product_id', $this->productId))
            ->when($this->partnerId, fn ($query) => $query->where('partner_id', $this->partnerId))
            ->when($this->locationId, fn ($query) => $query->where('location_id', $this->locationId))
            ->when($this->productType, function ($query): void {
                $query->whereHas('product', function ($productQuery): void {
                    $productQuery->where('type', $this->productType);
                });
            })
            ->whereDate('starts_on', '<=', $to)
            ->whereDate('ends_on', '>=', $from)
            ->when($quantity, fn ($query) => $query->where('capacity_available', '>=', $quantity))
            ->limit(60);

        match ($this->sort) {
            'price_low' => $query->orderBy('price_min'),
            'price_high' => $query->orderByDesc('price_min'),
            'availability' => $query->orderByDesc('capacity_available'),
            default => $query->orderBy('starts_on'),
        };

        return $query->get();
    }

    public function getHasRangeProperty(): bool
    {
        [$from, $to] = $this->resolveDateRangeBounds();

        return (bool) ($from && $to);
    }

    protected function keepResultsVisible(): void
    {
        if ($this->hasRange) {
            $this->hasSearched = true;
        }
    }

    /**
     * @return array{0: ?string, 1: ?string}
     */
    protected function resolveDateRangeBounds(): array
    {
        if ($this->dateRange instanceof DateRange) {
            $start = $this->dateRange->start()?->toDateString();
            $end = $this->dateRange->end()?->toDateString();

            return [$start, $end ?? $start];
        }

        if (is_string($this->dateRange)) {
            $parts = array_map('trim', explode('/', $this->dateRange));
            $start = $parts[0] ?? null;
            $end = $parts[1] ?? $start;

            return [$start ?: null, $end ?: null];
        }

        if (is_array($this->dateRange)) {
            $start = $this->dateRange['start'] ?? null;
            $end = $this->dateRange['end'] ?? $start;

            return [$start ?: null, $end ?: null];
        }

        return [null, null];
    }
};
