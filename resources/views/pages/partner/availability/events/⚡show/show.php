<?php

use App\Http\Requests\Partner\UpdateEventRequest;
use App\Models\Event;
use App\Models\Partner;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::app'), Title('Event Details')] class extends Component
{
    public Partner $partner;

    public Event $event;

    public string $status = 'scheduled';

    public string $publishState = 'draft';

    public ?string $trafficLight = null;

    public ?string $capacityTotal = null;

    public ?string $capacityReserved = null;

    public ?string $savedMessage = null;

    public function mount(Event $event): void
    {
        $this->partner = $this->resolvePartner();

        if ($event->partner_id !== $this->partner->id) {
            abort(404);
        }

        $this->event = $event->load('product');

        $this->fillForm();
    }

    public function statusColor(string $status): string
    {
        return match ($status) {
            'scheduled' => 'blue',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'zinc',
        };
    }

    public function publishStateColor(string $state): string
    {
        return match ($state) {
            'published' => 'green',
            'draft' => 'amber',
            default => 'zinc',
        };
    }

    public function trafficLightColor(?string $trafficLight): string
    {
        return match ($trafficLight) {
            'green' => 'green',
            'yellow' => 'amber',
            'red' => 'red',
            default => 'zinc',
        };
    }

    public function updateEvent(): void
    {
        $this->savedMessage = null;

        $payload = $this->eventPayload();

        $validated = Validator::make(
            $payload,
            UpdateEventRequest::rulesFor(),
            UpdateEventRequest::messagesFor()
        )->validate();

        if ($validated['capacity_total'] !== null
            && $validated['capacity_reserved'] !== null
            && $validated['capacity_reserved'] > $validated['capacity_total']) {
            $this->addError('capacity_reserved', 'Reserved capacity may not exceed total capacity.');

            return;
        }

        if ($validated['capacity_total'] === null) {
            $validated['capacity_reserved'] = 0;
        }

        $this->event->update($validated);
        $this->event->refresh()->load('product');

        $this->savedMessage = 'Event availability updated.';
        $this->resetValidation();
    }

    protected function fillForm(): void
    {
        $this->status = $this->event->status;
        $this->publishState = $this->event->publish_state;
        $this->trafficLight = $this->event->traffic_light;
        $this->capacityTotal = $this->event->capacity_total !== null
            ? (string) $this->event->capacity_total
            : null;
        $this->capacityReserved = $this->event->capacity_reserved !== null
            ? (string) $this->event->capacity_reserved
            : null;
    }

    /**
     * @return array<string, mixed>
     */
    protected function eventPayload(): array
    {
        return [
            'status' => trim($this->status),
            'publish_state' => trim($this->publishState),
            'traffic_light' => $this->normalizeString($this->trafficLight),
            'capacity_total' => $this->normalizeInteger($this->capacityTotal),
            'capacity_reserved' => $this->normalizeInteger($this->capacityReserved),
        ];
    }

    protected function normalizeInteger(?string $value): ?int
    {
        $value = $value !== null ? trim($value) : '';

        if ($value === '') {
            return null;
        }

        return (int) $value;
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
