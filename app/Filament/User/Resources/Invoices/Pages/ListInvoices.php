<?php

namespace App\Filament\User\Resources\Invoices\Pages;

use App\Filament\User\Resources\Invoices\InvoiceResource;
// No actions needed
use Filament\Resources\Pages\ListRecords;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
