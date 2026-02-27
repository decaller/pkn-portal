<?php

namespace App\Filament\Resources\Events\Schemas;

use App\Enums\EventType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Event Identity')
                ->description('Basic information and folder naming.')
                ->schema([
                    TextInput::make('title')
                        ->required()
                        ->live(onBlur: true) // Updates the slug when you click away
                        ->afterStateUpdated(
                            fn (
                                string $operation,
                                $state,
                                $set,
                            ) => $operation === 'create'
                                ? $set('slug', Str::slug($state))
                                : null,
                        ),

                    TextInput::make('slug')
                        ->disabled() // Keep it disabled so users don't break the folder link
                        ->dehydrated() // Ensures it still gets saved to the DB
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->label('Folder Name (Auto-generated)'),

                    DatePicker::make('event_date')
                        ->required()
                        ->native(false)
                        ->live(onBlur: true)
                        ->minDate(
                            fn (Get $get, string $operation): ?\Illuminate\Support\Carbon => $get('allow_registration') && $operation === 'create'
                                ? now()->startOfDay()
                                : null,
                        )
                        ->rules(
                            fn (Get $get, string $operation): array => $get('allow_registration') && $operation === 'create'
                                ? ['after_or_equal:today']
                                : [],
                        )
                        ->displayFormat('d/m/Y'),
                        
                    TextInput::make('duration_days')
                        ->label('Duration (Days)')
                        ->numeric()
                        ->minValue(1)
                        ->placeholder('Optional total days'),

                    Select::make('event_type')
                        ->label('Event Type')
                        ->options(EventType::class)
                        ->default(EventType::Offline->value)
                        ->required()
                        ->native(false),

                    Toggle::make('allow_registration')
                        ->label('Allow user registration')
                        ->helperText(
                            'When enabled, event date cannot be backdated.',
                        )
                        ->default(false),

                    TextInput::make('max_capacity')
                        ->label('Max Capacity')
                        ->numeric()
                        ->minValue(1)
                        ->placeholder('Leave blank for unlimited spots')
                        ->helperText('Maximum number of participants allowed to register.'),

                    TextInput::make('place')
                        ->label('Event Place/Location')
                        ->live(onBlur: true)
                        ->maxLength(255),
                        
                    TextInput::make('city')
                        ->label('City')
                        ->maxLength(255),
                        
                    TextInput::make('province')
                        ->label('Province')
                        ->maxLength(255),
                        
                    TextInput::make('nation')
                        ->label('Nation')
                        ->maxLength(255),
                        
                    TextInput::make('google_maps_url')
                        ->label('Google Maps URL')
                        ->url()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    \Filament\Forms\Components\Select::make('survey_template_id')
                        ->label('Survey Template')
                        ->relationship('surveyTemplate', 'name')
                        ->preload()
                        ->placeholder('Select a survey template')
                        ->nullable(),
                        
                    \Filament\Forms\Components\TagsInput::make('tags')
                        ->placeholder('Add tags...')
                        ->suggestions(
                            fn () => \App\Models\Event::pluck('tags')
                                ->flatten()
                                ->filter()
                                ->unique()
                                ->values()
                                ->toArray()
                        )
                        ->columnSpanFull(),
                ])
                ->columnSpan(2),

            Section::make('Promotional Image')
                ->schema([
                    FileUpload::make('cover_image')
                        ->image()
                        ->disk('public')
                        ->imageResizeMode('cover')
                        ->imageResizeTargetWidth('1200')
                        ->directory('event-covers'), // Cover image goes in a generic folder
                ])
                ->columnSpanFull(),

            Section::make('Additional Documents')
                ->schema([
                    FileUpload::make('proposal')
                        ->label('Event Proposal')
                        ->disk('public')
                        ->acceptedFileTypes(['application/pdf', 'image/*'])
                        ->directory('event-proposals')
                        ->downloadable()
                        ->openable(),
                        
                    FileUpload::make('documentation')
                        ->label('Event Documentation')
                        ->disk('public')
                        ->multiple()
                        ->directory('event-documentation')
                        ->reorderable()
                        ->downloadable()
                        ->openable()
                        ->panelLayout('grid'),
                ])
                ->columnSpanFull(),

            Section::make('Registration Packages')
                ->description('Define package types and price per participant.')
                ->schema([
                    Repeater::make('registration_packages')
                        ->schema([
                            TextInput::make('name')
                                ->label('Package name')
                                ->required()
                                ->maxLength(100)
                                ->placeholder('e.g. Regular, VIP, Group'),
                            TextInput::make('price')
                                ->label('Price / participant')
                                ->numeric()
                                ->prefix('IDR')
                                ->required()
                                ->minValue(0),
                            TextInput::make('max_quota')
                                ->label('Max Quota')
                                ->numeric()
                                ->minValue(1)
                                ->placeholder('Optional limit'),
                        ])
                        ->columns(3)
                        ->defaultItems(0)
                        ->addActionLabel('Add package')
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),

            Section::make('Description')
                ->schema([RichEditor::make('description')->columnSpanFull()])
                ->columnSpanFull(),
            Section::make('Event Rundown')
                ->description('Add sessions, speakers, and materials.')
                ->schema([
                    \Filament\Forms\Components\Builder::make('rundown') // Matches the 'rundown' column in DB
                        ->blocks([
                            \Filament\Forms\Components\Builder\Block::make('simple')
                                ->label('Simple Session')
                                ->schema([
                                    TextInput::make('title')->required()->columnSpanFull(),
                                    DatePicker::make('date')
                                        ->default(fn (Get $get) => $get('../../event_date'))
                                        ->required(),
                                    TextInput::make('place')
                                        ->default(fn (Get $get) => $get('../../place')),
                                    TextInput::make('start_time')->required(),
                                    TextInput::make('end_time')->required(),
                                    
                                ])->columns(2),

                            \Filament\Forms\Components\Builder\Block::make('advanced')
                                ->label('Advanced Session')
                                ->schema([
                                    TextInput::make('title')->required()->columnSpanFull(),
                                    DatePicker::make('date')
                                        ->default(fn (Get $get) => $get('../../event_date'))
                                        ->required(),
                                    TextInput::make('place')
                                        ->default(fn (Get $get) => $get('../../place')),
                                    TextInput::make('start_time')->required(),
                                    TextInput::make('end_time')->required(),
                                    
                                    
                                    TextInput::make('speaker')
                                        ->placeholder('e.g. Speaker Name')
                                        ->columnSpanFull(),
                                    
                                    RichEditor::make('description')
                                        ->toolbarButtons([
                                            'bold',
                                            'italic',
                                            'link',
                                            'bulletList',
                                        ])
                                        ->columnSpanFull(),
                                    
                                    FileUpload::make('session_files')
                                        ->label('Materials')
                                        ->multiple()
                                        ->disk('public')
                                        ->visibility('public')
                                        ->openable()
                                        ->directory(fn (Get $get) => 'events/'.($get('../../slug') ?? $get('../../../slug') ?? $get('../../../../slug') ?? 'draft').'/sessions')
                                        ->preserveFilenames()
                                        ->reorderable()
                                        ->downloadable()
                                        ->columnSpanFull(),
                                    
                                    Repeater::make('links')
                                        ->label('External Resources')
                                        ->schema([
                                            TextInput::make('url')
                                                ->label('URL')
                                                ->url()
                                                ->required(),
                                            TextInput::make('label')
                                                ->label('Label')
                                                ->placeholder('e.g. Watch on YouTube')
                                                ->required(),
                                        ])
                                        ->columns(2)
                                        ->defaultItems(0)
                                        ->addActionLabel('Add Link')
                                        ->columnSpanFull(),
                                ])->columns(2),
                        ])
                        ->columnSpanFull()
                        ->addActionLabel('Add Session Block'),
                ])
                ->columnSpanFull(),
        ]);
    }
}
