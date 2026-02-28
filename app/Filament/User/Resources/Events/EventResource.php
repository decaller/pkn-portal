<?php

namespace App\Filament\User\Resources\Events;

use App\Filament\Resources\Events\Schemas\EventForm;
use App\Filament\Shared\Schemas\EventInfolist;
use App\Filament\User\Resources\Events\Pages\ListEvents;
use App\Filament\User\Resources\Events\Pages\ViewEvent;
use App\Filament\User\Resources\Events\Tables\EventsTable;
use App\Models\Event;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDateRange;

    protected static string|UnitEnum|null $navigationGroup = 'Events';

    protected static ?string $navigationLabel = 'Events';

    public static function form(Schema $schema): Schema
    {
        return EventForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EventInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EventsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('is_published', true)
            ->orderBy('event_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEvents::route('/'),
            'view' => ViewEvent::route('/{record}'),
        ];
    }
}
