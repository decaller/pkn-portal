<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::whereHas('registration', function ($query) use ($request) {
            $query->where('booker_user_id', $request->user()->id);
        })->with('registration.event')->get();

        return InvoiceResource::collection($invoices);
    }

    public function show(Request $request, Invoice $invoice)
    {
        if ($invoice->registration->booker_user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return new InvoiceResource($invoice->load('items', 'payments', 'registration.event'));
    }

    public function download(Request $request, Invoice $invoice)
    {
        if ($invoice->registration->booker_user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Return a temporary URL or route to the web invoice download endpoint.
        // Assuming there is or will be a web route named `invoice.download`
        return response()->json([
            'download_url' => url("/invoices/{$invoice->id}/download?token=".$request->user()->createToken('invoice-download')->plainTextToken),
        ]);
    }
}
