<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApiClientRequest;
use App\Http\Resources\ApiClientResource;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PartnerApiClientController extends Controller
{
    public function store(StoreApiClientRequest $request, Partner $partner): JsonResponse
    {
        $payload = $request->validated();
        $plainSecret = $payload['client_secret'] ?? Str::random(40);

        $apiClient = $partner->apiClients()->create([
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
