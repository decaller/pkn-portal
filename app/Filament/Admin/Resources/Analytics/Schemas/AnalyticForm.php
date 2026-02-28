<?php

namespace App\Filament\Admin\Resources\Analytics\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AnalyticForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->numeric(),
                TextInput::make('trackable_type')
                    ->required(),
                TextInput::make('trackable_id')
                    ->required()
                    ->numeric(),
                TextInput::make('action')
                    ->required(),
                TextInput::make('platform'),
            ]);
    }
}
