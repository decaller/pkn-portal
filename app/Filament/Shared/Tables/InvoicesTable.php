<?php

namespace App\Filament\Shared\Tables;

use App\Models\Invoice;
use App\Services\Payments\InvoicePaymentService;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Throwable;

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
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('pay_now')
                    ->label(fn (Invoice $record): string => $record->hasActivePaymentAttempt()
                        ? __('Continue Payment')
                        : __('Pay Now'))
                    ->icon('heroicon-o-credit-card')
                    ->color('success')
                    ->visible(fn (Invoice $record): bool => $record->canStartGatewayPayment())
                    ->action(function (Invoice $record, $livewire): void {
                        try {
                            $payment = app(InvoicePaymentService::class)->createOrReuseSnapPayment($record);

                            Notification::make()
                                ->title(__('Payment session created'))
                                ->success()
                                ->send();

                            $livewire->dispatch('open-midtrans-snap', token: $payment->snap_token);
                        } catch (Throwable) {
                            Notification::make()
                                ->title(__('Unable to start payment'))
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('download')
                    ->label(__('Download PDF'))
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn (Invoice $record): string => route('invoices.download', $record))
                    ->openUrlInNewTab(),
                ViewAction::make(),
            ]);
    }
}
