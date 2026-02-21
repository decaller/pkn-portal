<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RegistrationParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        "registration_id",
        "user_id",
        "name",
        "email",
        "phone",
        "notes",
    ];

    protected static function booted(): void
    {
        static::creating(function (RegistrationParticipant $participant): void {
            $participant->hydrateUserReference();
        });

        static::deleting(function (RegistrationParticipant $participant): void {
            if (
                !$participant->registration ||
                $participant->registration->canRemoveParticipants()
            ) {
                return;
            }

            throw ValidationException::withMessages([
                "participant" =>
                    "Participants cannot be removed after payment has been submitted.",
            ]);
        });
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(EventRegistration::class, "registration_id");
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function hydrateUserReference(): void
    {
        if ($this->user_id) {
            $existingUser = User::find($this->user_id);

            if (!$existingUser) {
                return;
            }

            $this->name = $this->name ?: $existingUser->name;
            $this->email = $this->email ?: $existingUser->email;

            return;
        }

        $email = $this->email ?: Str::uuid() . "@participant.local";

        $user = User::firstOrCreate(
            ["email" => $email],
            [
                "name" => $this->name ?: "Participant",
                "password" => Hash::make(Str::random(40)),
            ],
        );

        $this->user_id = $user->getKey();
        $this->name = $this->name ?: $user->name;
        $this->email = $this->email ?: $user->email;
    }
}
