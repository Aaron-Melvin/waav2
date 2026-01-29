<?php

use App\Models\Partner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::app'), Title('Partner Approvals')] class extends Component
{
    use WithPagination;

    public string $status = 'pending';

    public string $search = '';

    public int $perPage = 15;

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
     * @return \Illuminate\Pagination\LengthAwarePaginator<Partner>
     */
    public function getPartnersProperty(): LengthAwarePaginator
    {
        $query = Partner::query();

        $this->applyStatusFilter($query);
        $this->applySearchFilter($query);

        return $query
            ->orderBy('name')
            ->paginate($this->perPage);
    }

    public function setStatus(string $partnerId, string $status): void
    {
        $this->ensureAdmin();

        if (! in_array($status, ['active', 'inactive', 'pending'], true)) {
            return;
        }

        Partner::query()
            ->whereKey($partnerId)
            ->update([
                'status' => $status,
            ]);
    }

    public function statusColor(string $status): string
    {
        return match ($status) {
            'active' => 'green',
            'inactive' => 'red',
            default => 'amber',
        };
    }

    protected function applyStatusFilter(Builder $query): void
    {
        if (! in_array($this->status, ['active', 'inactive', 'pending'], true)) {
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
                ->orWhere('slug', 'like', "%{$search}%")
                ->orWhere('billing_email', 'like', "%{$search}%");
        });
    }

    protected function ensureAdmin(): void
    {
        if (! auth()->user()?->hasRole('super-admin')) {
            abort(403);
        }
    }
};
