<?php

use App\Http\Requests\Partner\StoreEventOverrideRequest;
use App\Models\Event;
use App\Models\EventOverride;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::app'), Title('Event Overrides')] class extends Component
{
    public Partner $partner;

    public Event $event;

    public string $overrideField = 'capacity_total';

    public ?string $overrideValue = null;

    public ?string $overrideCurrency = null;

    public ?string $savedMessage = null;

    public function mount(Event $event): void
    {
        $this->partner = $this->resolvePartner();

        if ($event->partner_id !== $this->partner->id) {
            abort(404);
        }

        $this->event = $event->load('product');
        $this->overrideCurrency = $this->event->product?->default_currency ?? $this->partner->currency;
    }

    /**
     * @return Collection<int, EventOverride>
     */
    public function getOverridesProperty(): Collection
    {
        return EventOverride::query()
            ->where('event_id', $this->event->id)
            ->orderByDesc('created_at')
            ->get();
    }

    public function addOverride(): void
    {
        $this->savedMessage = null;

        $payload = $this->overridePayload();

        $validated = Validator::make(
            $payload,
            StoreEventOverrideRequest::rulesFor($payload['field']),
            StoreEventOverrideRequest::messagesFor()
        )->validate();

        $value = $this->formatOverrideValue($validated['field'], $validated['value'], $validated['currency'] ?? null);

        EventOverride::create([
            'event_id' => $this->event->id,
            'field' => $validated['field'],
            'value' => $value,
        ]);

        $this->savedMessage = 'Override added.';
        $this->reset(['overrideValue']);
        $this->resetValidation();
    }

    public function deleteOverride(string $overrideId): void
    {
        EventOverride::query()
            ->where('event_id', $this->event->id)
            ->where('id', $overrideId)
            ->delete();
    }

    public function displayValue(EventOverride $override): string
    {
        $value = $override->value;

        if (! is_array($value)) {
            return '—';
        }

        if ($override->field === 'price_override') {
            $amount = $value['value'] ?? null;
            $currency = $value['currency'] ?? null;

            if ($amount === null) {
                return '—';
            }

            return trim($currency ? $currency.' '.number_format((float) $amount, 2) : number_format((float) $amount, 2));
        }

        $raw = $value['value'] ?? null;

        if ($raw === null) {
            return '—';
        }

        return is_scalar($raw) ? (string) $raw : json_encode($raw);
    }

    /**
     * @return array<string, mixed>
     */
    protected function overridePayload(): array
    {
        return [
            'field' => trim($this->overrideField),
            'value' => $this->normalizeString($this->overrideValue),
            'currency' => $this->normalizeString($this->overrideCurrency),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function formatOverrideValue(string $field, mixed $value, ?string $currency): array
    {
        return match ($field) {
            'capacity_total' => ['value' => (int) $value],
            'price_override' => [
                'value' => (float) $value,
                'currency' => $currency ? strtoupper($currency) : null,
            ],
            default => ['value' => (string) $value],
        };
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
