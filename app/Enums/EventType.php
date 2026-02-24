<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EventType: string implements HasLabel, HasColor
{
    case Offline = 'offline';
    case Online = 'online';
    case Mixed = 'mixed';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Offline => 'Offline Only',
            self::Online => 'Online Only',
            self::Mixed => 'Mixed (Offline & Online)',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Offline => 'warning',
            self::Online => 'info',
            self::Mixed => 'success',
        };
    }
}
