<?php

namespace App\Filament\Admin\Resources\Organizations\Pages;

use App\Filament\Admin\Resources\Organizations\OrganizationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrganization extends CreateRecord
{
    protected static string $resource = OrganizationResource::class;

    protected function afterCreate(): void
    {
        $adminId = $this->record->admin_user_id;

        $this->record->users()->syncWithoutDetaching([
            $adminId => ['role' => 'admin'],
        ]);
    }
}
