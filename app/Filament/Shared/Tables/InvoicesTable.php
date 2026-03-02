<?php

namespace App\Filament\Shared\Tables;

use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event_registration_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('invoice_number')
                    ->label(__('Invoice #'))
                    ->searchable(),
                TextColumn::make('version')
                    ->label(__('Version'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->searchable(),
                TextColumn::make('issued_at')
                    ->label(__('Issued at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('due_at')
                    ->label(__('Due at'))
                    ->date()
                    ->sortable(),
                TextColumn::make('currency')
                    ->label(__('Currency'))
                    ->searchable(),
                TextColumn::make('subtotal')
                    ->label(__('Subtotal'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('discount_amount')
                    ->label(__('Discount'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tax_amount')
                    ->label(__('Tax'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label(__('Total'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('voided_at')
                    ->label(__('Voided at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('void_reason')
                    ->label(__('Void reason'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('download')
                    ->label(__('Download PDF'))
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn (\App\Models\Invoice $record): string => route('invoices.download', $record))
                    ->openUrlInNewTab(),
                ViewAction::make(),
            ]);
    }
}
