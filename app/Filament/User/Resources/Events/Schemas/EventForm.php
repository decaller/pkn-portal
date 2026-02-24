<?php

namespace App\Filament\User\Resources\Events\Schemas;

use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return \App\Filament\Resources\Events\Schemas\EventForm::configure(
            $schema,
        );
    }
}
