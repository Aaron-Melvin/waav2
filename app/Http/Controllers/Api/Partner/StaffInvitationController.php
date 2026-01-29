<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\StoreStaffInvitationRequest;
use App\Http\Resources\StaffInvitationResource;
use App\Models\Partner;
use App\Models\StaffInvitation;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class StaffInvitationController extends Controller
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

        $invitations = StaffInvitation::query()
            ->where('partner_id', $partner->id)
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return StaffInvitationResource::collection($invitations);
    }

    public function store(StoreStaffInvitationRequest $request): JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $payload = $request->validated();
        $expiresAt = $payload['expires_at'] ?? CarbonImmutable::now()->addDays(7);
        $meta = null;

        if (! empty($payload['message'])) {
            $meta = ['message' => $payload['message']];
        }

        $invitation = StaffInvitation::query()->create([
            'partner_id' => $partner->id,
            'inviter_id' => null,
            'email' => $payload['email'],
            'role' => $payload['role'] ?? 'partner-staff',
            'token' => Str::random(40),
            'status' => 'pending',
            'expires_at' => $expiresAt,
            'meta' => $meta,
        ]);

        return (new StaffInvitationResource($invitation))
            ->response()
            ->setStatusCode(201);
    }
}
