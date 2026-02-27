<?php

namespace App\Filament\Public\Resources\News;

use App\Filament\Public\Resources\News\Pages\ListNews;
use App\Filament\Public\Resources\News\Pages\ViewNews;
use App\Filament\Public\Resources\News\Schemas\NewsInfolist;
use App\Filament\Shared\Tables\NewsTable;
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

    public static function infolist(Schema $schema): Schema
    {
        return NewsInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NewsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNews::route('/'),
            'view' => ViewNews::route('/{record}'),
        ];
    }
}
