<?php

use App\Http\Requests\Partner\UpdateLocationRequest;
use App\Models\Location;
use App\Models\Partner;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::app'), Title('Location Details')] class extends Component
{
    public Partner $partner;

    public Location $location;

    public string $name = '';

    public ?string $addressLine1 = null;

    public ?string $addressLine2 = null;

    public ?string $city = null;

    public ?string $region = null;

    public ?string $postalCode = null;

    public ?string $countryCode = null;

    public ?string $latitude = null;

    public ?string $longitude = null;

    public string $timezone = '';

    public string $status = 'active';

    public ?string $savedMessage = null;

    public function mount(Location $location): void
    {
        $this->partner = $this->resolvePartner();

        if ($location->partner_id !== $this->partner->id) {
            abort(404);
        }

        $this->location = $location;

        $this->fillForm();
    }

    public function statusColor(string $status): string
    {
        return match ($status) {
            'active' => 'green',
            'inactive' => 'red',
            default => 'zinc',
        };
    }

    public function updateLocation(): void
    {
        $this->savedMessage = null;

        $payload = $this->locationPayload();

        $validated = Validator::make(
            $payload,
            UpdateLocationRequest::rulesFor(),
            UpdateLocationRequest::messagesFor()
        )->validate();

        $this->location->update($validated);
        $this->location->refresh();

        $this->savedMessage = 'Location details updated.';
        $this->resetValidation();
    }

    protected function fillForm(): void
    {
        $this->name = $this->location->name;
        $this->addressLine1 = $this->location->address_line_1;
        $this->addressLine2 = $this->location->address_line_2;
        $this->city = $this->location->city;
        $this->region = $this->location->region;
        $this->postalCode = $this->location->postal_code;
        $this->countryCode = $this->location->country_code;
        $this->latitude = $this->location->latitude !== null ? (string) $this->location->latitude : null;
        $this->longitude = $this->location->longitude !== null ? (string) $this->location->longitude : null;
        $this->timezone = $this->location->timezone;
        $this->status = $this->location->status;
    }

    /**
     * @return array<string, mixed>
     */
    protected function locationPayload(): array
    {
        return [
            'name' => trim($this->name),
            'address_line_1' => $this->normalizeString($this->addressLine1),
            'address_line_2' => $this->normalizeString($this->addressLine2),
            'city' => $this->normalizeString($this->city),
            'region' => $this->normalizeString($this->region),
            'postal_code' => $this->normalizeString($this->postalCode),
            'country_code' => $this->normalizeUpper($this->countryCode),
            'latitude' => $this->normalizeDecimal($this->latitude),
            'longitude' => $this->normalizeDecimal($this->longitude),
            'timezone' => trim($this->timezone),
            'status' => $this->normalizeString($this->status),
        ];
    }

    protected function normalizeString(?string $value): ?string
    {
        $value = $value !== null ? trim($value) : '';

        return $value !== '' ? $value : null;
    }

    protected function normalizeUpper(?string $value): ?string
    {
        $value = $this->normalizeString($value);

        return $value ? strtoupper($value) : null;
    }

    protected function normalizeDecimal(?string $value): ?float
    {
        $value = $value !== null ? trim($value) : '';

        if ($value === '') {
            return null;
        }

        return (float) $value;
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
