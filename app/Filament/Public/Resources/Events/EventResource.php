<?php

namespace App\Filament\Public\Resources\Events;

use App\Filament\Public\Resources\Events\Pages\ListEvents;
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

    protected static string|\UnitEnum|null $navigationGroup = 'Public Events';

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function infolist(Schema $schema): Schema
    {
        return AppFilamentSharedSchemasventInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AppFilamentPublicResourcesventsTablesventsTable::configure($table);
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
            'view' => \App\Filament\Public\Resources\Events\Pages\ViewEvent::route('/{record}'),
        ];
    }
}
