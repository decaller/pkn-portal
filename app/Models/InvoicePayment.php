<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoicePayment extends Model
{
    use HasFactory;

    public const PROVIDER_MIDTRANS = 'midtrans';

    public const STATUS_PENDING = 'pending';

    public const STATUS_PAID = 'paid';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'invoice_id',
        'event_registration_id',
        'provider',
        'order_id',
        'status',
        'gross_amount',
        'currency',
        'snap_token',
        'snap_redirect_url',
        'midtrans_transaction_id',
        'midtrans_transaction_status',
        'midtrans_payment_type',
        'midtrans_fraud_status',
        'raw_snap_response',
        'raw_notification_payload',
        'metadata',
        'expires_at',
        'paid_at',
        'last_notified_at',
    ];

    protected function casts(): array
    {
        return [
            'gross_amount' => 'decimal:2',
            'raw_snap_response' => 'array',
            'raw_notification_payload' => 'array',
            'metadata' => 'array',
            'expires_at' => 'datetime',
            'paid_at' => 'datetime',
            'last_notified_at' => 'datetime',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(EventRegistration::class, 'event_registration_id');
    }

    public static function pendingStatuses(): array
    {
        return [self::STATUS_PENDING];
    }

    public function isPendingLike(): bool
    {
        return in_array($this->status, self::pendingStatuses(), true);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }
}
