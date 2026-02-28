<?php

namespace App\Filament\Admin\Resources\Analytics;

use App\Filament\Admin\Resources\Analytics\Pages\CreateAnalytic;
use App\Filament\Admin\Resources\Analytics\Pages\EditAnalytic;
use App\Filament\Admin\Resources\Analytics\Pages\ListAnalytics;
use App\Filament\Admin\Resources\Analytics\Pages\ViewAnalytic;
use App\Filament\Admin\Resources\Analytics\Schemas\AnalyticForm;
use App\Filament\Admin\Resources\Analytics\Schemas\AnalyticInfolist;
use App\Filament\Admin\Resources\Analytics\Tables\AnalyticsTable;
use App\Models\Analytic;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AnalyticResource extends Resource
{
    protected static ?string $model = Analytic::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    protected static string|UnitEnum|null $navigationGroup = 'Analytics';

    public static function form(Schema $schema): Schema
    {
        return AnalyticForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AnalyticInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnalyticsTable::configure($table);
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
            'index' => ListAnalytics::route('/'),
            'create' => CreateAnalytic::route('/create'),
            'view' => ViewAnalytic::route('/{record}'),
            'edit' => EditAnalytic::route('/{record}/edit'),
        ];
    }
}
