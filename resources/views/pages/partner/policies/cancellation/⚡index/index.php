<?php

use App\Http\Requests\Partner\StoreCancellationPolicyRequest;
use App\Http\Requests\Partner\UpdateCancellationPolicyRequest;
use App\Models\CancellationPolicy;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::app'), Title('Cancellation Policies')] class extends Component
{
    use WithPagination;

    public Partner $partner;

    public string $search = '';

    public string $status = 'all';

    public int $perPage = 15;

    public ?string $createName = null;

    public ?string $createDescription = null;

    public ?string $createRulesJson = null;

    public string $createStatus = 'active';

    public ?string $savedMessage = null;

    public ?string $editingId = null;

    public ?string $editName = null;

    public ?string $editDescription = null;

    public ?string $editRulesJson = null;

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
     * @return LengthAwarePaginator<CancellationPolicy>
     */
    public function getPoliciesProperty(): LengthAwarePaginator
    {
        $query = CancellationPolicy::query()
            ->where('partner_id', $this->partner->id);

        $this->applyStatusFilter($query);
        $this->applySearchFilter($query);

        return $query
            ->orderBy('name')
            ->paginate($this->perPage);
    }

    public function createPolicy(): void
    {
        $this->savedMessage = null;

        $rules = $this->decodeRules($this->createRulesJson);

        if ($rules === null) {
            $this->addError('rules', 'Rules must be valid JSON.');
            return;
        }

        $payload = [
            'name' => $this->normalizeString($this->createName),
            'description' => $this->normalizeString($this->createDescription),
            'rules' => $rules,
            'status' => $this->normalizeString($this->createStatus) ?? 'active',
        ];

        $validated = Validator::make(
            $payload,
            StoreCancellationPolicyRequest::rulesFor(),
            StoreCancellationPolicyRequest::messagesFor()
        )->validate();

        $validated['partner_id'] = $this->partner->id;

        CancellationPolicy::create($validated);

        $this->savedMessage = 'Cancellation policy created.';
        $this->reset(['createName', 'createDescription', 'createRulesJson']);
        $this->createStatus = 'active';
        $this->resetValidation();
    }

    public function startEditing(string $policyId): void
    {
        $policy = CancellationPolicy::query()
            ->where('partner_id', $this->partner->id)
            ->findOrFail($policyId);

        $this->editingId = $policy->id;
        $this->editName = $policy->name;
        $this->editDescription = $policy->description;
        $this->editRulesJson = json_encode($policy->rules ?? [], JSON_PRETTY_PRINT);
        $this->editStatus = $policy->status ?? 'active';
        $this->resetValidation();
    }

    public function cancelEditing(): void
    {
        $this->editingId = null;
        $this->editName = null;
        $this->editDescription = null;
        $this->editRulesJson = null;
        $this->editStatus = 'active';
        $this->resetValidation();
    }

    public function updatePolicy(): void
    {
        if (! $this->editingId) {
            return;
        }

        $rules = $this->decodeRules($this->editRulesJson);

        if ($rules === null) {
            $this->addError('edit_rules', 'Rules must be valid JSON.');
            return;
        }

        $payload = [
            'name' => $this->normalizeString($this->editName),
            'description' => $this->normalizeString($this->editDescription),
            'rules' => $rules,
            'status' => $this->normalizeString($this->editStatus) ?? 'active',
        ];

        $validated = Validator::make(
            $payload,
            UpdateCancellationPolicyRequest::rulesFor(),
            UpdateCancellationPolicyRequest::messagesFor()
        )->validate();

        CancellationPolicy::query()
            ->where('partner_id', $this->partner->id)
            ->where('id', $this->editingId)
            ->update($validated);

        $this->savedMessage = 'Cancellation policy updated.';
        $this->cancelEditing();
    }

    public function deletePolicy(string $policyId): void
    {
        CancellationPolicy::query()
            ->where('partner_id', $this->partner->id)
            ->where('id', $policyId)
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

    /**
     * @return array<int, array<string, mixed>>|null
     */
    protected function decodeRules(?string $rulesJson): ?array
    {
        $value = $this->normalizeString($rulesJson);

        if ($value === null) {
            return null;
        }

        $decoded = json_decode($value, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            return null;
        }

        return $decoded;
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
