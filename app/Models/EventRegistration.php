<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use DomainException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class EventRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        "event_id",
        "organization_id",
        "package_breakdown",
        "booker_user_id",
        "status",
        "payment_status",
        "total_amount",
        "payment_proof_path",
        "notes",
        "verified_by_user_id",
        "verified_at",
    ];

    protected function casts(): array
    {
        return [
            "status" => RegistrationStatus::class,
            "payment_status" => PaymentStatus::class,
            "package_breakdown" => "array",
            "total_amount" => "decimal:2",
            "verified_at" => "datetime",
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function booker(): BelongsTo
    {
        return $this->belongsTo(User::class, "booker_user_id");
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, "verified_by_user_id");
    }

    public function participants(): HasMany
    {
        return $this->hasMany(
            RegistrationParticipant::class,
            "registration_id",
        );
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class)->orderByDesc("version");
    }

    public function latestInvoice(): HasOne
    {
        return $this->hasOne(Invoice::class)->latestOfMany("version");
    }

    public function isPaidOrAwaitingVerification(): bool
    {
        return in_array(
            $this->payment_status,
            [PaymentStatus::Submitted, PaymentStatus::Verified],
            true,
        );
    }

    public function canRemoveParticipants(): bool
    {
        return !$this->isPaidOrAwaitingVerification();
    }

    public function submitPaymentProof(string $proofPath): void
    {
        $this->forceFill([
            "payment_proof_path" => $proofPath,
            "payment_status" => PaymentStatus::Submitted,
            "status" => RegistrationStatus::PendingPayment,
        ])->save();
    }

    public function verifyPayment(User $actor): void
    {
        if (!$actor->isMainAdmin()) {
            throw new DomainException("Only main admins can verify payments.");
        }

        $this->forceFill([
            "payment_status" => PaymentStatus::Verified,
            "status" => RegistrationStatus::Paid,
            "verified_by_user_id" => $actor->getKey(),
            "verified_at" => now(),
        ])->save();
    }
}
