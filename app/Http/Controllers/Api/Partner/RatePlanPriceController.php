<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\StoreRatePlanPriceRequest;
use App\Http\Requests\Partner\UpdateRatePlanPriceRequest;
use App\Http\Resources\RatePlanPriceResource;
use App\Models\Partner;
use App\Models\Product;
use App\Models\RatePlan;
use App\Models\RatePlanPrice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RatePlanPriceController extends Controller
{
    public function index(Request $request, string $product, string $ratePlan): AnonymousResourceCollection|JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $productModel = Product::query()
            ->where('partner_id', $partner->id)
            ->findOrFail($product);

        $ratePlanModel = RatePlan::query()
            ->where('product_id', $productModel->id)
            ->findOrFail($ratePlan);

        $perPage = max(1, min($request->integer('per_page', 50), 100));

        $prices = RatePlanPrice::query()
            ->where('rate_plan_id', $ratePlanModel->id)
            ->orderBy('starts_on')
            ->paginate($perPage);

        return RatePlanPriceResource::collection($prices);
    }

    public function store(StoreRatePlanPriceRequest $request, string $product, string $ratePlan): JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $productModel = Product::query()
            ->where('partner_id', $partner->id)
            ->findOrFail($product);

        $ratePlanModel = RatePlan::query()
            ->where('product_id', $productModel->id)
            ->findOrFail($ratePlan);

        $payload = $request->validated();

        $restrictions = null;
        if (! empty($payload['restrictions'])) {
            $restrictions = json_decode($payload['restrictions'], true, 512, JSON_THROW_ON_ERROR);
        }

        $price = RatePlanPrice::query()->create([
            'rate_plan_id' => $ratePlanModel->id,
            'starts_on' => $payload['starts_on'],
            'ends_on' => $payload['ends_on'],
            'price' => $payload['price'],
            'extra_adult' => $payload['extra_adult'] ?? null,
            'extra_child' => $payload['extra_child'] ?? null,
            'restrictions' => $restrictions,
        ]);

        return (new RatePlanPriceResource($price))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateRatePlanPriceRequest $request, string $product, string $ratePlan, string $ratePlanPrice): RatePlanPriceResource|JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $productModel = Product::query()
            ->where('partner_id', $partner->id)
            ->findOrFail($product);

        $ratePlanModel = RatePlan::query()
            ->where('product_id', $productModel->id)
            ->findOrFail($ratePlan);

        $priceModel = RatePlanPrice::query()
            ->where('rate_plan_id', $ratePlanModel->id)
            ->findOrFail($ratePlanPrice);

        $payload = $request->validated();
        $restrictions = null;
        if (array_key_exists('restrictions', $payload)) {
            $restrictions = $payload['restrictions'] !== null
                ? json_decode($payload['restrictions'], true, 512, JSON_THROW_ON_ERROR)
                : null;
        }

        $priceModel->update([
            'starts_on' => $payload['starts_on'],
            'ends_on' => $payload['ends_on'],
            'price' => $payload['price'],
            'extra_adult' => $payload['extra_adult'] ?? null,
            'extra_child' => $payload['extra_child'] ?? null,
            'restrictions' => $restrictions,
        ]);

        return new RatePlanPriceResource($priceModel);
    }
}
