<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\StoreFeeRequest;
use App\Http\Requests\Partner\UpdateFeeRequest;
use App\Http\Resources\FeeResource;
use App\Models\Fee;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FeeController extends Controller
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

        $fees = Fee::query()
            ->where('partner_id', $partner->id)
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->orderBy('name')
            ->paginate($perPage);

        return FeeResource::collection($fees);
    }

    public function store(StoreFeeRequest $request): JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $payload = $request->validated();

        $fee = Fee::query()->create([
            'partner_id' => $partner->id,
            'name' => $payload['name'],
            'type' => $payload['type'] ?? 'flat',
            'amount' => $payload['amount'],
            'applies_to' => $payload['applies_to'],
            'status' => $payload['status'] ?? 'active',
            'meta' => $payload['meta'] ?? null,
        ]);

        return (new FeeResource($fee))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, string $fee): FeeResource|JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $feeModel = Fee::query()
            ->where('partner_id', $partner->id)
            ->findOrFail($fee);

        return new FeeResource($feeModel);
    }

    public function update(UpdateFeeRequest $request, string $fee): FeeResource|JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $feeModel = Fee::query()
            ->where('partner_id', $partner->id)
            ->findOrFail($fee);

        $feeModel->update($request->validated());

        return new FeeResource($feeModel);
    }
}
