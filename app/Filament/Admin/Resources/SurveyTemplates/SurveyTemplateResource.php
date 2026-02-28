<?php

namespace App\Filament\Admin\Resources\SurveyTemplates;

use App\Filament\Admin\Resources\SurveyTemplates\Pages\CreateSurveyTemplate;
use App\Filament\Admin\Resources\SurveyTemplates\Pages\EditSurveyTemplate;
use App\Filament\Admin\Resources\SurveyTemplates\Pages\ListSurveyTemplates;
use App\Filament\Admin\Resources\SurveyTemplates\Schemas\SurveyTemplateForm;
use App\Filament\Admin\Resources\SurveyTemplates\Tables\SurveyTemplatesTable;
use App\Models\SurveyTemplate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SurveyTemplateResource extends Resource
{
    protected static ?string $model = SurveyTemplate::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|\UnitEnum|null $navigationGroup = 'Event Management';

    public static function form(Schema $schema): Schema
    {
        return SurveyTemplateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SurveyTemplatesTable::configure($table);
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
            'index' => ListSurveyTemplates::route('/'),
            'create' => CreateSurveyTemplate::route('/create'),
            'edit' => EditSurveyTemplate::route('/{record}/edit'),
        ];
    }
}
