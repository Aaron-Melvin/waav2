<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\StoreRatePlanRequest;
use App\Http\Requests\Partner\UpdateRatePlanRequest;
use App\Http\Resources\RatePlanResource;
use App\Models\Partner;
use App\Models\Product;
use App\Models\RatePlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RatePlanController extends Controller
{
    public function index(Request $request, string $product): AnonymousResourceCollection|JsonResponse
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

        $perPage = max(1, min($request->integer('per_page', 50), 100));
        $status = trim($request->string('status', '')->toString());

        $ratePlans = RatePlan::query()
            ->where('product_id', $productModel->id)
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->orderBy('name')
            ->paginate($perPage);

        return RatePlanResource::collection($ratePlans);
    }

    public function store(StoreRatePlanRequest $request, string $product): JsonResponse
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

        $payload = $request->validated();

        $ratePlan = RatePlan::query()->create([
            'partner_id' => $partner->id,
            'product_id' => $productModel->id,
            'name' => $payload['name'],
            'code' => $payload['code'] ?? null,
            'pricing_model' => $payload['pricing_model'],
            'currency' => $payload['currency'],
            'status' => $payload['status'],
            'cancellation_policy_id' => $payload['cancellation_policy_id'] ?? null,
            'meta' => $payload['meta'] ?? null,
        ]);

        return (new RatePlanResource($ratePlan))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, string $product, string $ratePlan): RatePlanResource|JsonResponse
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

        return new RatePlanResource($ratePlanModel);
    }

    public function update(UpdateRatePlanRequest $request, string $product, string $ratePlan): RatePlanResource|JsonResponse
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

        $ratePlanModel->update($request->validated());

        return new RatePlanResource($ratePlanModel);
    }
}
