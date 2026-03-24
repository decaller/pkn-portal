<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\RegistrationResource;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        $registrations = EventRegistration::query()
            ->where('booker_user_id', $request->user()->id)
            ->with(['event', 'participants'])
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return RegistrationResource::collection($registrations);
    }

    public function show(Request $request, EventRegistration $registration)
    {
        if ($registration->booker_user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return new RegistrationResource($registration->load(['event', 'participants']));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'organization_id' => ['nullable', 'exists:organizations,id'],
            'packages' => ['required', 'array', 'min:1'],
            'packages.*.package_id' => ['required', 'string'],
            'packages.*.count' => ['required', 'integer', 'min:1'],
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
            $availablePackages = collect($event->registration_packages);

            foreach ($validated['packages'] as $pItem) {
                $selectedPackage = $availablePackages->firstWhere('id', $pItem['package_id']);

                if ($selectedPackage) {
                    $price = $selectedPackage['price'] ?? 0;
                    $count = $pItem['count'];
                    $subtotal = $price * $count;
                    $totalAmount += $subtotal;

                    $packageBreakdown[] = [
                        'package_id' => $pItem['package_id'],
                        'name' => $selectedPackage['name'] ?? 'Selected Package',
                        'count' => $count,
                        'price' => $price,
                        'subtotal' => $subtotal,
                    ];
                } else {
                    throw ValidationException::withMessages([
                        'packages' => "Package ID {$pItem['package_id']} is invalid for this event.",
                    ]);
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
