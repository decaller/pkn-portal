<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('event_id')
                    ->relationship('event', 'title')
                    ->required()
                    ->searchable()
                    ->preload(),
                \Filament\Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Registered User (Optional)')
                    ->searchable()
                    ->preload(),
                \Filament\Forms\Components\TextInput::make('guest_name')
                    ->label('Guest Name')
                    ->placeholder('Leave blank if User is selected')
                    ->maxLength(255),
                \Filament\Forms\Components\ToggleButtons::make('rating')
                    ->options([
                        1 => '1 Star',
                        2 => '2 Stars',
                        3 => '3 Stars',
                        4 => '4 Stars',
                        5 => '5 Stars',
                    ])
                    ->inline()
                    ->required()
                    ->default(5),
                Toggle::make('is_approved')
                    ->label('Approved & Published')
                    ->required(),
                Textarea::make('content')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
