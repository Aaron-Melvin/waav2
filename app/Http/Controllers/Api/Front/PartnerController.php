<?php

namespace App\Http\Controllers\Api\Front;

use App\Http\Controllers\Controller;
use App\Http\Resources\PartnerResource;
use App\Models\Partner;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PartnerController extends Controller
{
    public function show(Request $request): PartnerResource|Response
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        return new PartnerResource($partner);
    }
}
