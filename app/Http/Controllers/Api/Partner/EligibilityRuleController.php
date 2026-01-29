<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\StoreEligibilityRuleRequest;
use App\Http\Requests\Partner\UpdateEligibilityRuleRequest;
use App\Http\Resources\EligibilityRuleResource;
use App\Models\EligibilityRule;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EligibilityRuleController extends Controller
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
        $productId = trim($request->string('product_id', '')->toString());
        $kind = trim($request->string('kind', '')->toString());

        $rules = EligibilityRule::query()
            ->where('partner_id', $partner->id)
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($productId !== '', fn ($query) => $query->where('product_id', $productId))
            ->when($kind !== '', fn ($query) => $query->where('kind', $kind))
            ->orderBy('name')
            ->paginate($perPage);

        return EligibilityRuleResource::collection($rules);
    }

    public function store(StoreEligibilityRuleRequest $request): JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $payload = $request->validated();

        $rule = EligibilityRule::query()->create([
            'partner_id' => $partner->id,
            'product_id' => $payload['product_id'] ?? null,
            'name' => $payload['name'],
            'kind' => $payload['kind'],
            'config' => $payload['config'],
            'status' => $payload['status'] ?? 'active',
        ]);

        return (new EligibilityRuleResource($rule))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, string $eligibilityRule): EligibilityRuleResource|JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $rule = EligibilityRule::query()
            ->where('partner_id', $partner->id)
            ->findOrFail($eligibilityRule);

        return new EligibilityRuleResource($rule);
    }

    public function update(UpdateEligibilityRuleRequest $request, string $eligibilityRule): EligibilityRuleResource|JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $rule = EligibilityRule::query()
            ->where('partner_id', $partner->id)
            ->findOrFail($eligibilityRule);

        $rule->update($request->validated());

        return new EligibilityRuleResource($rule);
    }
}
