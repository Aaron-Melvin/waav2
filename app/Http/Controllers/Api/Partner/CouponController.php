<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\StoreCouponRequest;
use App\Http\Requests\Partner\UpdateCouponRequest;
use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CouponController extends Controller
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
        $code = trim($request->string('code', '')->toString());

        $coupons = Coupon::query()
            ->where('partner_id', $partner->id)
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($code !== '', fn ($query) => $query->where('code', 'like', "%{$code}%"))
            ->orderBy('code')
            ->paginate($perPage);

        return CouponResource::collection($coupons);
    }

    public function store(StoreCouponRequest $request): JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $payload = $request->validated();

        $coupon = Coupon::query()->create([
            'partner_id' => $partner->id,
            'code' => $payload['code'],
            'name' => $payload['name'] ?? null,
            'description' => $payload['description'] ?? null,
            'discount_type' => $payload['discount_type'] ?? 'percent',
            'discount_value' => $payload['discount_value'],
            'max_redemptions' => $payload['max_redemptions'] ?? null,
            'max_per_customer' => $payload['max_per_customer'] ?? null,
            'starts_on' => $payload['starts_on'] ?? null,
            'ends_on' => $payload['ends_on'] ?? null,
            'min_total' => $payload['min_total'] ?? null,
            'status' => $payload['status'] ?? 'active',
            'meta' => $payload['meta'] ?? null,
        ]);

        return (new CouponResource($coupon))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, string $coupon): CouponResource|JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $couponModel = Coupon::query()
            ->where('partner_id', $partner->id)
            ->findOrFail($coupon);

        return new CouponResource($couponModel);
    }

    public function update(UpdateCouponRequest $request, string $coupon): CouponResource|JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $couponModel = Coupon::query()
            ->where('partner_id', $partner->id)
            ->findOrFail($coupon);

        $couponModel->update($request->validated());

        return new CouponResource($couponModel);
    }
}
