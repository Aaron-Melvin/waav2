<?php

use App\Models\Payment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::app'), Title('Payment Details')] class extends Component
{
    public Payment $payment;

    public function mount(Payment $payment): void
    {
        $this->ensureAdmin();

        $this->payment = $payment->load([
            'partner',
            'booking.customer',
            'refunds' => fn ($query) => $query->latest(),
        ]);
    }

    public function statusColor(string $status): string
    {
        return match ($status) {
            'captured' => 'green',
            'authorized' => 'blue',
            'pending' => 'amber',
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
