<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\StoreCancellationPolicyRequest;
use App\Http\Requests\Partner\UpdateCancellationPolicyRequest;
use App\Http\Resources\CancellationPolicyResource;
use App\Models\CancellationPolicy;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CancellationPolicyController extends Controller
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

        $policies = CancellationPolicy::query()
            ->where('partner_id', $partner->id)
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->orderBy('name')
            ->paginate($perPage);

        return CancellationPolicyResource::collection($policies);
    }

    public function store(StoreCancellationPolicyRequest $request): JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $payload = $request->validated();

        $policy = CancellationPolicy::query()->create([
            'partner_id' => $partner->id,
            'name' => $payload['name'],
            'description' => $payload['description'] ?? null,
            'rules' => $payload['rules'],
            'status' => $payload['status'] ?? 'active',
        ]);

        return (new CancellationPolicyResource($policy))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, string $cancellationPolicy): CancellationPolicyResource|JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $policy = CancellationPolicy::query()
            ->where('partner_id', $partner->id)
            ->findOrFail($cancellationPolicy);

        return new CancellationPolicyResource($policy);
    }

    public function update(UpdateCancellationPolicyRequest $request, string $cancellationPolicy): CancellationPolicyResource|JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $policy = CancellationPolicy::query()
            ->where('partner_id', $partner->id)
            ->findOrFail($cancellationPolicy);

        $policy->update($request->validated());

        return new CancellationPolicyResource($policy);
    }
}
