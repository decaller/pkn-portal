<?php

namespace App\Filament\User\Resources\Invoices\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class InvoiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('event_registration_id')
                    ->numeric(),
                TextEntry::make('invoice_number'),
                TextEntry::make('version')
                    ->numeric(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('issued_at')
                    ->dateTime(),
                TextEntry::make('due_at')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('currency'),
                TextEntry::make('subtotal')
                    ->numeric(),
                TextEntry::make('discount_amount')
                    ->numeric(),
                TextEntry::make('tax_amount')
                    ->numeric(),
                TextEntry::make('total_amount')
                    ->numeric(),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('voided_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('void_reason')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
