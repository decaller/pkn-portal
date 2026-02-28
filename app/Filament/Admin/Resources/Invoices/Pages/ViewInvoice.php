<?php

namespace App\Filament\Admin\Resources\Invoices\Pages;

use App\Filament\Admin\Resources\EventRegistrations\EventRegistrationResource;
use App\Filament\Admin\Resources\Invoices\InvoiceResource;
use App\Services\InvoicePdfService;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(
                    fn () => app(InvoicePdfService::class)->download(
                        $this->record,
                    ),
                ),
            Action::make('view_registration')
                ->label('View Registration')
                ->icon('heroicon-o-ticket')
                ->color('gray')
                ->url(fn (\App\Models\Invoice $record): string => EventRegistrationResource::getUrl('view', ['record' => $record->event_registration_id])),

        ];
    }
}
