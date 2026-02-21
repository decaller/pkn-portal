<?php

namespace App\Enums;

enum RegistrationStatus: string
{
    case Draft = 'draft';
    case PendingPayment = 'pending_payment';
    case Paid = 'paid';
    case Closed = 'closed';
    case Cancelled = 'cancelled';
}
