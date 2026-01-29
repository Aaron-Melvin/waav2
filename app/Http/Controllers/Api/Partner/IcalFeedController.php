<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\StoreIcalFeedRequest;
use App\Http\Resources\IcalFeedResource;
use App\Models\IcalFeed;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class IcalFeedController extends Controller
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

        $feeds = IcalFeed::query()
            ->where('partner_id', $partner->id)
            ->orderBy('name')
            ->paginate($perPage);

        return IcalFeedResource::collection($feeds);
    }

    public function store(StoreIcalFeedRequest $request): JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $payload = $request->validated();

        $feed = IcalFeed::query()->create([
            'partner_id' => $partner->id,
            'product_id' => $payload['product_id'] ?? null,
            'unit_id' => $payload['unit_id'] ?? null,
            'name' => $payload['name'],
            'feed_token' => Str::random(32),
            'status' => $payload['status'] ?? 'active',
            'meta' => $payload['meta'] ?? null,
        ]);

        return (new IcalFeedResource($feed))
            ->response()
            ->setStatusCode(201);
    }
}
