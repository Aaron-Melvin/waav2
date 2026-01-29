<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTaxRequest;
use App\Http\Requests\Admin\UpdateTaxRequest;
use App\Http\Resources\TaxResource;
use App\Models\Tax;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaxController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = max(1, min($request->integer('per_page', 50), 100));
        $status = trim($request->string('status', '')->toString());
        $partnerId = trim($request->string('partner_id', '')->toString());

        $taxes = Tax::query()
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($partnerId !== '', fn ($query) => $query->where('partner_id', $partnerId))
            ->orderBy('name')
            ->paginate($perPage);

        return TaxResource::collection($taxes);
    }

    public function show(string $tax): TaxResource
    {
        $taxModel = Tax::query()->findOrFail($tax);

        return new TaxResource($taxModel);
    }

    public function store(StoreTaxRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $tax = Tax::query()->create([
            'partner_id' => $payload['partner_id'],
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

    public function update(UpdateTaxRequest $request, string $tax): TaxResource
    {
        $taxModel = Tax::query()->findOrFail($tax);

        $taxModel->update($request->validated());

        return new TaxResource($taxModel);
    }
}
