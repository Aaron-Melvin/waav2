<?php

use App\Http\Requests\Partner\UpdateEventBlackoutRequest;
use App\Models\EventBlackout;
use App\Models\Location;
use App\Models\Partner;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::app'), Title('Blackout Details')] class extends Component
{
    public Partner $partner;

    public EventBlackout $blackout;

    public ?string $productId = null;

    public ?string $locationId = null;

    public ?string $startsAt = null;

    public ?string $endsAt = null;

    public ?string $reason = null;

    public string $status = 'active';

    public ?string $savedMessage = null;

    public function mount(EventBlackout $blackout): void
    {
        $this->partner = $this->resolvePartner();

        if ($blackout->partner_id !== $this->partner->id) {
            abort(404);
        }

        $this->blackout = $blackout->load(['product', 'location']);
        $this->fillForm();
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProductsProperty(): Collection
    {
        return Product::query()
            ->where('partner_id', $this->partner->id)
            ->orderBy('name')
            ->get();
    }

    /**
     * @return Collection<int, Location>
     */
    public function getLocationsProperty(): Collection
    {
        return Location::query()
            ->where('partner_id', $this->partner->id)
            ->orderBy('name')
            ->get();
    }

    public function updateBlackout(): void
    {
        $this->savedMessage = null;

        $payload = $this->blackoutPayload();

        $validated = Validator::make(
            $payload,
            UpdateEventBlackoutRequest::rulesFor($this->partner),
            UpdateEventBlackoutRequest::messagesFor()
        )->validate();

        $this->blackout->update($validated);
        $this->blackout->refresh()->load(['product', 'location']);

        $this->savedMessage = 'Blackout updated.';
        $this->resetValidation();
    }

    public function deleteBlackout(): RedirectResponse
    {
        $this->blackout->delete();

        return redirect()->route('partner.availability.blackouts.index');
    }

    protected function fillForm(): void
    {
        $this->productId = $this->blackout->product_id;
        $this->locationId = $this->blackout->location_id;
        $this->startsAt = $this->blackout->starts_at?->format('Y-m-d');
        $this->endsAt = $this->blackout->ends_at?->format('Y-m-d');
        $this->reason = $this->blackout->reason;
        $this->status = $this->blackout->status;
    }

    /**
     * @return array<string, mixed>
     */
    protected function blackoutPayload(): array
    {
        return [
            'product_id' => $this->normalizeString($this->productId),
            'location_id' => $this->normalizeString($this->locationId),
            'starts_at' => $this->normalizeString($this->startsAt),
            'ends_at' => $this->normalizeString($this->endsAt),
            'reason' => $this->normalizeString($this->reason),
            'status' => $this->normalizeString($this->status) ?? 'active',
        ];
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
}
