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
            Section::make("Invoice")
                ->schema([
                    TextEntry::make("invoice_number")->label("Invoice #"),
                    TextEntry::make("version")->badge(),
                    TextEntry::make("status")
                        ->badge()
                        ->formatStateUsing(
                            fn(InvoiceStatus|string|null $state): string => $state instanceof InvoiceStatus
                                ? $state->getLabel()
                                : InvoiceStatus::tryFrom((string) $state)?->getLabel() ?? "-",
                        )
                        ->color(
                            fn(InvoiceStatus|string|null $state): string|array|null => $state instanceof InvoiceStatus
                                ? $state->getColor()
                                : InvoiceStatus::tryFrom((string) $state)?->getColor(),
                        ),
                    TextEntry::make("currency"),
                    TextEntry::make("issued_at")->dateTime(),
                    TextEntry::make("due_at")->date(),
                    TextEntry::make("total_amount")->money("IDR"),
                    TextEntry::make("void_reason")
                        ->placeholder("-")
                        ->columnSpanFull(),
                ])
                ->columns(3),

            Section::make("Registration Snapshot")
                ->schema([
                    TextEntry::make("registration.id")->label("Registration ID"),
                    TextEntry::make("event_snapshot.title")->label("Event"),
                    TextEntry::make("event_snapshot.date")
                        ->label("Event date")
                        ->placeholder("-"),
                    TextEntry::make("organization_snapshot.name")
                        ->label("Organization")
                        ->placeholder("Personal"),
                    TextEntry::make("booker_snapshot.name")->label("Booker"),
                    TextEntry::make("booker_snapshot.email")
                        ->label("Booker email")
                        ->placeholder("-"),
                ])
                ->columns(2),

            Section::make("Line Items")
                ->schema([
                    RepeatableEntry::make("items")
                        ->schema([
                            TextEntry::make("package_name")
                                ->label("Package")
                                ->weight("bold"),
                            TextEntry::make("participant_count")
                                ->label("Qty")
                                ->badge(),
                            TextEntry::make("unit_price")
                                ->label("Unit")
                                ->money("IDR"),
                            TextEntry::make("line_total")
                                ->label("Line total")
                                ->money("IDR"),
                        ])
                        ->columns(4)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
