<?php

namespace App\Filament\User\Resources\Documents\Pages;

use App\Filament\Public\Resources\Events\EventResource;
use App\Filament\User\Resources\Documents\DocumentResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewDocument extends ViewRecord
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_event')
                ->label('View Related Event')
                ->icon('heroicon-o-link')
                ->color('info')
                ->url(fn (): string => EventResource::getUrl('view', ['record' => $this->record->event_id]))
                ->visible(fn (): bool => $this->record->event_id !== null),

        ];
    }
}
