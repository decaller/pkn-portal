<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ParticipantResource;
use App\Models\EventRegistration;
use App\Models\RegistrationParticipant;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ParticipantController extends Controller
{
    public function index(Request $request, $registration_id)
    {
        $registration = EventRegistration::findOrFail($registration_id);

        if ($registration->booker_user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return ParticipantResource::collection($registration->participants);
    }

    public function store(Request $request, $registration_id)
    {
        $registration = EventRegistration::findOrFail($registration_id);

        if ($registration->booker_user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($registration->event->isFull()) {
            throw ValidationException::withMessages(['participant' => 'The event is already full.']);
        }

        if ($registration->isPaidOrAwaitingVerification()) {
            throw ValidationException::withMessages(['participant' => 'Cannot add a participant to a registration that is paid or awaiting verification.']);
        }

        $validated = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $participant = RegistrationParticipant::create(array_merge($validated, [
            'registration_id' => $registration->id,
        ]));

        return new ParticipantResource($participant);
    }

    public function update(Request $request, RegistrationParticipant $participant)
    {
        if ($participant->registration->booker_user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($participant->registration->isPaidOrAwaitingVerification()) {
            throw ValidationException::withMessages(['participant' => 'Cannot modify a participant when registration is paid.']);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $participant->update($validated);

        return new ParticipantResource($participant);
    }

    public function destroy(Request $request, RegistrationParticipant $participant)
    {
        if ($participant->registration->booker_user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (! $participant->registration->canRemoveParticipants()) {
            throw ValidationException::withMessages(['participant' => 'Cannot remove participant after payment has been submitted.']);
        }

        $participant->delete();

        return response()->json(['message' => 'Participant removed successfully.']);
    }
}
