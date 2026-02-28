<?php

namespace App\Filament\Admin\Resources\Testimonials\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('event_id')
                    ->relationship('event', 'title')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label(__('Registered User (Optional)'))
                    ->searchable()
                    ->preload(),
                TextInput::make('guest_name')
                    ->label(__('Guest Name'))
                    ->placeholder(__('Leave blank if User is selected'))
                    ->maxLength(255),
                ToggleButtons::make('rating')
                    ->options([
                        1 => __('1 Star'),
                        2 => __('2 Stars'),
                        3 => __('3 Stars'),
                        4 => __('4 Stars'),
                        5 => __('5 Stars'),
                    ])
                    ->inline()
                    ->required()
                    ->default(5),
                Toggle::make('is_approved')
                    ->label(__('Approved & Published'))
                    ->required(),
                Textarea::make('content')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
