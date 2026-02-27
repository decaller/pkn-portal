<?php

namespace App\Filament\Admin\Resources\SurveyTemplates\Pages;

use App\Filament\Admin\Resources\SurveyTemplates\SurveyTemplateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSurveyTemplates extends ListRecords
{
    protected static string $resource = SurveyTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
