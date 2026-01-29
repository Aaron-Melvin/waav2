<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\StoreTaxRequest;
use App\Http\Requests\Partner\UpdateTaxRequest;
use App\Http\Resources\TaxResource;
use App\Models\Partner;
use App\Models\Tax;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaxController extends Controller
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

        $taxes = Tax::query()
            ->where('partner_id', $partner->id)
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->orderBy('name')
            ->paginate($perPage);

        return TaxResource::collection($taxes);
    }

    public function store(StoreTaxRequest $request): JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $payload = $request->validated();

        $tax = Tax::query()->create([
            'partner_id' => $partner->id,
            'name' => $payload['name'],
            'rate' => $payload['rate'],
            'applies_to' => $payload['applies_to'],
            'is_inclusive' => $payload['is_inclusive'] ?? false,
            'status' => $payload['status'] ?? 'active',
            'meta' => $payload['meta'] ?? null,
        ]);

        return (new TaxResource($tax))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, string $tax): TaxResource|JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $taxModel = Tax::query()
            ->where('partner_id', $partner->id)
            ->findOrFail($tax);

        return new TaxResource($taxModel);
    }

    public function update(UpdateTaxRequest $request, string $tax): TaxResource|JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $taxModel = Tax::query()
            ->where('partner_id', $partner->id)
            ->findOrFail($tax);

        $taxModel->update($request->validated());

        return new TaxResource($taxModel);
    }
}
