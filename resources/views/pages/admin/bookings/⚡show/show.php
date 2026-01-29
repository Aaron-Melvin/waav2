<?php

use App\Models\Booking;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::app'), Title('Booking Details')] class extends Component
{
    public Booking $booking;

    public function mount(Booking $booking): void
    {
        $this->ensureAdmin();

        $this->booking = $booking->load([
            'partner',
            'customer',
            'coupon',
            'items' => fn ($query) => $query
                ->with(['product', 'event', 'unit'])
                ->orderBy('starts_on'),
            'allocations' => fn ($query) => $query
                ->with(['event.product', 'unit']),
            'payments' => fn ($query) => $query
                ->latest(),
            'invoices' => fn ($query) => $query
                ->latest(),
            'statusHistory' => fn ($query) => $query
                ->latest(),
        ]);
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

    public function paymentStatusColor(?string $status): string
    {
        return match ($status) {
            'paid', 'captured' => 'green',
            'pending', 'authorized' => 'amber',
            'failed', 'refunded' => 'red',
            default => 'zinc',
        };
    }

    protected function ensureAdmin(): void
    {
        if (! auth()->user()?->hasRole('super-admin')) {
            abort(403);
        }
    }
};
