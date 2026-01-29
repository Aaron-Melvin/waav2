<?php

use App\Http\Requests\Partner\StoreUnitCalendarRequest;
use App\Http\Requests\Partner\UpdateUnitRequest;
use App\Models\Partner;
use App\Models\Product;
use App\Models\Unit;
use App\Models\UnitCalendar;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::app'), Title('Unit Details')] class extends Component
{
    public Partner $partner;

    public Product $product;

    public Unit $unit;

    public string $name = '';

    public ?string $code = null;

    public ?string $occupancyAdults = null;

    public ?string $occupancyChildren = null;

    public string $status = 'active';

    public string $housekeepingRequired = '0';

    public ?string $metaJson = null;

    public ?string $savedMessage = null;

    public ?string $calendarDate = null;

    public string $calendarAvailable = '1';

    public ?string $calendarMinStay = null;

    public ?string $calendarMaxStay = null;

    public ?string $calendarReason = null;

    public ?string $calendarMessage = null;

    public ?string $rangeStart = null;

    public ?string $rangeEnd = null;

    public string $rangeAvailable = '1';

    public ?string $rangeMinStay = null;

    public ?string $rangeMaxStay = null;

    public ?string $rangeReason = null;

    public ?string $rangeMessage = null;

    public function mount(Product $product, Unit $unit): void
    {
        $this->partner = $this->resolvePartner();

        if ($product->partner_id !== $this->partner->id) {
            abort(404);
        }

        if ($product->type !== 'accommodation') {
            abort(404);
        }

        if ($unit->product_id !== $product->id) {
            abort(404);
        }

        $this->product = $product->load('location');
        $this->unit = $unit;

        $this->fillForm();
    }

    /**
     * @return Collection<int, UnitCalendar>
     */
    public function getCalendarsProperty(): Collection
    {
        return UnitCalendar::query()
            ->where('unit_id', $this->unit->id)
            ->orderByDesc('date')
            ->limit(20)
            ->get();
    }

    public function updateUnit(): void
    {
        $this->savedMessage = null;

        $meta = $this->decodeMeta($this->metaJson);

        if ($this->metaJson !== null && $meta === null) {
            $this->addError('meta', 'Metadata must be valid JSON.');
            return;
        }

        $payload = $this->unitPayload();
        $payload['meta'] = $meta;

        $validated = Validator::make(
            $payload,
            UpdateUnitRequest::rulesFor($this->product, $this->unit),
            UpdateUnitRequest::messagesFor()
        )->validate();

        $this->unit->update($validated);
        $this->unit->refresh();

        $this->savedMessage = 'Unit settings updated.';
        $this->resetValidation();
    }

    public function saveCalendar(): void
    {
        $this->calendarMessage = null;

        $payload = $this->calendarPayload();

        $validated = Validator::make(
            $payload,
            StoreUnitCalendarRequest::rulesFor(),
            StoreUnitCalendarRequest::messagesFor()
        )->validate();

        $calendar = UnitCalendar::query()->updateOrCreate(
            [
                'unit_id' => $this->unit->id,
                'date' => $validated['date'],
            ],
            [
                'partner_id' => $this->partner->id,
                'is_available' => $validated['is_available'],
                'min_stay_nights' => $validated['min_stay_nights'],
                'max_stay_nights' => $validated['max_stay_nights'],
                'reason' => $validated['reason'],
            ]
        );

        $this->calendarMessage = $calendar->wasRecentlyCreated
            ? 'Calendar date added.'
            : 'Calendar date updated.';

        $this->resetCalendarForm();
        $this->resetValidation();
    }

    public function saveCalendarRange(): void
    {
        $this->rangeMessage = null;

        $payload = $this->calendarRangePayload();

        $rules = [
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after_or_equal:start'],
            'is_available' => ['required', 'boolean'],
            'min_stay_nights' => ['nullable', 'integer', 'min:1'],
            'max_stay_nights' => ['nullable', 'integer', 'min:1'],
            'reason' => ['nullable', 'string', 'max:150'],
        ];

        $validated = Validator::make($payload, $rules)->validate();

        if ($validated['min_stay_nights'] && $validated['max_stay_nights'] && $validated['max_stay_nights'] < $validated['min_stay_nights']) {
            $this->addError('rangeMaxStay', 'Max stay must be greater than or equal to min stay.');
            return;
        }

        $start = \Carbon\CarbonImmutable::parse($validated['start']);
        $end = \Carbon\CarbonImmutable::parse($validated['end']);

        for ($date = $start; $date->lessThanOrEqualTo($end); $date = $date->addDay()) {
            UnitCalendar::query()->updateOrCreate(
                [
                    'unit_id' => $this->unit->id,
                    'date' => $date->toDateString(),
                ],
                [
                    'partner_id' => $this->partner->id,
                    'is_available' => $validated['is_available'],
                    'min_stay_nights' => $validated['min_stay_nights'],
                    'max_stay_nights' => $validated['max_stay_nights'],
                    'reason' => $validated['reason'],
                ]
            );
        }

        $this->rangeMessage = 'Calendar range updated.';
        $this->resetRangeForm();
        $this->resetValidation();
    }

    public function deleteCalendar(string $calendarId): void
    {
        UnitCalendar::query()
            ->where('unit_id', $this->unit->id)
            ->where('id', $calendarId)
            ->delete();
    }

    protected function fillForm(): void
    {
        $this->name = $this->unit->name;
        $this->code = $this->unit->code;
        $this->occupancyAdults = (string) $this->unit->occupancy_adults;
        $this->occupancyChildren = (string) $this->unit->occupancy_children;
        $this->status = $this->unit->status;
        $this->housekeepingRequired = $this->unit->housekeeping_required ? '1' : '0';
        $this->metaJson = $this->unit->meta ? json_encode($this->unit->meta, JSON_PRETTY_PRINT) : null;
    }

    /**
     * @return array<string, mixed>
     */
    protected function unitPayload(): array
    {
        return [
            'name' => trim($this->name),
            'code' => $this->normalizeString($this->code),
            'occupancy_adults' => $this->normalizeInteger($this->occupancyAdults),
            'occupancy_children' => $this->normalizeInteger($this->occupancyChildren),
            'status' => $this->normalizeString($this->status) ?? 'active',
            'housekeeping_required' => $this->normalizeBoolean($this->housekeepingRequired),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function calendarPayload(): array
    {
        return [
            'date' => $this->normalizeString($this->calendarDate),
            'is_available' => $this->normalizeBoolean($this->calendarAvailable),
            'min_stay_nights' => $this->normalizeInteger($this->calendarMinStay),
            'max_stay_nights' => $this->normalizeInteger($this->calendarMaxStay),
            'reason' => $this->normalizeString($this->calendarReason),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function calendarRangePayload(): array
    {
        return [
            'start' => $this->normalizeString($this->rangeStart),
            'end' => $this->normalizeString($this->rangeEnd),
            'is_available' => $this->normalizeBoolean($this->rangeAvailable),
            'min_stay_nights' => $this->normalizeInteger($this->rangeMinStay),
            'max_stay_nights' => $this->normalizeInteger($this->rangeMaxStay),
            'reason' => $this->normalizeString($this->rangeReason),
        ];
    }

    protected function resetCalendarForm(): void
    {
        $this->calendarDate = null;
        $this->calendarAvailable = '1';
        $this->calendarMinStay = null;
        $this->calendarMaxStay = null;
        $this->calendarReason = null;
    }

    protected function resetRangeForm(): void
    {
        $this->rangeStart = null;
        $this->rangeEnd = null;
        $this->rangeAvailable = '1';
        $this->rangeMinStay = null;
        $this->rangeMaxStay = null;
        $this->rangeReason = null;
    }

    protected function normalizeInteger(?string $value): ?int
    {
        $value = $value !== null ? trim($value) : '';

        if ($value === '') {
            return null;
        }

        return (int) $value;
    }

    protected function normalizeBoolean(string $value): bool
    {
        return $value === '1';
    }

    protected function normalizeString(?string $value): ?string
    {
        $value = $value !== null ? trim($value) : '';

        return $value !== '' ? $value : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function decodeMeta(?string $value): ?array
    {
        $value = $this->normalizeString($value);

        if ($value === null) {
            return null;
        }

        $decoded = json_decode($value, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            return null;
        }

        return $decoded;
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
