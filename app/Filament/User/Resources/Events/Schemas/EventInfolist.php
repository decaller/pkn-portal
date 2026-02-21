<?php

namespace App\Filament\User\Resources\Events\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EventInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Event')
                    ->schema([
                        TextEntry::make('title')->weight('bold'),
                        TextEntry::make('event_date')->date('d M Y'),
                        ImageEntry::make('cover_image')->height(180),
                        TextEntry::make('description')->markdown(),
                    ]),
            ]);
    }
}
