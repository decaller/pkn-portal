<?php

namespace App\Filament\Public\Resources\Events;

use App\Filament\Public\Resources\Events\Pages\ListEvents;
use App\Filament\Public\Resources\Events\Pages\ViewEvent;
use App\Filament\Public\Resources\Events\Tables\EventsTable;
use App\Filament\Shared\Schemas\EventInfolist;
use App\Models\Event;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDateRange;

    public static function getNavigationGroup(): ?string
    {
        return __('Public Events');
    }

    public static function getNavigationLabel(): string
    {
        return __('Events');
    }

    public static function getModelLabel(): string
    {
        return __('Event');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Events');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function infolist(Schema $schema): Schema
    {
        return EventInfolist::configure($schema, [
            'showRundownFiles' => false,
            'showRundownLinks' => false,
            'showPublicRundownCta' => true,
        ]);
    }

    public static function table(Table $table): Table
    {
        return EventsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEvents::route('/'),
            'view' => ViewEvent::route('/{record}'),
        ];
    }
}
