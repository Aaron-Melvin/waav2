<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\StoreEventRequest;
use App\Http\Requests\Partner\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Carbon\CarbonImmutable;

class EventController extends Controller
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
        $publishState = trim($request->string('publish_state', '')->toString());
        $productId = trim($request->string('product_id', '')->toString());
        $from = trim($request->string('from', '')->toString());
        $to = trim($request->string('to', '')->toString());

        $events = Event::query()
            ->where('partner_id', $partner->id)
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($publishState !== '', fn ($query) => $query->where('publish_state', $publishState))
            ->when($productId !== '', fn ($query) => $query->where('product_id', $productId))
            ->when($from !== '', fn ($query) => $query->where('starts_at', '>=', CarbonImmutable::parse($from)))
            ->when($to !== '', fn ($query) => $query->where('ends_at', '<=', CarbonImmutable::parse($to)))
            ->orderBy('starts_at')
            ->paginate($perPage);

        return EventResource::collection($events);
    }

    public function store(StoreEventRequest $request): JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $payload = $request->validated();

        $event = Event::query()->create([
            'partner_id' => $partner->id,
            'product_id' => $payload['product_id'],
            'event_series_id' => $payload['event_series_id'] ?? null,
            'starts_at' => $payload['starts_at'],
            'ends_at' => $payload['ends_at'],
            'capacity_total' => $payload['capacity_total'] ?? null,
            'capacity_reserved' => $payload['capacity_reserved'] ?? 0,
            'traffic_light' => $payload['traffic_light'] ?? null,
            'status' => $payload['status'] ?? 'scheduled',
            'publish_state' => $payload['publish_state'] ?? 'draft',
            'weather_alert' => $payload['weather_alert'] ?? false,
        ]);

        return (new EventResource($event))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, string $event): EventResource|JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $eventModel = Event::query()
            ->where('partner_id', $partner->id)
            ->with('overrides')
            ->findOrFail($event);

        return new EventResource($eventModel);
    }

    public function update(UpdateEventRequest $request, string $event): EventResource|JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $eventModel = Event::query()
            ->where('partner_id', $partner->id)
            ->findOrFail($event);

        $eventModel->update($request->validated());

        return new EventResource($eventModel);
    }
}
