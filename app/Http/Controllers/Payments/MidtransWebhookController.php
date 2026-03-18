<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\InvoicePayment;
use App\Services\Payments\InvoicePaymentService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MidtransWebhookController extends Controller
{
    public function __invoke(Request $request, InvoicePaymentService $paymentService): JsonResponse
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

        if (! InvoicePayment::query()->where('order_id', $payload['order_id'])->exists()) {
            return response()->json(['message' => 'unknown order'], 404);
        }

        if (! $this->hasValidSignature($payload)) {
            return response()->json(['message' => 'invalid signature'], 403);
        }

        try {
            $paymentService->handleMidtransNotification($request->all());
        } catch (ModelNotFoundException) {
            return response()->json(['message' => 'unknown order'], 404);
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
