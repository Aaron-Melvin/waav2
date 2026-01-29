<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\StoreUnitRequest;
use App\Http\Requests\Partner\UpdateUnitRequest;
use App\Http\Resources\UnitResource;
use App\Models\Partner;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UnitController extends Controller
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

        $units = Unit::query()
            ->where('product_id', $productModel->id)
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->orderBy('name')
            ->paginate($perPage);

        return UnitResource::collection($units);
    }

    public function store(StoreUnitRequest $request, string $product): JsonResponse
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

        $unit = Unit::query()->create([
            'partner_id' => $partner->id,
            'product_id' => $productModel->id,
            'name' => $payload['name'],
            'code' => $payload['code'] ?? null,
            'occupancy_adults' => $payload['occupancy_adults'],
            'occupancy_children' => $payload['occupancy_children'],
            'status' => $payload['status'],
            'housekeeping_required' => $payload['housekeeping_required'],
        ]);

        return (new UnitResource($unit))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, string $product, string $unit): UnitResource|JsonResponse
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

        $unitModel = Unit::query()
            ->where('product_id', $productModel->id)
            ->findOrFail($unit);

        return new UnitResource($unitModel);
    }

    public function update(UpdateUnitRequest $request, string $product, string $unit): UnitResource|JsonResponse
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

        $unitModel = Unit::query()
            ->where('product_id', $productModel->id)
            ->findOrFail($unit);

        $unitModel->update($request->validated());

        return new UnitResource($unitModel);
    }
}
