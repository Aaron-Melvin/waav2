<?php

use App\Http\Requests\Partner\StoreTaxRequest;
use App\Http\Requests\Partner\UpdateTaxRequest;
use App\Models\Partner;
use App\Models\Tax;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::app'), Title('Taxes')] class extends Component
{
    use WithPagination;

    public Partner $partner;

    public string $search = '';

    public string $status = 'all';

    public int $perPage = 15;

    public ?string $createName = null;

    public ?string $createRate = null;

    public ?string $createAppliesTo = null;

    public string $createInclusive = '0';

    public string $createStatus = 'active';

    public ?string $savedMessage = null;

    public ?string $editingId = null;

    public ?string $editName = null;

    public ?string $editRate = null;

    public ?string $editAppliesTo = null;

    public string $editInclusive = '0';

    public string $editStatus = 'active';

    public function mount(): void
    {
        $this->partner = $this->resolvePartner();
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
     * @return LengthAwarePaginator<Tax>
     */
    public function getTaxesProperty(): LengthAwarePaginator
    {
        $query = Tax::query()
            ->where('partner_id', $this->partner->id);

        $this->applyStatusFilter($query);
        $this->applySearchFilter($query);

        return $query
            ->orderBy('name')
            ->paginate($this->perPage);
    }

    public function createTax(): void
    {
        $this->savedMessage = null;

        $payload = [
            'name' => $this->normalizeString($this->createName),
            'rate' => $this->normalizeString($this->createRate),
            'applies_to' => $this->normalizeString($this->createAppliesTo),
            'is_inclusive' => $this->normalizeBoolean($this->createInclusive),
            'status' => $this->normalizeString($this->createStatus) ?? 'active',
        ];

        $validated = Validator::make(
            $payload,
            StoreTaxRequest::rulesFor(),
            StoreTaxRequest::messagesFor()
        )->validate();

        $validated['partner_id'] = $this->partner->id;

        Tax::create($validated);

        $this->savedMessage = 'Tax created.';
        $this->reset(['createName', 'createRate', 'createAppliesTo']);
        $this->createInclusive = '0';
        $this->createStatus = 'active';
        $this->resetValidation();
    }

    public function startEditing(string $taxId): void
    {
        $tax = Tax::query()
            ->where('partner_id', $this->partner->id)
            ->findOrFail($taxId);

        $this->editingId = $tax->id;
        $this->editName = $tax->name;
        $this->editRate = (string) $tax->rate;
        $this->editAppliesTo = $tax->applies_to;
        $this->editInclusive = $tax->is_inclusive ? '1' : '0';
        $this->editStatus = $tax->status ?? 'active';
        $this->resetValidation();
    }

    public function cancelEditing(): void
    {
        $this->editingId = null;
        $this->editName = null;
        $this->editRate = null;
        $this->editAppliesTo = null;
        $this->editInclusive = '0';
        $this->editStatus = 'active';
        $this->resetValidation();
    }

    public function updateTax(): void
    {
        if (! $this->editingId) {
            return;
        }

        $payload = [
            'name' => $this->normalizeString($this->editName),
            'rate' => $this->normalizeString($this->editRate),
            'applies_to' => $this->normalizeString($this->editAppliesTo),
            'is_inclusive' => $this->normalizeBoolean($this->editInclusive),
            'status' => $this->normalizeString($this->editStatus) ?? 'active',
        ];

        $validated = Validator::make(
            $payload,
            UpdateTaxRequest::rulesFor(),
            UpdateTaxRequest::messagesFor()
        )->validate();

        Tax::query()
            ->where('partner_id', $this->partner->id)
            ->where('id', $this->editingId)
            ->update($validated);

        $this->savedMessage = 'Tax updated.';
        $this->cancelEditing();
    }

    public function deleteTax(string $taxId): void
    {
        Tax::query()
            ->where('partner_id', $this->partner->id)
            ->where('id', $taxId)
            ->delete();
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

        $query->where('name', 'like', "%{$search}%");
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
