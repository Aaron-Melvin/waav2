<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\StoreEventOverrideRequest;
use App\Http\Resources\EventOverrideResource;
use App\Models\Event;
use App\Models\EventOverride;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EventOverrideController extends Controller
{
    public function index(Request $request, string $event): AnonymousResourceCollection|JsonResponse
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

        $overrides = EventOverride::query()
            ->where('event_id', $eventModel->id)
            ->orderBy('field')
            ->get();

        return EventOverrideResource::collection($overrides);
    }

    public function store(StoreEventOverrideRequest $request, string $event): JsonResponse
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

        $payload = $request->validated();

        $value = match ($payload['field']) {
            'price_override' => [
                'value' => (float) $payload['value'],
                'currency' => $payload['currency'],
            ],
            'capacity_total' => [
                'value' => (int) $payload['value'],
            ],
            default => [
                'value' => (string) $payload['value'],
            ],
        };

        $override = EventOverride::query()->create([
            'event_id' => $eventModel->id,
            'field' => $payload['field'],
            'value' => $value,
        ]);

        return (new EventOverrideResource($override))
            ->response()
            ->setStatusCode(201);
    }
}
