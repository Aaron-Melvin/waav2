<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\AvailabilitySearchRequest;
use App\Http\Resources\AvailabilityResource;
use App\Models\Partner;
use App\Models\SearchIndex;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AvailabilityController extends Controller
{
    public function search(AvailabilitySearchRequest $request): AnonymousResourceCollection|JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $payload = $request->validated();
        $from = $payload['date_range']['from'];
        $to = $payload['date_range']['to'];
        $quantity = $payload['quantity'] ?? null;

        $results = SearchIndex::query()
            ->where('partner_id', $partner->id)
            ->where('product_id', $payload['product_id'])
            ->whereDate('starts_on', '<=', $to)
            ->whereDate('ends_on', '>=', $from)
            ->when($quantity, fn ($query) => $query->where('capacity_available', '>=', $quantity))
            ->orderBy('starts_on')
            ->get();

        return AvailabilityResource::collection($results);
    }
}
