<?php

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::app'), Title('Products Overview')] class extends Component
{
    use WithPagination;

    public string $status = 'all';

    public string $type = 'all';

    public string $visibility = 'all';

    public string $search = '';

    public int $perPage = 15;

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
            ->with(['partner', 'location']);

        $this->applyStatusFilter($query);
        $this->applyTypeFilter($query);
        $this->applyVisibilityFilter($query);
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
                ->orWhereHas('partner', function (Builder $partnerQuery) use ($search): void {
                    $partnerQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                })
                ->orWhereHas('location', function (Builder $locationQuery) use ($search): void {
                    $locationQuery->where('name', 'like', "%{$search}%");
                });
        });
    }
};
