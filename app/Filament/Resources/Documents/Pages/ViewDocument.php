<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewDocument extends ViewRecord
{
    protected static string $resource = DocumentResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->record->analytics()->create([
            "user_id" => Auth::id(),
            "action" => "view",
            "platform" => "Admin Panel",
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('view_event')
                ->label('View Related Event')
                ->icon('heroicon-o-link')
                ->color('info')
                ->url(fn (): string => \App\Filament\Resources\Events\EventResource::getUrl('view', ['record' => $this->record->event_id]))
                ->visible(fn (): bool => $this->record->event_id !== null),
            EditAction::make(),
        ];
    }
}
