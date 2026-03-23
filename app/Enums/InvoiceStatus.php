<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum InvoiceStatus: string implements HasColor, HasLabel
{
    case Issued = 'issued';
    case Pending = 'pending';
    case Paid = 'paid';
    case Expired = 'expired';
    case Cancelled = 'cancelled';
    case Void = 'void';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Issued => 'Issued',
            self::Pending => 'Pending',
            self::Paid => 'Paid',
            self::Expired => 'Expired',
            self::Cancelled => 'Cancelled',
            self::Void => 'Void',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Issued => 'success',
            self::Pending => 'warning',
            self::Paid => 'success',
            self::Expired => 'gray',
            self::Cancelled => 'danger',
            self::Void => 'danger',
        };
    }
}
