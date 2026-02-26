<?php

namespace App\Filament\User\Resources\Documents\Pages;

use App\Filament\User\Resources\Documents\DocumentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDocument extends ViewRecord
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('view_event')
                ->label('View Related Event')
                ->icon('heroicon-o-link')
                ->color('info')
                ->url(fn (): string => \App\Filament\User\Resources\Events\EventResource::getUrl('view', ['record' => $this->record->event_id]))
                ->visible(fn (): bool => $this->record->event_id !== null),
            // EditAction::make(),
        ];
    }
}
