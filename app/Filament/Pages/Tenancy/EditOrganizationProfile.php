<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Organization;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Schemas\Schema;

class EditOrganizationProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return __('Organization profile');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label(__('Name'))->required()->maxLength(255),
            TextInput::make('slug')
                ->label(__('Slug'))
                ->required()
                ->alphaDash()
                ->maxLength(255)
                ->unique(Organization::class, 'slug', ignoreRecord: true),
            FileUpload::make('logo')
                ->label(__('Logo'))
                ->image()
                ->imageResizeMode('cover')
                ->imageResizeTargetWidth('1200')
                ->disk('public')
                ->visibility('public')
                ->directory('organization-logos')
                ->imageEditor(),
        ]);
    }
}
