<?php

namespace App\Filament\Admin\Resources\Invoices\Tables;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
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
                Action::make('verify_payment')
                    ->label(__('Verify'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (\App\Models\Invoice $record): bool => $record->registration && $record->registration->payment_status === PaymentStatus::Submitted)
                    ->requiresConfirmation()
                    ->modalHeading(__('Verify Payment'))
                    ->modalDescription(__('Are you sure you want to completely verify this invoice payment? The registration will be marked as paid.'))
                    ->modalSubmitActionLabel(__('Yes, mark as Verified'))
                    ->action(function (\App\Models\Invoice $record) {
                        $registration = $record->registration;
                        if ($registration) {
                            $registration->verifyPayment(auth()->user());
                        }

                        \Filament\Notifications\Notification::make()
                            ->title(__('Payment Verified'))
                            ->success()
                            ->send();
                    }),
                Action::make('download')
                    ->label(__('Download PDF'))
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn (\App\Models\Invoice $record): string => route('invoices.download', $record))
                    ->openUrlInNewTab(),
                ViewAction::make(),
            ]);
    }
}
