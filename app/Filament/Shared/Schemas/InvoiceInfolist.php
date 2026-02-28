<?php

namespace App\Filament\Shared\Schemas;

use App\Enums\InvoiceStatus;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InvoiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('Invoice'))
                ->schema([
                    TextEntry::make('invoice_number')->label(__('Invoice #')),
                    TextEntry::make('version')->badge(),
                    TextEntry::make('status')
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
                        ),
                    TextEntry::make('currency'),
                    TextEntry::make('issued_at')->dateTime(),
                    TextEntry::make('due_at')->date(),
                    TextEntry::make('total_amount')->money('IDR'),
                    TextEntry::make('void_reason')
                        ->placeholder('-')
                        ->columnSpanFull(),
                ])
                ->columns(3),

            Section::make(__('Registration Snapshot'))
                ->schema([
                    TextEntry::make('registration.id')->label(__('Registration ID')),
                    TextEntry::make('event_snapshot.title')->label(__('Event')),
                    TextEntry::make('event_snapshot.date')
                        ->label(__('Event date'))
                        ->placeholder('-'),
                    TextEntry::make('organization_snapshot.name')
                        ->label(__('Organization'))
                        ->placeholder(__('Personal')),
                    TextEntry::make('booker_snapshot.name')->label(__('Booker')),
                    TextEntry::make('booker_snapshot.email')
                        ->label(__('Booker email'))
                        ->placeholder('-'),
                ])
                ->columns(2),

            Section::make(__('Line Items'))
                ->schema([
                    RepeatableEntry::make('items')
                        ->schema([
                            TextEntry::make('package_name')
                                ->label(__('Package'))
                                ->weight('bold'),
                            TextEntry::make('participant_count')
                                ->label(__('Qty'))
                                ->badge(),
                            TextEntry::make('unit_price')
                                ->label(__('Unit'))
                                ->money('IDR'),
                            TextEntry::make('line_total')
                                ->label(__('Line total'))
                                ->money('IDR'),
                        ])
                        ->columns(4)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
