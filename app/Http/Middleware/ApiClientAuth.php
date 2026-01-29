<?php

namespace App\Http\Middleware;

use App\Models\ApiClient;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiClientAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $clientId = $request->header('X-Client-Id') ?? $request->header('X-Api-Client');
        $clientSecret = $request->header('X-Client-Secret') ?? $request->header('X-Api-Secret');

        if (! $clientId || ! $clientSecret) {
            return response()->json([
                'message' => 'API client credentials are required.',
            ], 401);
        }

        $apiClient = ApiClient::query()
            ->with('partner')
            ->where('client_id', $clientId)
            ->where('status', 'active')
            ->first();

        if (! $apiClient || ! $apiClient->verifySecret($clientSecret)) {
            return response()->json([
                'message' => 'Invalid API client credentials.',
            ], 401);
        }

        if (! $apiClient->partner) {
            return response()->json([
                'message' => 'API client is not linked to an active partner.',
            ], 403);
        }

        if ($apiClient->partner->status !== 'active') {
            return response()->json([
                'message' => 'Partner is not active.',
            ], 403);
        }

        $apiClient->forceFill([
            'last_used_at' => now(),
        ])->save();

        $request->attributes->set('apiClient', $apiClient);
        $request->attributes->set('currentPartner', $apiClient->partner);

        return $next($request);
    }
}
