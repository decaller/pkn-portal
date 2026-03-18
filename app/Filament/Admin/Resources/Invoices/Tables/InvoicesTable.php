<?php

namespace App\Filament\Admin\Resources\Invoices\Tables;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label(__('Invoice #'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('registration.event.title')
                    ->label(__('Event'))
                    ->searchable(),
                TextColumn::make('registration.organization.name')
                    ->label(__('Organization'))
                    ->placeholder(__('Personal')),
                TextColumn::make('registration.booker.name')
                    ->label(__('Booker'))
                    ->searchable(),
                TextColumn::make('version')
                    ->label(__('Version'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
                    ->translateLabel()
                    ->badge()
                    ->formatStateUsing(
                        fn (InvoiceStatus|string|null $state): string => $state instanceof InvoiceStatus
                            ? $state->getLabel()
                            : InvoiceStatus::tryFrom((string) $state)?->getLabel() ?? '-',
                    )
                    ->color(
                        fn (InvoiceStatus|string|null $state): string|array|null => $state instanceof InvoiceStatus
                            ? $state->getColor()
                            : InvoiceStatus::tryFrom((string) $state)?->getColor(),
                    )
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label(__('Total'))
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('latestPayment.status')
                    ->label(__('Payment Status'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'pending' => __('Pending Payment'),
                        'paid' => __('Paid'),
                        'failed' => __('Failed'),
                        default => __('Unpaid'),
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('latestPayment.order_id')
                    ->label(__('Order ID'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('latestPayment.midtrans_payment_type')
                    ->label(__('Payment method'))
                    ->toggleable(),
                TextColumn::make('issued_at')
                    ->translateLabel()
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(
                    collect(InvoiceStatus::cases())->mapWithKeys(
                        fn (InvoiceStatus $case): array => [
                            $case->value => (string) $case->getLabel(),
                        ],
                    ),
                ),
            ])
            ->defaultSort('issued_at', 'desc')
            ->recordActions([
                Action::make('download')
                    ->label(__('Download PDF'))
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn (Invoice $record): string => route('invoices.download', $record))
                    ->openUrlInNewTab(),
                ViewAction::make(),
            ]);
    }
}
