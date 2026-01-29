<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\StoreHoldRequest;
use App\Http\Resources\HoldResource;
use App\Models\Event;
use App\Models\Hold;
use App\Models\Partner;
use App\Models\Product;
use App\Models\Unit;
use App\Models\UnitHoldLock;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;

class HoldController extends Controller
{
    public function store(StoreHoldRequest $request): JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $payload = $request->validated();

        $product = Product::query()
            ->where('partner_id', $partner->id)
            ->findOrFail($payload['product_id']);

        $event = null;
        if (! empty($payload['event_id'])) {
            $event = Event::query()
                ->where('partner_id', $partner->id)
                ->findOrFail($payload['event_id']);

            if ($event->product_id !== $product->id) {
                return response()->json([
                    'message' => 'Event does not belong to the selected product.',
                ], 422);
            }
        }

        $unit = null;
        if (! empty($payload['unit_id'])) {
            $unit = Unit::query()
                ->where('partner_id', $partner->id)
                ->findOrFail($payload['unit_id']);

            if ($unit->product_id !== $product->id) {
                return response()->json([
                    'message' => 'Unit does not belong to the selected product.',
                ], 422);
            }
        }

        if (! $event && ! $unit) {
            return response()->json([
                'message' => 'An event or unit is required to create a hold.',
            ], 422);
        }

        $startsOn = $payload['starts_on'] ?? $payload['date'] ?? $event?->starts_at?->toDateString();
        $endsOn = $payload['ends_on'] ?? $payload['date'] ?? $event?->ends_at?->toDateString();

        if (! $startsOn) {
            return response()->json([
                'message' => 'A start date is required to create a hold.',
            ], 422);
        }

        if (! $endsOn) {
            $endsOn = $startsOn;
        }

        $expiresAt = now()->addMinutes($payload['expires_in_minutes'] ?? 15);

        $hold = Hold::query()->create([
            'partner_id' => $partner->id,
            'product_id' => $product->id,
            'event_id' => $event?->id,
            'unit_id' => $unit?->id,
            'starts_on' => $startsOn,
            'ends_on' => $endsOn,
            'quantity' => $payload['quantity'] ?? 1,
            'status' => 'active',
            'expires_at' => $expiresAt,
            'meta' => [
                'source' => 'front',
            ],
        ]);

        if ($unit) {
            $startDate = CarbonImmutable::parse($startsOn);
            $endDate = CarbonImmutable::parse($endsOn);

            for ($date = $startDate; $date->lessThanOrEqualTo($endDate); $date = $date->addDay()) {
                UnitHoldLock::query()->firstOrCreate([
                    'hold_id' => $hold->id,
                    'unit_id' => $unit->id,
                    'date' => $date->toDateString(),
                ]);
            }
        }

        return (new HoldResource($hold))
            ->response()
            ->setStatusCode(201);
    }
}
