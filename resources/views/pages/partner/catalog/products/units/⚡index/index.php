<?php

use App\Http\Requests\Partner\StoreUnitRequest;
use App\Models\Partner;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::app'), Title('Units')] class extends Component
{
    use WithPagination;

    public Partner $partner;

    public Product $product;

    public string $search = '';

    public string $status = 'all';

    public int $perPage = 15;

    public ?string $createName = null;

    public ?string $createCode = null;

    public ?string $createOccupancyAdults = null;

    public ?string $createOccupancyChildren = null;

    public string $createStatus = 'active';

    public string $createHousekeepingRequired = '0';

    public ?string $createMetaJson = null;

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
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    /**
     * @return LengthAwarePaginator<Unit>
     */
    public function getUnitsProperty(): LengthAwarePaginator
    {
        $query = Unit::query()
            ->where('partner_id', $this->partner->id)
            ->where('product_id', $this->product->id);

        $this->applySearchFilter($query);
        $this->applyStatusFilter($query);

        return $query
            ->orderBy('name')
            ->paginate($this->perPage);
    }

    public function createUnit(): void
    {
        $this->savedMessage = null;

        $payload = $this->unitPayload();

        $validated = Validator::make(
            $payload,
            StoreUnitRequest::rulesFor($this->product),
            StoreUnitRequest::messagesFor()
        )->validate();

        $meta = $this->decodeMeta($this->createMetaJson);

        if ($this->createMetaJson !== null && $meta === null) {
            $this->addError('meta', 'Metadata must be valid JSON.');
            return;
        }

        Unit::create([
            'partner_id' => $this->partner->id,
            'product_id' => $this->product->id,
            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'occupancy_adults' => $validated['occupancy_adults'],
            'occupancy_children' => $validated['occupancy_children'],
            'status' => $validated['status'],
            'housekeeping_required' => $validated['housekeeping_required'],
            'meta' => $meta,
        ]);

        $this->savedMessage = 'Unit created.';
        $this->reset(['createName', 'createCode', 'createOccupancyAdults', 'createOccupancyChildren', 'createMetaJson']);
        $this->createStatus = 'active';
        $this->createHousekeepingRequired = '0';
        $this->resetValidation();
    }

    protected function applySearchFilter(Builder $query): void
    {
        $search = trim($this->search);

        if ($search === '') {
            return;
        }

        $query->where(function (Builder $builder) use ($search): void {
            $builder
                ->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%");
        });
    }

    protected function applyStatusFilter(Builder $query): void
    {
        if ($this->status === 'all') {
            return;
        }

        $query->where('status', $this->status);
    }

    /**
     * @return array<string, mixed>
     */
    protected function unitPayload(): array
    {
        return [
            'name' => $this->normalizeString($this->createName),
            'code' => $this->normalizeString($this->createCode),
            'occupancy_adults' => $this->normalizeInteger($this->createOccupancyAdults),
            'occupancy_children' => $this->normalizeInteger($this->createOccupancyChildren),
            'status' => $this->normalizeString($this->createStatus) ?? 'active',
            'housekeeping_required' => $this->normalizeBoolean($this->createHousekeepingRequired),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function decodeMeta(?string $value): ?array
    {
        $value = $this->normalizeString($value);

        if ($value === null) {
            return null;
        }

        $decoded = json_decode($value, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            return null;
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

    protected function normalizeBoolean(string $value): bool
    {
        return $value === '1';
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
