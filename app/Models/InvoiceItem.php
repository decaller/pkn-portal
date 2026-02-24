<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        "invoice_id",
        "package_name",
        "participant_count",
        "unit_price",
        "line_total",
        "metadata",
    ];

    protected function casts(): array
    {
        return [
            "participant_count" => "integer",
            "unit_price" => "decimal:2",
            "line_total" => "decimal:2",
            "metadata" => "array",
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
