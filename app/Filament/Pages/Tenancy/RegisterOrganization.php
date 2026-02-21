<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Organization;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegisterOrganization extends RegisterTenant
{
    public static function getLabel(): string
    {
        return "Create organization";
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make("name")
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(
                    fn($state, $set) => $set(
                        "slug",
                        Str::slug((string) $state),
                    ),
                ),
            TextInput::make("slug")
                ->required()
                ->alphaDash()
                ->maxLength(255)
                ->unique(Organization::class, "slug"),
            FileUpload::make("logo")
                ->image()
                ->disk("public")
                ->visibility("public")
                ->directory("organization-logos")
                ->imageEditor(),
        ]);
    }

    protected function handleRegistration(array $data): Model
    {
        $organization = Organization::create([
            "name" => $data["name"],
            "slug" => $data["slug"],
            "logo" => $data["logo"] ?? null,
            "admin_user_id" => Auth::id(),
        ]);

        $organization->users()->syncWithoutDetaching([
            Auth::id() => ["role" => "admin"],
        ]);

        return $organization;
    }
}
