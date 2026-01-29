<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\StoreProductRequest;
use App\Http\Requests\Partner\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Partner;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class ProductController extends Controller
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
        $type = trim($request->string('type', '')->toString());
        $visibility = trim($request->string('visibility', '')->toString());
        $search = trim($request->string('search', '')->toString());

        $products = Product::query()
            ->where('partner_id', $partner->id)
            ->with('location')
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($type !== '', fn ($query) => $query->where('type', $type))
            ->when($visibility !== '', fn ($query) => $query->where('visibility', $visibility))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($innerQuery) use ($search): void {
                    $innerQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate($perPage);

        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $payload = $request->validated();
        $baseSlug = $payload['slug'] ?? Str::slug($payload['name']);
        $slug = $this->uniqueSlug($partner->id, $payload['type'], $baseSlug !== '' ? $baseSlug : Str::slug($payload['name']));

        $product = Product::query()->create([
            'partner_id' => $partner->id,
            'location_id' => $payload['location_id'] ?? null,
            'name' => $payload['name'],
            'type' => $payload['type'],
            'slug' => $slug,
            'description' => $payload['description'] ?? null,
            'capacity_total' => $payload['capacity_total'] ?? null,
            'default_currency' => $payload['default_currency'] ?? $partner->currency ?? 'EUR',
            'status' => $payload['status'] ?? 'active',
            'visibility' => $payload['visibility'] ?? 'public',
            'lead_time_minutes' => $payload['lead_time_minutes'] ?? null,
            'cutoff_minutes' => $payload['cutoff_minutes'] ?? null,
            'meta' => $payload['meta'] ?? null,
        ]);

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, string $product): ProductResource|JsonResponse
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
            ->with('location')
            ->findOrFail($product);

        return new ProductResource($productModel);
    }

    public function update(UpdateProductRequest $request, string $product): ProductResource|JsonResponse
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

        $productModel->update($request->validated());

        return new ProductResource($productModel);
    }

    protected function uniqueSlug(string $partnerId, string $type, string $baseSlug): string
    {
        $baseSlug = $baseSlug !== '' ? $baseSlug : Str::random(8);
        $slug = $baseSlug;
        $suffix = 1;

        while (Product::query()
            ->where('partner_id', $partnerId)
            ->where('type', $type)
            ->where('slug', $slug)
            ->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
