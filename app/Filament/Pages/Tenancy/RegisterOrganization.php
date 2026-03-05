<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Organization;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegisterOrganization extends RegisterTenant
{
    public static function getLabel(): string
    {
        return __('Join / Create organization');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Radio::make('registration_type')
                ->label(__('Do you want to create a new organization or join an existing one?'))
                ->options([
                    'new' => __('Create a new organization'),
                    'existing' => __('Join an existing organization'),
                ])
                ->default('new')
                ->inline()
                ->live(),
            Select::make('existing_organization_id')
                ->label(__('Select Organization'))
                ->options(fn () => Organization::query()
                    ->whereRaw('LOWER(slug) NOT LIKE ?', ['%pkn%'])
                    ->where('name', 'not like', 'Personal - %')
                    ->orderBy('name')
                    ->pluck('name', 'id')
                )
                ->searchable()
                ->helperText(__('Search for your organization by its official name.'))
                ->required(fn (Get $get) => $get('registration_type') === 'existing')
                ->visible(fn (Get $get) => $get('registration_type') === 'existing'),
            TextInput::make('name')
                ->label(__('Name'))
                ->required(fn (Get $get) => $get('registration_type') === 'new')
                ->visible(fn (Get $get) => $get('registration_type') === 'new')
                ->helperText(__('Enter the official name of your organization.'))
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(
                    fn ($state, $set) => $set(
                        'slug',
                        Str::slug((string) $state),
                    ),
                ),
            TextInput::make('slug')
                ->label(__('Slug'))
                ->required(fn (Get $get) => $get('registration_type') === 'new')
                ->visible(fn (Get $get) => $get('registration_type') === 'new')
                ->helperText(__('A unique, URL-friendly name for your organization (e.g., \'acme-corp\').'))
                ->alphaDash()
                ->maxLength(255)
                ->unique(Organization::class, 'slug'),
            FileUpload::make('logo')
                ->label(__('Logo'))
                ->image()
                ->imageResizeMode('cover')
                ->imageResizeTargetWidth('1200')
                ->disk('public')
                ->visibility('public')
                ->directory('organization-logos')
                ->helperText(__('Upload a square image (JPG, PNG). Recommended size: 256x256 pixels. Max 2MB.'))
                ->imageEditor()
                ->visible(
                    fn (Get $get): bool => $get('registration_type') === 'new',
                ),
        ]);
    }

    protected function handleRegistration(array $data): Model
    {
        $registrationType = $data['registration_type'] ?? 'new';

        if ($registrationType === 'existing') {
            $organization = Organization::findOrFail($data['existing_organization_id']);
            $organization->users()->syncWithoutDetaching([
                Auth::id() => ['role' => 'user'],
            ]);

            return $organization;
        }

        $organization = Organization::create([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'logo' => $data['logo'] ?? null,
            'admin_user_id' => Auth::id(),
        ]);

        $organization->users()->syncWithoutDetaching([
            Auth::id() => ['role' => 'admin'],
        ]);

        return $organization;
    }
}
