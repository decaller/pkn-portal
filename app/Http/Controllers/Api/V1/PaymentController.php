<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\InvoiceResource;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Services\Payments\InvoicePaymentService;
use DomainException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function charge(Request $request, InvoicePaymentService $paymentService): JsonResponse
    {
        $validated = $request->validate([
            'invoice_id' => ['required', 'integer', 'exists:invoices,id'],
        ]);

        $invoice = Invoice::query()
            ->with(['registration', 'latestPayment'])
            ->findOrFail($validated['invoice_id']);

        if ($invoice->registration?->booker_user_id !== $request->user()->getKey()) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice is already paid or does not exist.',
            ], 404);
        }

        $status = (new InvoiceResource($invoice))->resolve($request)['status'] ?? null;
        if (! in_array($status, ['unpaid', 'pending'], true) || ! $invoice->canStartGatewayPayment()) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice is already paid or does not exist.',
            ], 422);
        }

        try {
            $payment = $paymentService->createOrReuseSnapPayment($invoice);
        } catch (DomainException) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice is already paid or does not exist.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'redirect_url' => $payment->snap_redirect_url,
            'token' => $payment->snap_token,
        ]);
    }

    public function webhook(Request $request, InvoicePaymentService $paymentService): JsonResponse
    {
        $payload = Validator::make($request->all(), [
            'order_id' => ['required', 'string'],
            'status_code' => ['required', 'string'],
            'gross_amount' => ['required'],
            'signature_key' => ['required', 'string'],
            'transaction_status' => ['required', 'string'],
            'transaction_id' => ['nullable', 'string'],
            'payment_type' => ['nullable', 'string'],
            'fraud_status' => ['nullable', 'string'],
        ])->validate();

        if ($this->hasValidSignature($payload) && InvoicePayment::query()->where('order_id', $payload['order_id'])->exists()) {
            try {
                $paymentService->handleMidtransNotification($payload);
            } catch (ModelNotFoundException) {
                // Midtrans expects an acknowledgement even when the order is stale or missing locally.
            }
        }

        return response()->json(['message' => 'ok']);
    }

    private function hasValidSignature(array $payload): bool
    {
        $expected = hash(
            'sha512',
            $payload['order_id'].$payload['status_code'].$payload['gross_amount'].config('services.midtrans.server_key'),
        );

        return hash_equals($expected, $payload['signature_key']);
    }
}
