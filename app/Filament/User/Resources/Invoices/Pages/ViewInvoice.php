<?php

namespace App\Filament\User\Resources\Invoices\Pages;

use App\Filament\User\Resources\EventRegistrations\EventRegistrationResource;
use App\Filament\User\Resources\Invoices\InvoiceResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download')
                ->label('Download PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->url(fn (\App\Models\Invoice $record): string => route('invoices.download', $record))
                ->openUrlInNewTab(),

            Action::make('view_registration')
                ->label('View Registration')
                ->icon('heroicon-o-ticket')
                ->color('gray')
                ->url(fn (\App\Models\Invoice $record): string => EventRegistrationResource::getUrl('view', ['record' => $record->event_registration_id])),
        ];
    }
}
