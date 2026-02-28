<?php

namespace App\Filament\User\Resources\Documents;

// No form needed
use App\Filament\Shared\Schemas\DocumentForm as AppFilamentSharedSchemasDocumentForm;
use App\Filament\Shared\Schemas\DocumentInfolist as AppFilamentSharedSchemasDocumentInfolist;
use App\Filament\Shared\Tables\DocumentsTable;
use App\Filament\User\Resources\Documents\Pages\ListDocuments;
use App\Filament\User\Resources\Documents\Pages\ViewDocument;
use App\Models\Document;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Information';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return AppFilamentSharedSchemasDocumentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AppFilamentSharedSchemasDocumentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DocumentsTable::configure($table);
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
            'index' => ListDocuments::route('/'),
            'view' => ViewDocument::route('/{record}'),
        ];
    }
}
