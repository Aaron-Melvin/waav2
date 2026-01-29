<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolvePartner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->attributes->has('currentPartner')) {
            $partner = $request->user()?->partner;

            if ($partner) {
                $request->attributes->set('currentPartner', $partner);
            }
        }

        return $next($request);
    }
}
