<?php

use App\Http\Requests\Partner\StoreFeeRequest;
use App\Http\Requests\Partner\UpdateFeeRequest;
use App\Models\Fee;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::app'), Title('Fees')] class extends Component
{
    use WithPagination;

    public Partner $partner;

    public string $search = '';

    public string $status = 'all';

    public int $perPage = 15;

    public ?string $createName = null;

    public ?string $createType = null;

    public ?string $createAmount = null;

    public ?string $createAppliesTo = null;

    public string $createStatus = 'active';

    public ?string $savedMessage = null;

    public ?string $editingId = null;

    public ?string $editName = null;

    public ?string $editType = null;

    public ?string $editAmount = null;

    public ?string $editAppliesTo = null;

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
     * @return LengthAwarePaginator<Fee>
     */
    public function getFeesProperty(): LengthAwarePaginator
    {
        $query = Fee::query()
            ->where('partner_id', $this->partner->id);

        $this->applyStatusFilter($query);
        $this->applySearchFilter($query);

        return $query
            ->orderBy('name')
            ->paginate($this->perPage);
    }

    public function createFee(): void
    {
        $this->savedMessage = null;

        $payload = [
            'name' => $this->normalizeString($this->createName),
            'type' => $this->normalizeString($this->createType) ?? 'flat',
            'amount' => $this->normalizeString($this->createAmount),
            'applies_to' => $this->normalizeString($this->createAppliesTo),
            'status' => $this->normalizeString($this->createStatus) ?? 'active',
        ];

        $validated = Validator::make(
            $payload,
            StoreFeeRequest::rulesFor(),
            StoreFeeRequest::messagesFor()
        )->validate();

        $validated['partner_id'] = $this->partner->id;

        Fee::create($validated);

        $this->savedMessage = 'Fee created.';
        $this->reset(['createName', 'createType', 'createAmount', 'createAppliesTo']);
        $this->createStatus = 'active';
        $this->resetValidation();
    }

    public function startEditing(string $feeId): void
    {
        $fee = Fee::query()
            ->where('partner_id', $this->partner->id)
            ->findOrFail($feeId);

        $this->editingId = $fee->id;
        $this->editName = $fee->name;
        $this->editType = $fee->type ?? 'flat';
        $this->editAmount = (string) $fee->amount;
        $this->editAppliesTo = $fee->applies_to;
        $this->editStatus = $fee->status ?? 'active';
        $this->resetValidation();
    }

    public function cancelEditing(): void
    {
        $this->editingId = null;
        $this->editName = null;
        $this->editType = null;
        $this->editAmount = null;
        $this->editAppliesTo = null;
        $this->editStatus = 'active';
        $this->resetValidation();
    }

    public function updateFee(): void
    {
        if (! $this->editingId) {
            return;
        }

        $payload = [
            'name' => $this->normalizeString($this->editName),
            'type' => $this->normalizeString($this->editType) ?? 'flat',
            'amount' => $this->normalizeString($this->editAmount),
            'applies_to' => $this->normalizeString($this->editAppliesTo),
            'status' => $this->normalizeString($this->editStatus) ?? 'active',
        ];

        $validated = Validator::make(
            $payload,
            UpdateFeeRequest::rulesFor(),
            UpdateFeeRequest::messagesFor()
        )->validate();

        Fee::query()
            ->where('partner_id', $this->partner->id)
            ->where('id', $this->editingId)
            ->update($validated);

        $this->savedMessage = 'Fee updated.';
        $this->cancelEditing();
    }

    public function deleteFee(string $feeId): void
    {
        Fee::query()
            ->where('partner_id', $this->partner->id)
            ->where('id', $feeId)
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
