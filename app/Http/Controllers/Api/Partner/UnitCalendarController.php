<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\StoreUnitCalendarRequest;
use App\Http\Resources\UnitCalendarResource;
use App\Models\Partner;
use App\Models\Product;
use App\Models\Unit;
use App\Models\UnitCalendar;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UnitCalendarController extends Controller
{
    public function index(Request $request, string $product, string $unit): AnonymousResourceCollection|JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $productModel = Product::query()
            ->where('partner_id', $partner->id)
            ->findOrFail($product);

        $unitModel = Unit::query()
            ->where('product_id', $productModel->id)
            ->findOrFail($unit);

        $from = trim($request->string('from', '')->toString());
        $to = trim($request->string('to', '')->toString());

        $calendar = UnitCalendar::query()
            ->where('unit_id', $unitModel->id)
            ->when($from !== '', fn ($query) => $query->where('date', '>=', CarbonImmutable::parse($from)))
            ->when($to !== '', fn ($query) => $query->where('date', '<=', CarbonImmutable::parse($to)))
            ->orderBy('date')
            ->get();

        return UnitCalendarResource::collection($calendar);
    }

    public function store(StoreUnitCalendarRequest $request, string $product, string $unit): JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $productModel = Product::query()
            ->where('partner_id', $partner->id)
            ->findOrFail($product);

        $unitModel = Unit::query()
            ->where('product_id', $productModel->id)
            ->findOrFail($unit);

        $payload = $request->validated();

        $calendar = UnitCalendar::query()->updateOrCreate([
            'unit_id' => $unitModel->id,
            'date' => $payload['date'],
        ], [
            'partner_id' => $partner->id,
            'is_available' => $payload['is_available'],
            'min_stay_nights' => $payload['min_stay_nights'] ?? null,
            'max_stay_nights' => $payload['max_stay_nights'] ?? null,
            'reason' => $payload['reason'] ?? null,
        ]);

        return (new UnitCalendarResource($calendar))
            ->response()
            ->setStatusCode(201);
    }
}
