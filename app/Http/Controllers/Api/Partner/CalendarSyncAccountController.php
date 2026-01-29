<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\StoreCalendarSyncAccountRequest;
use App\Http\Resources\CalendarSyncAccountResource;
use App\Models\CalendarSyncAccount;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CalendarSyncAccountController extends Controller
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

        $accounts = CalendarSyncAccount::query()
            ->where('partner_id', $partner->id)
            ->orderBy('provider')
            ->paginate($perPage);

        return CalendarSyncAccountResource::collection($accounts);
    }

    public function store(StoreCalendarSyncAccountRequest $request): JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $payload = $request->validated();

        $account = CalendarSyncAccount::query()->create([
            'partner_id' => $partner->id,
            'provider' => $payload['provider'],
            'external_id' => $payload['external_id'] ?? null,
            'email' => $payload['email'] ?? null,
            'status' => $payload['status'] ?? 'active',
            'access_token' => $payload['access_token'] ?? null,
            'refresh_token' => $payload['refresh_token'] ?? null,
            'token_expires_at' => $payload['token_expires_at'] ?? null,
            'meta' => $payload['meta'] ?? null,
        ]);

        return (new CalendarSyncAccountResource($account))
            ->response()
            ->setStatusCode(201);
    }
}
