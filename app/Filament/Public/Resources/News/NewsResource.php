<?php

namespace App\Filament\Public\Resources\News;

use App\Filament\Public\Resources\News\Pages\ListNews;
use App\Models\News;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static string|\UnitEnum|null $navigationGroup = 'Public Information';

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function infolist(Schema $schema): Schema
    {
        return AppFilamentSharedSchemasNewsInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AppFilamentPublicResourcesNewsTablesNewsTable::configure($table);
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
            'index' => ListNews::route('/'),
            'view' => \App\Filament\Public\Resources\News\Pages\ViewNews::route('/{record}'),
        ];
    }
}
