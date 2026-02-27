<?php

namespace App\Filament\Admin\Resources\News\Pages;

use App\Filament\Admin\Resources\News\NewsResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewNews extends ViewRecord
{
    protected static string $resource = NewsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    public function mount(int|string $record): void
    {
        // 1. Load the record so we can see it
        parent::mount($record);

        // 2. The Tracker Logic
        // We only want to count it if it's NOT a duplicate view in the last minute (optional, but smart)
        // For now, let's just log EVERY view so you can see it working.

        $this->record->analytics()->create([
            'user_id' => Auth::id(), // Who is reading? (You)
            'action' => 'view',      // What did they do?
            'platform' => 'Admin Panel', // Where are they?
        ]);
    }
}
