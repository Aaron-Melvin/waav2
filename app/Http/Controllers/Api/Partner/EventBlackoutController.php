<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\StoreEventBlackoutRequest;
use App\Http\Requests\Partner\UpdateEventBlackoutRequest;
use App\Http\Resources\EventBlackoutResource;
use App\Models\EventBlackout;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EventBlackoutController extends Controller
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
        $locationId = trim($request->string('location_id', '')->toString());

        $blackouts = EventBlackout::query()
            ->where('partner_id', $partner->id)
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($productId !== '', fn ($query) => $query->where('product_id', $productId))
            ->when($locationId !== '', fn ($query) => $query->where('location_id', $locationId))
            ->orderBy('starts_at')
            ->paginate($perPage);

        return EventBlackoutResource::collection($blackouts);
    }

    public function store(StoreEventBlackoutRequest $request): JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $payload = $request->validated();

        $blackout = EventBlackout::query()->create([
            'partner_id' => $partner->id,
            'product_id' => $payload['product_id'] ?? null,
            'location_id' => $payload['location_id'] ?? null,
            'starts_at' => $payload['starts_at'],
            'ends_at' => $payload['ends_at'],
            'reason' => $payload['reason'] ?? null,
            'status' => $payload['status'] ?? 'active',
        ]);

        return (new EventBlackoutResource($blackout))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, string $blackout): EventBlackoutResource|JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $blackoutModel = EventBlackout::query()
            ->where('partner_id', $partner->id)
            ->findOrFail($blackout);

        return new EventBlackoutResource($blackoutModel);
    }

    public function update(UpdateEventBlackoutRequest $request, string $blackout): EventBlackoutResource|JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $blackoutModel = EventBlackout::query()
            ->where('partner_id', $partner->id)
            ->findOrFail($blackout);

        $blackoutModel->update($request->validated());

        return new EventBlackoutResource($blackoutModel);
    }
}
