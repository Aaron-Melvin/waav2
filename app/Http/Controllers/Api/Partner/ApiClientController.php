<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApiClientRequest;
use App\Http\Resources\ApiClientResource;
use App\Models\ApiClient;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ApiClientController extends Controller
{
    public function store(StoreApiClientRequest $request): JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $payload = $request->validated();
        $plainSecret = $payload['client_secret'] ?? Str::random(40);

        $apiClient = ApiClient::query()->create([
            'partner_id' => $partner->id,
            'client_id' => $payload['client_id'],
            'client_secret_hash' => Hash::make($plainSecret),
            'scopes' => $payload['scopes'] ?? [],
            'status' => $payload['status'] ?? 'active',
        ]);

        return (new ApiClientResource($apiClient))
            ->additional([
                'client_secret' => $plainSecret,
            ])
            ->response()
            ->setStatusCode(201);
    }
}
