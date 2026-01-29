<?php

use App\Http\Requests\Partner\StoreEventRequest;
use App\Models\Event;
use App\Models\EventSeries;
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

new #[Layout('layouts::app'), Title('Event Availability')] class extends Component
{
    use WithPagination;

    public Partner $partner;

    public string $status = 'all';

    public string $publishState = 'all';

    public string $trafficLight = 'all';

    public string $search = '';

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    public int $perPage = 15;

    public ?string $createProductId = null;

    public ?string $createEventSeriesId = null;

    public ?string $createStartsAt = null;

    public ?string $createEndsAt = null;

    public ?string $createCapacityTotal = null;

    public ?string $createCapacityReserved = null;

    public ?string $createTrafficLight = null;

    public string $createStatus = 'scheduled';

    public string $createPublishState = 'draft';

    public bool $createWeatherAlert = false;

    public ?string $savedMessage = null;

    public function mount(): void
    {
        $this->partner = $this->resolvePartner();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedPublishState(): void
    {
        $this->resetPage();
    }

    public function updatedTrafficLight(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatedDateTo(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    /**
     * @return LengthAwarePaginator<Event>
     */
    public function getEventsProperty(): LengthAwarePaginator
    {
        $query = Event::query()
            ->where('partner_id', $this->partner->id)
            ->with('product');

        $this->applyStatusFilter($query);
        $this->applyPublishStateFilter($query);
        $this->applyTrafficLightFilter($query);
        $this->applySearchFilter($query);
        $this->applyDateFilter($query);

        return $query
            ->orderBy('starts_at')
            ->paginate($this->perPage);
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProductsProperty(): Collection
    {
        return Product::query()
            ->where('partner_id', $this->partner->id)
            ->where('type', 'event')
            ->orderBy('name')
            ->get();
    }

    /**
     * @return Collection<int, EventSeries>
     */
    public function getEventSeriesProperty(): Collection
    {
        return EventSeries::query()
            ->where('partner_id', $this->partner->id)
            ->orderBy('name')
            ->get();
    }

    public function createEvent(): void
    {
        $this->savedMessage = null;

        $payload = $this->eventPayload();

        $validated = Validator::make(
            $payload,
            StoreEventRequest::rulesFor($this->partner),
            StoreEventRequest::messagesFor()
        )->validate();

        Event::create([
            'partner_id' => $this->partner->id,
            'product_id' => $validated['product_id'],
            'event_series_id' => $validated['event_series_id'] ?? null,
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'capacity_total' => $validated['capacity_total'] ?? null,
            'capacity_reserved' => $validated['capacity_reserved'] ?? 0,
            'traffic_light' => $validated['traffic_light'] ?? null,
            'status' => $validated['status'] ?? 'scheduled',
            'publish_state' => $validated['publish_state'] ?? 'draft',
            'weather_alert' => $validated['weather_alert'] ?? false,
        ]);

        $this->savedMessage = 'Event created.';
        $this->reset([
            'createProductId',
            'createEventSeriesId',
            'createStartsAt',
            'createEndsAt',
            'createCapacityTotal',
            'createCapacityReserved',
            'createTrafficLight',
        ]);
        $this->createStatus = 'scheduled';
        $this->createPublishState = 'draft';
        $this->createWeatherAlert = false;
        $this->resetValidation();
    }

    public function statusColor(string $status): string
    {
        return match ($status) {
            'scheduled' => 'blue',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'zinc',
        };
    }

    public function publishStateColor(string $state): string
    {
        return match ($state) {
            'published' => 'green',
            'draft' => 'amber',
            default => 'zinc',
        };
    }

    public function trafficLightColor(?string $trafficLight): string
    {
        return match ($trafficLight) {
            'green' => 'green',
            'yellow' => 'amber',
            'red' => 'red',
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

    protected function applyPublishStateFilter(Builder $query): void
    {
        if ($this->publishState === 'all') {
            return;
        }

        $query->where('publish_state', $this->publishState);
    }

    protected function applyTrafficLightFilter(Builder $query): void
    {
        if ($this->trafficLight === 'all') {
            return;
        }

        $query->where('traffic_light', $this->trafficLight);
    }

    protected function applySearchFilter(Builder $query): void
    {
        $search = trim($this->search);

        if ($search === '') {
            return;
        }

        $query->whereHas('product', function (Builder $productQuery) use ($search): void {
            $productQuery
                ->where('name', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%");
        });
    }

    protected function applyDateFilter(Builder $query): void
    {
        if ($this->dateFrom) {
            $query->whereDate('starts_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('starts_at', '<=', $this->dateTo);
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function eventPayload(): array
    {
        return [
            'product_id' => $this->normalizeString($this->createProductId),
            'event_series_id' => $this->normalizeString($this->createEventSeriesId),
            'starts_at' => $this->normalizeString($this->createStartsAt),
            'ends_at' => $this->normalizeString($this->createEndsAt),
            'capacity_total' => $this->normalizeString($this->createCapacityTotal),
            'capacity_reserved' => $this->normalizeString($this->createCapacityReserved),
            'traffic_light' => $this->normalizeString($this->createTrafficLight),
            'status' => $this->normalizeString($this->createStatus) ?? 'scheduled',
            'publish_state' => $this->normalizeString($this->createPublishState) ?? 'draft',
            'weather_alert' => $this->createWeatherAlert,
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
