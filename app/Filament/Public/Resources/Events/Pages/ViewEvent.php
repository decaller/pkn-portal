<?php

namespace App\Filament\Public\Resources\Events\Pages;

use App\Filament\Public\Resources\Events\EventResource;
use Filament\Resources\Pages\ViewRecord;

class ViewEvent extends ViewRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('register')
                ->label('Register Now')
                ->icon('heroicon-m-ticket')
                ->color('success')
                ->url(fn () => route('filament.user.auth.register', ['event_id' => $this->getRecord()->id])),
        ];
    }
}
