<?php

use App\Models\Location;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::app'), Title('Locations Overview')] class extends Component
{
    use WithPagination;

    public string $status = 'all';

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
     * @return LengthAwarePaginator<Location>
     */
    public function getLocationsProperty(): LengthAwarePaginator
    {
        $query = Location::query()
            ->with('partner');

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
                ->orWhere('region', 'like', "%{$search}%")
                ->orWhere('country_code', 'like', "%{$search}%")
                ->orWhereHas('partner', function (Builder $partnerQuery) use ($search): void {
                    $partnerQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
        });
    }
};
