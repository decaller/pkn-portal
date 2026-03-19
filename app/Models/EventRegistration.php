<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Notifications\PaymentApprovedNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class EventRegistration extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'event_id',
        'organization_id',
        'package_breakdown',
        'booker_user_id',
        'status',
        'payment_status',
        'total_amount',
        'payment_proof_path',
        'notes',
        'verified_by_user_id',
        'verified_at',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected function casts(): array
    {
        return [
            'status' => RegistrationStatus::class,
            'payment_status' => PaymentStatus::class,
            'package_breakdown' => 'array',
            'total_amount' => 'decimal:2',
            'verified_at' => 'datetime',
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
        return $this->belongsTo(User::class, 'booker_user_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(
            RegistrationParticipant::class,
            'registration_id',
        );
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class)->orderByDesc('version');
    }

    public function latestInvoice(): HasOne
    {
        return $this->hasOne(Invoice::class)->latestOfMany('version');
    }

    public function invoicePayments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
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
        return ! $this->hasGatewayPendingPayment()
            && $this->payment_status !== PaymentStatus::Verified;
    }

    public function hasGatewayPendingPayment(): bool
    {
        if ($this->relationLoaded('invoicePayments')) {
            return $this->invoicePayments->contains(
                fn (InvoicePayment $payment): bool => $payment->isPendingLike()
                    && $payment->expires_at?->isFuture() !== false,
            );
        }

        return $this->invoicePayments()
            ->where('provider', InvoicePayment::PROVIDER_MIDTRANS)
            ->whereIn('status', InvoicePayment::pendingStatuses())
            ->where(function ($query): void {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->exists();
    }

    public function markPaymentPendingFromGateway(array $attributes = []): void
    {
        if ($this->payment_status === PaymentStatus::Verified) {
            return;
        }

        $this->forceFill([
            'payment_status' => PaymentStatus::Submitted,
            'status' => RegistrationStatus::PendingPayment,
            'verified_by_user_id' => null,
            'verified_at' => null,
        ])->save();
    }

    public function markPaidFromGateway(array $attributes = []): void
    {
        $wasPaid = $this->payment_status === PaymentStatus::Verified
            && $this->status === RegistrationStatus::Paid;

        $this->forceFill([
            'payment_status' => PaymentStatus::Verified,
            'status' => RegistrationStatus::Paid,
            'verified_by_user_id' => null,
            'verified_at' => $attributes['verified_at'] ?? now(),
        ])->save();

        if (! $wasPaid && $this->booker) {
            $this->booker->notify(new PaymentApprovedNotification($this));
        }
    }

    public function markPaymentFailedFromGateway(array $attributes = []): void
    {
        if ($this->payment_status === PaymentStatus::Verified) {
            return;
        }

        $this->forceFill([
            'payment_status' => PaymentStatus::Rejected,
            'status' => RegistrationStatus::Draft,
            'verified_by_user_id' => null,
            'verified_at' => null,
        ])->save();
    }
}
