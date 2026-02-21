<?php

namespace App\Filament\Resources\Organizations\Schemas;

use App\Models\Organization;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class OrganizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make("Organization details")
                ->schema([
                    TextInput::make("name")
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (
                            ?string $state,
                            $set,
                            ?string $operation,
                        ): void {
                            if ($operation !== "create") {
                                return;
                            }

                            $set("slug", Str::slug((string) $state));
                        }),
                    TextInput::make("slug")
                        ->required()
                        ->alphaDash()
                        ->maxLength(255)
                        ->unique(
                            Organization::class,
                            "slug",
                            ignoreRecord: true,
                        ),
                    FileUpload::make("logo")
                        ->image()
                        ->disk("public")
                        ->visibility("public")
                        ->directory("organization-logos")
                        ->imageEditor(),
                    Select::make("admin_user_id")
                        ->label("Organization admin")
                        ->relationship("admin", "name")
                        ->searchable()
                        ->preload()
                        ->required(),
                ])
                ->columns(2),
            Section::make("Members")
                ->description(
                    "Users attached to this organization. Selected admin will always be included as admin.",
                )
                ->schema([
                    Select::make("users")
                        ->multiple()
                        ->relationship("users", "name")
                        ->getOptionLabelFromRecordUsing(
                            fn(User $record): string => $record->name .
                                " (" .
                                ($record->phone_number ?: "no phone") .
                                ")",
                        )
                        ->searchable(["name", "phone_number", "email"])
                        ->preload(),
                ]),
        ]);
    }
}
