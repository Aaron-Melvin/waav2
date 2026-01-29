<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\StoreNotificationTemplateRequest;
use App\Http\Requests\Partner\UpdateNotificationTemplateRequest;
use App\Http\Resources\NotificationTemplateResource;
use App\Models\NotificationTemplate;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NotificationTemplateController extends Controller
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
        $channel = trim($request->string('channel', '')->toString());
        $status = trim($request->string('status', '')->toString());

        $templates = NotificationTemplate::query()
            ->where('partner_id', $partner->id)
            ->when($channel !== '', fn ($query) => $query->where('channel', $channel))
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->orderBy('name')
            ->paginate($perPage);

        return NotificationTemplateResource::collection($templates);
    }

    public function store(StoreNotificationTemplateRequest $request): JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $payload = $request->validated();

        $template = NotificationTemplate::query()->create([
            'partner_id' => $partner->id,
            'name' => $payload['name'],
            'channel' => $payload['channel'],
            'locale' => $payload['locale'] ?? 'en',
            'subject' => $payload['subject'] ?? null,
            'body' => $payload['body'],
            'status' => $payload['status'] ?? 'active',
            'meta' => $payload['meta'] ?? null,
        ]);

        return (new NotificationTemplateResource($template))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateNotificationTemplateRequest $request, string $template): NotificationTemplateResource|JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $templateModel = NotificationTemplate::query()
            ->where('partner_id', $partner->id)
            ->findOrFail($template);

        $payload = $request->validated();

        if (array_key_exists('subject', $payload) && ($payload['channel'] ?? $templateModel->channel) === 'sms') {
            $payload['subject'] = null;
        }

        $templateModel->update($payload);

        return new NotificationTemplateResource($templateModel);
    }
}
