<?php

namespace App\Filament\Admin\Resources\EventRegistrations\Tables;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Filament\Admin\Resources\EventRegistrations\EventRegistrationResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EventRegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')->label(__('Status'))->badge(),
                TextColumn::make('payment_status')->label(__('Payment Status'))->badge(),
                TextColumn::make('event.title')
                    ->label(__('Event'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('booker.name')->label(__('Booker'))->searchable(),
                TextColumn::make('organization.name')
                    ->label(__('Organization'))
                    ->placeholder(__('Personal')),
                // TextColumn::make("package_name")
                //     ->label("Package")
                //     ->placeholder("-"),
                TextColumn::make('participants_count')
                    ->label(__('Participants'))
                    ->badge(),
                // TextColumn::make("participant_count")->label("Qty")->badge(),
                // TextColumn::make("unit_price")
                //     ->money("IDR")
                //     ->label("Unit price"),

                TextColumn::make('total_amount')->money('IDR')->label(__('Total'))->sortable(),
                TextColumn::make('updated_at')->label(__('Updated'))->since(),
            ])
            ->filters([
                SelectFilter::make('status')->options(
                    collect(RegistrationStatus::cases())->mapWithKeys(
                        fn ($case) => [
                            $case->value => ucfirst(
                                str_replace('_', ' ', $case->value),
                            ),
                        ],
                    ),
                ),
                SelectFilter::make('payment_status')->options(
                    collect(PaymentStatus::cases())->mapWithKeys(
                        fn ($case) => [$case->value => ucfirst($case->value)],
                    ),
                ),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('activities')
                    ->label(__('Activities'))
                    ->icon('heroicon-o-clock')
                    ->url(fn ($record) => EventRegistrationResource::getUrl('activities', ['record' => $record])),
            ]);
    }
}
