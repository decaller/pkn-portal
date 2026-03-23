<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::query()
            ->whereHas('registration', function ($query) use ($request) {
                $query->where('booker_user_id', $request->user()->id);
            })
            ->with(['registration.event', 'latestPayment'])
            ->latest('issued_at')
            ->paginate($request->integer('per_page', 15));

        return InvoiceResource::collection($invoices);
    }

    public function show(Request $request, Invoice $invoice)
    {
        if ($invoice->registration->booker_user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return new InvoiceResource($invoice->load(['items', 'payments', 'latestPayment', 'registration.event']));
    }

    public function download(Request $request, Invoice $invoice)
    {
        if ($invoice->registration->booker_user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'download_url' => URL::temporarySignedRoute(
                'invoices.temporary-download',
                now()->addMinutes(10),
                [
                    'invoice' => $invoice->getKey(),
                    'user' => $request->user()->getKey(),
                ],
            ),
        ]);
    }
}
