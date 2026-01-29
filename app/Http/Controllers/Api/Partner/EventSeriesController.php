<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\GenerateEventSeriesRequest;
use App\Http\Requests\Partner\StoreEventSeriesRequest;
use App\Http\Requests\Partner\UpdateEventSeriesRequest;
use App\Http\Resources\EventResource;
use App\Http\Resources\EventSeriesResource;
use App\Models\Event;
use App\Models\EventSeries;
use App\Models\Partner;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EventSeriesController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection|JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $perPage = max(1, min($request->integer('per_page', 50), 100));
        $status = trim($request->string('status', '')->toString());
        $productId = trim($request->string('product_id', '')->toString());

        $series = EventSeries::query()
            ->where('partner_id', $partner->id)
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($productId !== '', fn ($query) => $query->where('product_id', $productId))
            ->orderBy('name')
            ->paginate($perPage);

        return EventSeriesResource::collection($series);
    }

    public function store(StoreEventSeriesRequest $request): JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $payload = $request->validated();

        $series = EventSeries::query()->create([
            'partner_id' => $partner->id,
            'product_id' => $payload['product_id'],
            'name' => $payload['name'],
            'starts_at' => $payload['starts_at'],
            'ends_at' => $payload['ends_at'],
            'capacity_total' => $payload['capacity_total'] ?? null,
            'timezone' => $payload['timezone'],
            'recurrence_rule' => $payload['recurrence_rule'] ?? null,
            'status' => $payload['status'] ?? 'active',
        ]);

        return (new EventSeriesResource($series))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, string $eventSeries): EventSeriesResource|JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $series = EventSeries::query()
            ->where('partner_id', $partner->id)
            ->findOrFail($eventSeries);

        return new EventSeriesResource($series);
    }

    public function update(UpdateEventSeriesRequest $request, string $eventSeries): EventSeriesResource|JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $series = EventSeries::query()
            ->where('partner_id', $partner->id)
            ->findOrFail($eventSeries);

        $series->update($request->validated());

        return new EventSeriesResource($series);
    }

    public function generate(GenerateEventSeriesRequest $request, string $eventSeries): JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $series = EventSeries::query()
            ->where('partner_id', $partner->id)
            ->findOrFail($eventSeries);

        $payload = $request->validated();
        $from = CarbonImmutable::parse($payload['date_range']['from'], $series->timezone)->startOfDay();
        $to = CarbonImmutable::parse($payload['date_range']['to'], $series->timezone)->endOfDay();
        $preview = (bool) ($payload['preview'] ?? false);

        $dates = $this->buildSeriesDates($series, $from, $to);

        if ($dates === []) {
            return response()->json([
                'message' => 'Event series recurrence rules are missing or invalid.',
            ], 422);
        }

        $existingStarts = Event::query()
            ->where('event_series_id', $series->id)
            ->whereBetween('starts_at', [$from, $to])
            ->get()
            ->map(fn (Event $event): string => $event->starts_at?->format('Y-m-d H:i:s') ?? '')
            ->filter()
            ->all();

        $existingLookup = array_flip($existingStarts);
        $events = [];

        foreach ($dates as $date) {
            $startsAt = $date->setTimeFromTimeString($series->starts_at);
            $endsAt = $date->setTimeFromTimeString($series->ends_at);

            if ($endsAt->lessThanOrEqualTo($startsAt)) {
                $endsAt = $endsAt->addDay();
            }

            $startsKey = $startsAt->format('Y-m-d H:i:s');

            if (isset($existingLookup[$startsKey])) {
                continue;
            }

            if ($preview) {
                $events[] = new Event([
                    'partner_id' => $partner->id,
                    'product_id' => $series->product_id,
                    'event_series_id' => $series->id,
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                    'capacity_total' => $series->capacity_total,
                    'capacity_reserved' => 0,
                    'status' => 'scheduled',
                    'publish_state' => 'draft',
                    'weather_alert' => false,
                ]);

                continue;
            }

            $events[] = Event::query()->firstOrCreate([
                'event_series_id' => $series->id,
                'starts_at' => $startsAt,
            ], [
                'partner_id' => $partner->id,
                'product_id' => $series->product_id,
                'ends_at' => $endsAt,
                'capacity_total' => $series->capacity_total,
                'capacity_reserved' => 0,
                'status' => 'scheduled',
                'publish_state' => 'draft',
                'weather_alert' => false,
            ]);
        }

        return response()->json([
            'preview' => $preview,
            'created' => $preview ? 0 : count($events),
            'planned' => $preview ? count($events) : 0,
            'data' => EventResource::collection($events),
        ]);
    }

    /**
     * @return array<int, CarbonImmutable>
     */
    protected function buildSeriesDates(EventSeries $series, CarbonImmutable $from, CarbonImmutable $to): array
    {
        $rule = $series->recurrence_rule ?? [];

        if (! is_array($rule)) {
            return [];
        }

        if (! empty($rule['dates']) && is_array($rule['dates'])) {
            return collect($rule['dates'])
                ->filter()
                ->map(fn (string $date): CarbonImmutable => CarbonImmutable::parse($date, $series->timezone)->startOfDay())
                ->filter(fn (CarbonImmutable $date): bool => $date->betweenIncluded($from, $to))
                ->values()
                ->all();
        }

        $frequency = $rule['frequency'] ?? 'weekly';
        $interval = max(1, (int) ($rule['interval'] ?? 1));
        $dates = [];

        if ($frequency === 'daily') {
            for ($date = $from; $date->lessThanOrEqualTo($to); $date = $date->addDays($interval)) {
                $dates[] = $date->startOfDay();
            }

            return $dates;
        }

        $byweekday = $rule['byweekday'] ?? [];
        $weekdayMap = [
            'MO' => 1,
            'TU' => 2,
            'WE' => 3,
            'TH' => 4,
            'FR' => 5,
            'SA' => 6,
            'SU' => 7,
        ];

        $allowedWeekdays = collect($byweekday)
            ->map(fn (string $day): ?int => $weekdayMap[$day] ?? null)
            ->filter()
            ->values()
            ->all();

        if ($allowedWeekdays === []) {
            return [];
        }

        $weekStart = $from->startOfWeek();

        for ($cursor = $weekStart; $cursor->lessThanOrEqualTo($to); $cursor = $cursor->addWeeks($interval)) {
            foreach ($allowedWeekdays as $weekday) {
                $date = $cursor->addDays($weekday - 1)->startOfDay();

                if ($date->betweenIncluded($from, $to)) {
                    $dates[] = $date;
                }
            }
        }

        return collect($dates)
            ->sort()
            ->unique(fn (CarbonImmutable $date): string => $date->toDateString())
            ->values()
            ->all();
    }
}
