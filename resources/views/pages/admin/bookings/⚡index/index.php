<?php

use App\Models\Booking;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::app'), Title('Bookings Overview')] class extends Component
{
    use WithPagination;

    public string $status = 'all';

    public string $search = '';

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    public int $perPage = 15;

    public function updatedStatus(): void
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
     * @return LengthAwarePaginator<Booking>
     */
    public function getBookingsProperty(): LengthAwarePaginator
    {
        $query = Booking::query()
            ->with(['partner', 'customer']);

        $this->applyStatusFilter($query);
        $this->applySearchFilter($query);
        $this->applyDateFilter($query);

        return $query
            ->orderByDesc('created_at')
            ->paginate($this->perPage);
    }

    public function statusColor(string $status): string
    {
        return match ($status) {
            'confirmed' => 'green',
            'completed' => 'blue',
            'cancelled' => 'red',
            'pending_payment' => 'amber',
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
                ->where('booking_reference', 'like', "%{$search}%")
                ->orWhereHas('customer', function (Builder $customerQuery) use ($search): void {
                    $customerQuery
                        ->where('email', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                })
                ->orWhereHas('partner', function (Builder $partnerQuery) use ($search): void {
                    $partnerQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
        });
    }

    protected function applyDateFilter(Builder $query): void
    {
        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }
    }

    protected function ensureAdmin(): void
    {
        if (! auth()->user()?->hasRole('super-admin')) {
            abort(403);
        }
    }
};
