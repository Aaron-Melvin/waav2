<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCancellationPolicyRequest;
use App\Http\Requests\Admin\UpdateCancellationPolicyRequest;
use App\Http\Resources\CancellationPolicyResource;
use App\Models\CancellationPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CancellationPolicyController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = max(1, min($request->integer('per_page', 50), 100));
        $status = trim($request->string('status', '')->toString());
        $partnerId = trim($request->string('partner_id', '')->toString());

        $policies = CancellationPolicy::query()
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($partnerId !== '', fn ($query) => $query->where('partner_id', $partnerId))
            ->orderBy('name')
            ->paginate($perPage);

        return CancellationPolicyResource::collection($policies);
    }

    public function show(string $cancellationPolicy): CancellationPolicyResource
    {
        $policy = CancellationPolicy::query()->findOrFail($cancellationPolicy);

        return new CancellationPolicyResource($policy);
    }

    public function store(StoreCancellationPolicyRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $policy = CancellationPolicy::query()->create([
            'partner_id' => $payload['partner_id'],
            'name' => $payload['name'],
            'description' => $payload['description'] ?? null,
            'rules' => $payload['rules'],
            'status' => $payload['status'] ?? 'active',
        ]);

        return (new CancellationPolicyResource($policy))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateCancellationPolicyRequest $request, string $cancellationPolicy): CancellationPolicyResource
    {
        $policy = CancellationPolicy::query()->findOrFail($cancellationPolicy);

        $policy->update($request->validated());

        return new CancellationPolicyResource($policy);
    }
}
