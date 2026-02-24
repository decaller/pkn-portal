<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        "event_registration_id",
        "invoice_number",
        "version",
        "status",
        "issued_at",
        "due_at",
        "currency",
        "event_snapshot",
        "organization_snapshot",
        "booker_snapshot",
        "subtotal",
        "discount_amount",
        "tax_amount",
        "total_amount",
        "notes",
        "voided_at",
        "void_reason",
    ];

    protected function casts(): array
    {
        return [
            "status" => InvoiceStatus::class,
            "issued_at" => "datetime",
            "due_at" => "date",
            "event_snapshot" => "array",
            "organization_snapshot" => "array",
            "booker_snapshot" => "array",
            "subtotal" => "decimal:2",
            "discount_amount" => "decimal:2",
            "tax_amount" => "decimal:2",
            "total_amount" => "decimal:2",
            "voided_at" => "datetime",
        ];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(EventRegistration::class, "event_registration_id");
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
