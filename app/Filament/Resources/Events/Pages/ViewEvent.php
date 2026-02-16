<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;


class ViewEvent extends ViewRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
    public function mount(int | string $record): void
    {
        // 1. Load the record data
        parent::mount($record);

        // 2. Track the View
        // This relies on the 'analytics()' relationship in your Event model
        $this->record->analytics()->create([
            'user_id' => Auth::id(),
            'action' => 'view',
            'platform' => 'Admin Panel',
        ]);
    }
}
