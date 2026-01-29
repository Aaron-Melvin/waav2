<?php

use App\Http\Requests\Partner\StoreLocationRequest;
use App\Models\Location;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::app'), Title('Catalog Locations')] class extends Component
{
    use WithPagination;

    public Partner $partner;

    public string $status = 'all';

    public string $search = '';

    public int $perPage = 15;

    public ?string $createName = null;

    public ?string $createAddressLine1 = null;

    public ?string $createAddressLine2 = null;

    public ?string $createCity = null;

    public ?string $createRegion = null;

    public ?string $createPostalCode = null;

    public ?string $createCountryCode = null;

    public ?string $createLatitude = null;

    public ?string $createLongitude = null;

    public ?string $createTimezone = null;

    public string $createStatus = 'active';

    public ?string $savedMessage = null;

    public function mount(): void
    {
        $this->partner = $this->resolvePartner();
        $this->createTimezone = $this->partner->timezone;
    }

    public function updatedStatus(): void
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
     * @return LengthAwarePaginator<Location>
     */
    public function getLocationsProperty(): LengthAwarePaginator
    {
        $query = Location::query()
            ->where('partner_id', $this->partner->id);

        $this->applyStatusFilter($query);
        $this->applySearchFilter($query);

        return $query
            ->orderBy('name')
            ->paginate($this->perPage);
    }

    public function statusColor(string $status): string
    {
        return match ($status) {
            'active' => 'green',
            'inactive' => 'red',
            default => 'zinc',
        };
    }

    public function createLocation(): void
    {
        $this->savedMessage = null;

        $payload = $this->locationPayload();

        $validated = Validator::make(
            $payload,
            StoreLocationRequest::rulesFor(),
            StoreLocationRequest::messagesFor()
        )->validate();

        $validated['partner_id'] = $this->partner->id;

        Location::create($validated);

        $this->savedMessage = 'Location created.';
        $this->reset([
            'createName',
            'createAddressLine1',
            'createAddressLine2',
            'createCity',
            'createRegion',
            'createPostalCode',
            'createCountryCode',
            'createLatitude',
            'createLongitude',
        ]);
        $this->createStatus = 'active';
        $this->createTimezone = $this->partner->timezone;
        $this->resetValidation();
    }

    protected function applyStatusFilter(Builder $query): void
    {
        if ($this->status === 'all') {
            return;
        }

        $query->where('status', $this->status);
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
                ->orWhere('city', 'like', "%{$search}%")
                ->orWhere('region', 'like', "%{$search}%");
        });
    }

    /**
     * @return array<string, mixed>
     */
    protected function locationPayload(): array
    {
        return [
            'name' => $this->normalizeString($this->createName),
            'address_line_1' => $this->normalizeString($this->createAddressLine1),
            'address_line_2' => $this->normalizeString($this->createAddressLine2),
            'city' => $this->normalizeString($this->createCity),
            'region' => $this->normalizeString($this->createRegion),
            'postal_code' => $this->normalizeString($this->createPostalCode),
            'country_code' => $this->normalizeString($this->createCountryCode),
            'latitude' => $this->normalizeString($this->createLatitude),
            'longitude' => $this->normalizeString($this->createLongitude),
            'timezone' => $this->normalizeString($this->createTimezone),
            'status' => $this->normalizeString($this->createStatus) ?? 'active',
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
