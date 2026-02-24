<?php

namespace App\Filament\User\Resources\EventRegistrations\Schemas;

use App\Enums\InvoiceStatus;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EventRegistrationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make("Registration details")
                ->schema([
                    TextEntry::make("event.title")->label("Event"),
                    TextEntry::make("organization.name")
                        ->label("Organization")
                        ->placeholder("Personal registration"),
                    TextEntry::make("status"),
                    TextEntry::make("payment_status"),
                    TextEntry::make("total_amount")->money("IDR"),
                    TextEntry::make("notes")->placeholder("-"),
                    TextEntry::make("created_at")->dateTime(),
                ])
                ->columns(2),
            Section::make("Invoices")
                ->schema([
                    RepeatableEntry::make("invoices")
                        ->label("")
                        ->schema([
                            TextEntry::make("invoice_number")
                                ->label("Invoice #")
                                ->weight("bold"),
                            TextEntry::make("version")
                                ->label("Version")
                                ->badge(),
                            TextEntry::make("status")
                                ->badge()
                                ->formatStateUsing(
                                    fn(
                                        InvoiceStatus|string|null $state,
                                    ): string => $state instanceof InvoiceStatus
                                        ? $state->getLabel()
                                        : InvoiceStatus::tryFrom(
                                                (string) $state,
                                            )?->getLabel() ?? "-",
                                )
                                ->color(
                                    fn(
                                        InvoiceStatus|string|null $state,
                                    ): string|array|null => $state instanceof
                                    InvoiceStatus
                                        ? $state->getColor()
                                        : InvoiceStatus::tryFrom(
                                            (string) $state,
                                        )?->getColor(),
                                ),
                            TextEntry::make("total_amount")
                                ->label("Total")
                                ->money("IDR"),
                            TextEntry::make("issued_at")
                                ->label("Issued")
                                ->dateTime(),
                        ])
                        ->columns(5)
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ]);
    }
}
