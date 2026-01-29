<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InvoiceController extends Controller
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
        $bookingId = trim($request->string('booking_id', '')->toString());

        $invoices = Invoice::query()
            ->where('partner_id', $partner->id)
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($bookingId !== '', fn ($query) => $query->where('booking_id', $bookingId))
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return InvoiceResource::collection($invoices);
    }

    public function show(Request $request, string $invoice): InvoiceResource|JsonResponse
    {
        /** @var Partner|null $partner */
        $partner = $request->attributes->get('currentPartner');

        if (! $partner) {
            return response()->json([
                'message' => 'Partner context is required for this request.',
            ], 403);
        }

        $invoiceModel = Invoice::query()
            ->where('partner_id', $partner->id)
            ->findOrFail($invoice);

        return new InvoiceResource($invoiceModel);
    }
}
