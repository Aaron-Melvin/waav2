<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePartnerRequest;
use App\Http\Requests\UpdatePartnerStatusRequest;
use App\Http\Resources\PartnerResource;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PartnerController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = max(1, min($request->integer('per_page', 50), 100));
        $status = strtolower($request->string('status', '')->toString());
        $search = trim($request->string('search', '')->toString());

        $partners = Partner::query()
            ->when(in_array($status, ['active', 'inactive', 'pending'], true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('billing_email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate($perPage);

        return PartnerResource::collection($partners);
    }

    public function pending(Request $request): AnonymousResourceCollection
    {
        $perPage = max(1, min($request->integer('per_page', 50), 100));

        $partners = Partner::query()
            ->where('status', 'pending')
            ->orderBy('name')
            ->paginate($perPage);

        return PartnerResource::collection($partners);
    }

    public function store(StorePartnerRequest $request): JsonResponse
    {
        $partner = Partner::query()->create($request->validated());

        return (new PartnerResource($partner))
            ->response()
            ->setStatusCode(201);
    }

    public function updateStatus(UpdatePartnerStatusRequest $request, Partner $partner): PartnerResource
    {
        $partner->update([
            'status' => $request->validated()['status'],
        ]);

        return new PartnerResource($partner);
    }
}
