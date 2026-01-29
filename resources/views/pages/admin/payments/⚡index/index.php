<?php

use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts::app'), Title('Payments Overview')] class extends Component
{
    use WithPagination;

    public string $status = 'all';

    public string $provider = 'all';

    public string $search = '';

    public int $perPage = 15;

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedProvider(): void
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
     * @return LengthAwarePaginator<Payment>
     */
    public function getPaymentsProperty(): LengthAwarePaginator
    {
        $query = Payment::query()
            ->with(['partner', 'booking']);

        $this->applyStatusFilter($query);
        $this->applyProviderFilter($query);
        $this->applySearchFilter($query);

        return $query
            ->orderByDesc('created_at')
            ->paginate($this->perPage);
    }

    public function statusColor(string $status): string
    {
        return match ($status) {
            'captured' => 'green',
            'authorized' => 'amber',
            'failed' => 'red',
            'refunded' => 'blue',
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

    protected function applyProviderFilter(Builder $query): void
    {
        if ($this->provider === 'all') {
            return;
        }

        $query->where('provider', $this->provider);
    }

    protected function applySearchFilter(Builder $query): void
    {
        $search = trim($this->search);

        if ($search === '') {
            return;
        }

        $query->where(function (Builder $innerQuery) use ($search): void {
            $innerQuery
                ->where('provider_payment_id', 'like', "%{$search}%")
                ->orWhereHas('booking', function (Builder $bookingQuery) use ($search): void {
                    $bookingQuery->where('booking_reference', 'like', "%{$search}%");
                })
                ->orWhereHas('partner', function (Builder $partnerQuery) use ($search): void {
                    $partnerQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
        });
    }
};
