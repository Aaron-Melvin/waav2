<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFeeRequest;
use App\Http\Requests\Admin\UpdateFeeRequest;
use App\Http\Resources\FeeResource;
use App\Models\Fee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FeeController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = max(1, min($request->integer('per_page', 50), 100));
        $status = trim($request->string('status', '')->toString());
        $partnerId = trim($request->string('partner_id', '')->toString());

        $fees = Fee::query()
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($partnerId !== '', fn ($query) => $query->where('partner_id', $partnerId))
            ->orderBy('name')
            ->paginate($perPage);

        return FeeResource::collection($fees);
    }

    public function show(string $fee): FeeResource
    {
        $feeModel = Fee::query()->findOrFail($fee);

        return new FeeResource($feeModel);
    }

    public function store(StoreFeeRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $fee = Fee::query()->create([
            'partner_id' => $payload['partner_id'],
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

    public function update(UpdateFeeRequest $request, string $fee): FeeResource
    {
        $feeModel = Fee::query()->findOrFail($fee);

        $feeModel->update($request->validated());

        return new FeeResource($feeModel);
    }
}
