<?php

namespace App\Filament\User\Resources\Invoices\Schemas;

use App\Enums\InvoiceStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('event_registration_id')
                    ->required()
                    ->numeric(),
                TextInput::make('invoice_number')
                    ->required(),
                TextInput::make('version')
                    ->required()
                    ->numeric()
                    ->default(1),
                Select::make('status')
                    ->options(InvoiceStatus::class)
                    ->default('issued')
                    ->required(),
                DateTimePicker::make('issued_at')
                    ->required(),
                DatePicker::make('due_at'),
                TextInput::make('currency')
                    ->required()
                    ->default('IDR'),
                TextInput::make('event_snapshot'),
                TextInput::make('organization_snapshot'),
                TextInput::make('booker_snapshot'),
                TextInput::make('subtotal')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('discount_amount')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('tax_amount')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_amount')
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('notes')
                    ->columnSpanFull(),
                DateTimePicker::make('voided_at'),
                TextInput::make('void_reason'),
            ]);
    }
}
