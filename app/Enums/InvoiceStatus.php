<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum InvoiceStatus: string implements HasLabel, HasColor
{
    case Issued = "issued";
    case Void = "void";

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Issued => "Issued",
            self::Void => "Void",
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Issued => "success",
            self::Void => "danger",
        };
    }
}
