<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Unpaid = 'unpaid';
    case Submitted = 'submitted';
    case Verified = 'verified';
    case Rejected = 'rejected';

    public function getLabel(): string
    {
        return match ($this) {
            self::Unpaid => 'Unpaid',
            self::Submitted => 'Pending Payment',
            self::Verified => 'Paid',
            self::Rejected => 'Failed',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Unpaid => 'gray',
            self::Submitted => 'warning',
            self::Verified => 'success',
            self::Rejected => 'danger',
        };
    }
}
