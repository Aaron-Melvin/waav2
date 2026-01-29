<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\StoreLocationRequest;
use App\Http\Requests\Partner\UpdateLocationRequest;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LocationController extends Controller
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
        $search = trim($request->string('search', '')->toString());

        $locations = Location::query()
            ->where('partner_id', $partner->id)
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate($perPage);

        return LocationResource::collection($locations);
    }

    public function store(StoreLocationRequest $request): JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $payload = $request->validated();

        $location = Location::query()->create([
            'partner_id' => $partner->id,
            'name' => $payload['name'],
            'address_line_1' => $payload['address_line_1'] ?? null,
            'address_line_2' => $payload['address_line_2'] ?? null,
            'city' => $payload['city'] ?? null,
            'region' => $payload['region'] ?? null,
            'postal_code' => $payload['postal_code'] ?? null,
            'country_code' => $payload['country_code'] ?? null,
            'latitude' => $payload['latitude'] ?? null,
            'longitude' => $payload['longitude'] ?? null,
            'timezone' => $payload['timezone'],
            'status' => $payload['status'] ?? 'active',
        ]);

        return (new LocationResource($location))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, string $location): LocationResource|JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $locationModel = Location::query()
            ->where('partner_id', $partner->id)
            ->findOrFail($location);

        return new LocationResource($locationModel);
    }

    public function update(UpdateLocationRequest $request, string $location): LocationResource|JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $locationModel = Location::query()
            ->where('partner_id', $partner->id)
            ->findOrFail($location);

        $locationModel->update($request->validated());

        return new LocationResource($locationModel);
    }
}
