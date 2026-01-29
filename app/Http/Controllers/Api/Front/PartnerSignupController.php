<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\PartnerSignupRequest;
use App\Http\Resources\PartnerResource;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class PartnerSignupController extends Controller
{
    public function store(PartnerSignupRequest $request): JsonResponse
    {
        $payload = $request->validated();
        $slug = $payload['slug'] ?? $this->generateUniqueSlug($payload['name']);

        $partner = Partner::query()->create([
            'name' => $payload['name'],
            'slug' => $slug,
            'billing_email' => $payload['billing_email'],
            'currency' => $payload['currency'] ?? 'EUR',
            'timezone' => $payload['timezone'],
            'status' => 'pending',
        ]);

        return (new PartnerResource($partner))
            ->response()
            ->setStatusCode(201);
    }

    protected function generateUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $suffix = 1;

        while (Partner::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
