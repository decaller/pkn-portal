<?php

namespace App\Filament\Admin\Resources\Events\Schemas;

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
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\TagsInput;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('Event Identity'))
                ->description(__('Basic information and folder naming.'))
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
                        ->label(__('Folder Name (Auto-generated)')),

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
                        ->label(__('Duration (Days)'))
                        ->numeric()
                        ->minValue(1)
                        ->placeholder(__('Optional total days')),

                    Select::make('event_type')
                        ->label(__('Event Type'))
                        ->options(EventType::class)
                        ->default(EventType::Offline->value)
                        ->required()
                        ->native(false),

                    Toggle::make('allow_registration')
                        ->label(__('Allow user registration'))
                        ->helperText(
                            __('When enabled, event date cannot be backdated.'),
                        )
                        ->default(false),

                    TextInput::make('max_capacity')
                        ->label(__('Max Capacity'))
                        ->numeric()
                        ->minValue(1)
                        ->placeholder(__('Leave blank for unlimited spots'))
                        ->helperText(__('Maximum number of participants allowed to register.')),

                    TextInput::make('place')
                        ->label(__('Event Place/Location'))
                        ->live(onBlur: true)
                        ->maxLength(255),

                    TextInput::make('city')
                        ->label(__('City'))
                        ->maxLength(255),

                    TextInput::make('province')
                        ->label(__('Province'))
                        ->maxLength(255),

                    TextInput::make('nation')
                        ->label(__('Nation'))
                        ->maxLength(255),

                    TextInput::make('google_maps_url')
                        ->label(__('Google Maps URL'))
                        ->url()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Select::make('survey_template_id')
                        ->label(__('Survey Template'))
                        ->relationship('surveyTemplate', 'name')
                        ->preload()
                        ->placeholder(__('Select a survey template'))
                        ->nullable(),

                    TagsInput::make('tags')
                        ->placeholder(__('Add tags...'))
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

            Section::make(__('Promotional Image'))
                ->schema([
                    FileUpload::make('cover_image')
                        ->image()
                        ->disk('public')
                        ->imageResizeMode('cover')
                        ->imageResizeTargetWidth('1200')
                        ->directory('event-covers'), // Cover image goes in a generic folder
                ])
                ->columnSpanFull(),

            Section::make(__('Additional Documents'))
                ->schema([
                    FileUpload::make('proposal')
                        ->label(__('Event Proposal'))
                        ->disk('public')
                        ->acceptedFileTypes(['application/pdf', 'image/*'])
                        ->directory('event-proposals')
                        ->downloadable()
                        ->openable(),

                    FileUpload::make('documentation')
                        ->label(__('Event Documentation'))
                        ->disk('public')
                        ->multiple()
                        ->directory('event-documentation')
                        ->reorderable()
                        ->downloadable()
                        ->openable()
                        ->panelLayout('grid'),
                ])
                ->columnSpanFull(),

            Section::make(__('Registration Packages'))
                ->description(__('Define package types and price per participant.'))
                ->schema([
                    Repeater::make('registration_packages')
                        ->schema([
                            TextInput::make('name')
                                ->label(__('Package name'))
                                ->required()
                                ->maxLength(100)
                                ->placeholder(__('e.g. Regular, VIP, Group')),
                            TextInput::make('price')
                                ->label(__('Price / participant'))
                                ->numeric()
                                ->prefix('IDR')
                                ->required()
                                ->minValue(0),
                            TextInput::make('max_quota')
                                ->label(__('Max Quota'))
                                ->numeric()
                                ->minValue(1)
                                ->placeholder(__('Optional limit')),
                        ])
                        ->columns(3)
                        ->defaultItems(0)
                        ->addActionLabel(__('Add package'))
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),

            Section::make(__('Description'))
                ->schema([RichEditor::make('description')->columnSpanFull()])
                ->columnSpanFull(),
            Section::make(__('Event Rundown'))
                ->description(__('Add sessions, speakers, and materials.'))
                ->schema([
                    Builder::make('rundown') // Matches the 'rundown' column in DB
                        ->blocks([
                            Block::make('simple')
                                ->label(__('Simple Session'))
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

                            Block::make('advanced')
                                ->label(__('Advanced Session'))
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
                                        ->placeholder(__('e.g. Speaker Name'))
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
                                        ->label(__('Materials'))
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
                                        ->label(__('External Resources'))
                                        ->schema([
                                            TextInput::make('url')
                                                ->label('URL')
                                                ->url()
                                                ->required(),
                                            TextInput::make('label')
                                                ->label(__('Label'))
                                                ->placeholder(__('e.g. Watch on YouTube'))
                                                ->required(),
                                        ])
                                        ->columns(2)
                                        ->defaultItems(0)
                                        ->addActionLabel(__('Add Link'))
                                        ->columnSpanFull(),
                                ])->columns(2),
                        ])
                        ->columnSpanFull()
                        ->addActionLabel(__('Add Session Block')),
                ])
                ->columnSpanFull(),
        ]);
    }
}
