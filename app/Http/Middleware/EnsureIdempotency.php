<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\IdempotencyKey;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\CarbonImmutable;
use Symfony\Component\HttpFoundation\Response;

class EnsureIdempotency
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->isMutation($request)) {
            return $next($request);
        }

        $key = $request->header('Idempotency-Key') ?? $request->header('X-Idempotency-Key');

        if (! $key) {
            return $next($request);
        }

        [$scopeType, $scopeId, $partnerId] = $this->resolveScope($request);

        if (! $scopeType || ! $scopeId) {
            return $next($request);
        }

        $hash = hash('sha256', $this->requestFingerprint($request));

        $record = IdempotencyKey::query()
            ->where('key', $key)
            ->where(function ($query) use ($scopeType, $scopeId, $partnerId): void {
                $query
                    ->where(function ($inner) use ($scopeType, $scopeId): void {
                        $inner
                            ->where('scope_type', $scopeType)
                            ->where('scope_id', $scopeId);
                    })
                    ->orWhere(function ($inner) use ($partnerId): void {
                        if ($partnerId) {
                            $inner
                                ->whereNull('scope_type')
                                ->whereNull('scope_id')
                                ->where('partner_id', $partnerId);
                        }
                    });
            })
            ->first();

        if ($record) {
            if ($record->request_hash !== $hash) {
                return response()->json([
                    'message' => 'Idempotency key already used with a different payload.',
                ], 409);
            }

            if ($record->response) {
                return response()->json($record->response);
            }

            return response()->json([
                'message' => 'Idempotency key is already being processed.',
            ], 409);
        }

        $record = IdempotencyKey::query()->create([
            'partner_id' => $partnerId,
            'scope_type' => $scopeType,
            'scope_id' => $scopeId,
            'key' => $key,
            'request_hash' => $hash,
            'status' => 'pending',
            'expires_at' => CarbonImmutable::now()->addMinutes(30),
        ]);

        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $record->update([
                'status' => 'completed',
                'response' => $response->getData(true),
            ]);
        }

        return $response;
    }

    protected function isMutation(Request $request): bool
    {
        return in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true);
    }

    protected function requestFingerprint(Request $request): string
    {
        $query = $request->getQueryString();

        return implode('|', [
            $request->method(),
            $request->path(),
            $query ?? '',
            $request->getContent(),
        ]);
    }

    /**
     * @return array{0: ?string, 1: ?string, 2: ?string}
     */
    protected function resolveScope(Request $request): array
    {
        $partner = $request->attributes->get('currentPartner');

        if ($partner instanceof Partner) {
            return [Partner::class, $partner->id, $partner->id];
        }

        $user = $request->user();

        if ($user instanceof User) {
            return [User::class, (string) $user->getAuthIdentifier(), null];
        }

        return [null, null, null];
    }
}
