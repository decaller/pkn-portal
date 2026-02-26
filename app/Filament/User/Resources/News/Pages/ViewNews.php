<?php

namespace App\Filament\User\Resources\News\Pages;

use App\Filament\User\Resources\News\NewsResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewNews extends ViewRecord
{
    protected static string $resource = NewsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('share_twitter')
                ->label('Share on X')
                ->icon('heroicon-o-share')
                ->color('info')
                ->url(fn (): string => 'https://twitter.com/intent/tweet?text=' . urlencode($this->record->title) . '&url=' . urlencode(request()->url()))
                ->openUrlInNewTab(),
            \Filament\Actions\Action::make('share_whatsapp')
                ->label('Share on WhatsApp')
                ->icon('heroicon-o-chat-bubble-oval-left')
                ->color('success')
                ->url(fn (): string => 'https://api.whatsapp.com/send?text=' . urlencode($this->record->title . ' ' . request()->url()))
                ->openUrlInNewTab(),
        ];
    }
}
