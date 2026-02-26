<?php

namespace App\Filament\Resources\Invoices\Tables;

use App\Enums\InvoiceStatus;
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
                    ->label('Invoice #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('registration.event.title')
                    ->label('Event')
                    ->searchable(),
                TextColumn::make('registration.organization.name')
                    ->label('Organization')
                    ->placeholder('Personal'),
                TextColumn::make('registration.booker.name')
                    ->label('Booker')
                    ->searchable(),
                TextColumn::make('version')
                    ->label('Version')
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
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
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('issued_at')
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
                    ->label('Verify')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (\App\Models\Invoice $record): bool => $record->registration && $record->registration->payment_status === \App\Enums\PaymentStatus::Submitted)
                    ->requiresConfirmation()
                    ->modalHeading('Verify Payment')
                    ->modalDescription('Are you sure you want to completely verify this invoice payment? The registration will be marked as paid.')
                    ->modalSubmitActionLabel('Yes, mark as Verified')
                    ->action(function (\App\Models\Invoice $record) {
                        $registration = $record->registration;
                        if ($registration) {
                            $registration->verifyPayment(auth()->user());
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Payment Verified')
                            ->success()
                            ->send();
                    }),
                Action::make('download')
                    ->label('Download PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn (\App\Models\Invoice $record): string => route('invoices.download', $record))
                    ->openUrlInNewTab(),
                ViewAction::make(),
            ]);
    }
}
