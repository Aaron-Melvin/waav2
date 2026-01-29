<?php

use App\Models\Booking;
use App\Models\BookingAllocation;
use App\Models\BookingStatusHistory;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\UnitBookingLock;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::front'), Title('Confirm booking')] class extends Component
{
    public Booking $booking;

    public string $paymentMethod = 'manual';

    public ?string $paymentToken = null;

    public string $status = 'captured';

    public bool $confirmed = false;

    public ?string $errorMessage = null;

    public function mount(Booking $booking): void
    {
        $this->booking = $booking->loadMissing(['items', 'customer', 'partner']);
        $this->confirmed = $this->booking->status === 'confirmed';
    }

    /**
     * @return array<string, array<int, string>>
     */
    protected function rules(): array
    {
        return [
            'paymentMethod' => ['required', 'string', 'max:50'],
            'paymentToken' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:captured,authorized'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'paymentMethod.required' => 'A payment method is required.',
            'paymentMethod.max' => 'Payment method may not exceed 50 characters.',
            'status.in' => 'Status must be captured or authorized.',
        ];
    }

    public function confirmBooking(): void
    {
        $this->errorMessage = null;

        if ($this->confirmed) {
            return;
        }

        if (! in_array($this->booking->status, ['draft', 'pending_payment'], true)) {
            $this->errorMessage = 'Booking cannot be confirmed from its current status.';
            return;
        }

        $this->validate();

        $fromStatus = $this->booking->status;
        $paymentStatus = $this->status === 'captured' ? 'paid' : 'pending';

        $this->booking->update([
            'status' => 'confirmed',
            'payment_status' => $paymentStatus,
        ]);

        $payment = Payment::query()->create([
            'partner_id' => $this->booking->partner_id,
            'booking_id' => $this->booking->id,
            'provider' => $this->paymentMethod,
            'provider_payment_id' => $this->paymentToken,
            'amount' => $this->booking->total_gross,
            'currency' => $this->booking->currency,
            'status' => $this->status,
            'captured_at' => $this->status === 'captured'
                ? CarbonImmutable::now()
                : null,
        ]);

        Invoice::query()->create([
            'partner_id' => $this->booking->partner_id,
            'booking_id' => $this->booking->id,
            'number' => $this->generateInvoiceNumber(),
            'currency' => $this->booking->currency,
            'total_gross' => $this->booking->total_gross,
            'total_tax' => $this->booking->total_tax,
            'total_fees' => $this->booking->total_fees,
            'status' => 'issued',
            'issued_at' => CarbonImmutable::now(),
            'due_at' => CarbonImmutable::now()->addDays(14),
            'meta' => [
                'payment_id' => $payment->id,
            ],
        ]);

        $this->ensureBookingAllocations();
        $this->createUnitLocks();

        BookingStatusHistory::query()->create([
            'booking_id' => $this->booking->id,
            'from_status' => $fromStatus,
            'to_status' => 'confirmed',
            'reason' => 'Payment confirmed',
        ]);

        $this->booking->refresh()->loadMissing(['items', 'customer', 'partner']);
        $this->confirmed = true;
    }

    protected function ensureBookingAllocations(): void
    {
        $this->booking->loadMissing('items');

        foreach ($this->booking->items as $item) {
            BookingAllocation::query()->firstOrCreate([
                'booking_id' => $this->booking->id,
                'event_id' => $item->event_id,
                'unit_id' => $item->unit_id,
            ], [
                'quantity' => $item->quantity,
            ]);
        }
    }

    protected function createUnitLocks(): void
    {
        $this->booking->loadMissing('items');

        foreach ($this->booking->items as $item) {
            if (! $item->unit_id || ! $item->starts_on || ! $item->ends_on) {
                continue;
            }

            $start = CarbonImmutable::parse($item->starts_on);
            $end = CarbonImmutable::parse($item->ends_on);

            for ($date = $start; $date->lessThanOrEqualTo($end); $date = $date->addDay()) {
                UnitBookingLock::query()->firstOrCreate([
                    'booking_id' => $this->booking->id,
                    'unit_id' => $item->unit_id,
                    'date' => $date->toDateString(),
                ]);
            }
        }
    }

    protected function generateInvoiceNumber(): string
    {
        do {
            $number = 'INV-'.Str::upper(Str::random(6));
        } while (Invoice::query()->where('number', $number)->exists());

        return $number;
    }
};
