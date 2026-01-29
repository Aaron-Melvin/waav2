<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\StoreWebhookRequest;
use App\Http\Requests\Partner\UpdateWebhookRequest;
use App\Http\Resources\WebhookResource;
use App\Models\Partner;
use App\Models\Webhook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class WebhookController extends Controller
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

        $webhooks = Webhook::query()
            ->where('partner_id', $partner->id)
            ->orderBy('name')
            ->paginate($perPage);

        return WebhookResource::collection($webhooks);
    }

    public function store(StoreWebhookRequest $request): JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $payload = $request->validated();

        $webhook = Webhook::query()->create([
            'partner_id' => $partner->id,
            'name' => $payload['name'],
            'url' => $payload['url'],
            'events' => $payload['events'],
            'secret' => $payload['secret'] ?? Str::random(32),
            'headers' => $payload['headers'] ?? null,
            'status' => $payload['status'] ?? 'active',
        ]);

        return (new WebhookResource($webhook))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateWebhookRequest $request, string $webhook): WebhookResource|JsonResponse
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

        $payload = $request->validated();

        $webhookModel->update($payload);

        return new WebhookResource($webhookModel);
    }
}
