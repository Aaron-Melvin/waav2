<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\Controller;
use App\Http\Resources\WebhookDeliveryResource;
use App\Models\Partner;
use App\Models\Webhook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WebhookDeliveryController extends Controller
{
    public function index(Request $request, string $webhook): AnonymousResourceCollection|JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $webhookModel = Webhook::query()
            ->where('partner_id', $partner->id)
            ->findOrFail($webhook);

        $perPage = max(1, min($request->integer('per_page', 50), 100));
        $status = trim($request->string('status', '')->toString());

        $deliveries = $webhookModel->deliveries()
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return WebhookDeliveryResource::collection($deliveries);
    }
}
