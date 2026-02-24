<?php

namespace App\Filament\Resources\Events\Schemas;

use App\Enums\EventType;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;
use Illuminate\Support\Str;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\Repeater;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make("Event Identity")
                ->description("Basic information and folder naming.")
                ->schema([
                    TextInput::make("title")
                        ->required()
                        ->live(onBlur: true) // Updates the slug when you click away
                        ->afterStateUpdated(
                            fn(
                                string $operation,
                                $state,
                                $set,
                            ) => $operation === "create"
                                ? $set("slug", Str::slug($state))
                                : null,
                        ),

                    TextInput::make("slug")
                        ->disabled() // Keep it disabled so users don't break the folder link
                        ->dehydrated() // Ensures it still gets saved to the DB
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->label("Folder Name (Auto-generated)"),

                    DatePicker::make("event_date")
                        ->required()
                        ->native(false)
                        ->minDate(
                            fn(Get $get): ?\Illuminate\Support\Carbon => $get(
                                "allow_registration",
                            )
                                ? now()->startOfDay()
                                : null,
                        )
                        ->rules(
                            fn(Get $get): array => $get("allow_registration")
                                ? ["after_or_equal:today"]
                                : [],
                        )
                        ->displayFormat("d/m/Y"),

                    Select::make("event_type")
                        ->label("Event Type")
                        ->options(EventType::class)
                        ->default(EventType::Offline->value)
                        ->required()
                        ->native(false),

                    Toggle::make("allow_registration")
                        ->label("Allow user registration")
                        ->helperText(
                            "When enabled, event date cannot be backdated.",
                        )
                        ->default(false),
                ])
                ->columnSpan(2),

            Section::make("Media")
                ->schema([
                    FileUpload::make("cover_image")
                        ->image()
                        ->directory("event-covers"), // Cover image goes in a generic folder
                ])
                ->columnSpanFull(),

            Section::make("Registration Packages")
                ->description("Define package types and price per participant.")
                ->schema([
                    Repeater::make("registration_packages")
                        ->schema([
                            TextInput::make("name")
                                ->label("Package name")
                                ->required()
                                ->maxLength(100)
                                ->placeholder("e.g. Regular, VIP, Group"),
                            TextInput::make("price")
                                ->label("Price / participant")
                                ->numeric()
                                ->prefix("IDR")
                                ->required()
                                ->minValue(0),
                        ])
                        ->columns(2)
                        ->defaultItems(0)
                        ->addActionLabel("Add package")
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),

            Section::make("Description")
                ->schema([RichEditor::make("description")->columnSpanFull()])
                ->columnSpanFull(),
            Section::make("Event Rundown")
                ->description("Add sessions, speakers, and materials.")
                ->schema([
                    Repeater::make("rundown") // Matches the 'rundown' column in DB
                        ->schema([
                            // Session Title
                            TextInput::make("title")
                                ->required()
                                ->placeholder("e.g. Opening Ceremony")
                                ->columnSpanFull()
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (
                                    $state,
                                    $set,
                                    $get,
                                ) {
                                    // 1. Get the main Event Title from the parent form
                                    $eventTitle =
                                        $get("../../title") ?? "event";

                                    // 2. Combine Event Title + Session Title + Random Suffix
                                    $combinedSlug =
                                        Str::slug($eventTitle) .
                                        "-" .
                                        Str::slug($state) .
                                        "-" .
                                        Str::lower(Str::random(4));

                                    // 3. Set the slug field in the current repeater row
                                    $set("slug", $combinedSlug);
                                }),
                            TextInput::make("slug")
                                ->disabled() // Keep it disabled so users don't break the folder link
                                ->dehydrated() // Ensures it still gets saved to the DB
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->label("Session ID (Auto-generated)"),

                            TextInput::make("speaker")
                                ->required()
                                ->placeholder("e.g. Speaker Name")
                                ->columnSpanFull(),

                            // Session Details
                            RichEditor::make("description")
                                ->toolbarButtons([
                                    "bold",
                                    "italic",
                                    "link",
                                    "bulletList",
                                ])
                                ->columnSpanFull(),

                            // Session Files (PDFs/PPTs)
                            FileUpload::make("session_files")
                                ->label("Materials")
                                ->multiple()
                                ->disk("public")
                                ->visibility("public")
                                ->openable()
                                // Save to: events/graduation-2026/sessions/
                                ->directory(
                                    fn($get) => "events/" .
                                        $get("../../slug") .
                                        "/sessions",
                                )
                                ->preserveFilenames()
                                ->downloadable(),

                            // External Link
                            Repeater::make("links")
                                ->label("External Resources")
                                ->schema([
                                    TextInput::make("url")
                                        ->label("URL")
                                        ->url()
                                        ->required()
                                        ->prefix("https://"),

                                    TextInput::make("label")
                                        ->label("Label")
                                        ->placeholder("e.g. Watch on YouTube")
                                        ->required(),
                                ])
                                ->columns(2) // Make the inner items sit in a row (Compact!)
                                ->defaultItems(0) // Start empty
                                ->addActionLabel("Add Link"),
                        ])
                        ->itemLabel(
                            fn(array $state): ?string => $state["title"] ??
                                null,
                        ) // Show title on the collapsed bar
                        ->collapsible() // Allow collapsing to save space
                        ->cloneable() // Allow duplicating sessions easily
                        ->columnSpanFull()
                        ->addActionLabel("Add Session"),
                ])
                ->columnSpanFull(),
        ]);
    }
}
