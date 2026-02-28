<?php

namespace App\Filament\Admin\Resources\Invoices;

use App\Filament\Admin\Resources\Invoices\Pages\ListInvoices;
use App\Filament\Admin\Resources\Invoices\Pages\ViewInvoice;
use App\Filament\Admin\Resources\Invoices\Tables\InvoicesTable;
use App\Filament\Shared\Schemas\InvoiceInfolist;
use App\Models\Invoice;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Event Management';

    protected static ?string $navigationLabel = 'Invoices';

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function infolist(Schema $schema): Schema
    {
        return InvoiceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InvoicesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with([
            'registration.event',
            'registration.organization',
            'registration.booker',
            'items',
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInvoices::route('/'),
            'view' => ViewInvoice::route('/{record}'),
        ];
    }
}
