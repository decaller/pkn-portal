<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\RegistrationResource;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\RegistrationParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RegistrationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'package_id' => ['nullable', 'string'],
            'participants' => ['required', 'array', 'min:1'],
            'participants.*.name' => ['required', 'string'],
            'participants.*.email' => ['nullable', 'email'],
            'participants.*.phone' => ['nullable', 'string'],
        ]);

        $event = Event::findOrFail($validated['event_id']);

        if (! $event->allow_registration) {
            throw ValidationException::withMessages(['event_id' => 'Registration is not allowed for this event.']);
        }

        if ($event->isFull()) {
            throw ValidationException::withMessages(['event_id' => 'This event is fully booked.']);
        }

        return DB::transaction(function () use ($validated, $event, $request) {
            $totalAmount = 0;
            $packageBreakdown = [];

            if (! empty($validated['package_id']) && ! empty($event->registration_packages)) {
                $packages = collect($event->registration_packages);
                $selectedPackage = $packages->firstWhere('id', $validated['package_id']);

                if ($selectedPackage) {
                    $price = $selectedPackage['price'] ?? 0;
                    $quantity = count($validated['participants']);
                    $totalAmount = $price * $quantity;

                    $packageBreakdown = [
                        [
                            'package_name' => $selectedPackage['name'] ?? 'Selected Package',
                            'price' => $price,
                            'quantity' => $quantity,
                            'subtotal' => $totalAmount,
                        ],
                    ];
                }
            }

            $registration = EventRegistration::create([
                'event_id' => $event->id,
                'organization_id' => $validated['organization_id'] ?? null,
                'package_breakdown' => $packageBreakdown,
                'booker_user_id' => $request->user()->id,
                'status' => RegistrationStatus::Draft,
                'payment_status' => PaymentStatus::Unpaid,
                'total_amount' => $totalAmount,
            ]);

            foreach ($validated['participants'] as $pData) {
                RegistrationParticipant::create(array_merge($pData, [
                    'registration_id' => $registration->id,
                ]));
            }

            return new RegistrationResource($registration->load('participants', 'event'));
        });
    }

    public function update(Request $request, EventRegistration $registration)
    {
        if ($registration->booker_user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($registration->isPaidOrAwaitingVerification()) {
            throw ValidationException::withMessages(['registration' => 'Cannot modify a registration that is paid or awaiting verification.']);
        }

        $validated = $request->validate([
            'organization_id' => ['nullable', 'exists:organizations,id'],
        ]);

        $registration->update([
            'organization_id' => $validated['organization_id'] ?? $registration->organization_id,
        ]);

        return new RegistrationResource($registration->load('participants', 'event'));
    }

    public function destroy(Request $request, EventRegistration $registration)
    {
        if ($registration->booker_user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($registration->isPaidOrAwaitingVerification()) {
            throw ValidationException::withMessages(['registration' => 'Cannot cancel a registration that is paid or awaiting verification.']);
        }

        $registration->delete();

        return response()->json(['message' => 'Registration cancelled successfully.']);
    }
}
