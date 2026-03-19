<?php

namespace App\Filament\Shared\Schemas;

use App\Models\Event;
use Filament\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EventInfolist
{
    /**
     * @param  array{
     *     showRundownFiles?: bool,
     *     showRundownLinks?: bool,
     *     showPublicRundownCta?: bool
     * }  $options
     */
    public static function configure(Schema $schema, array $options = []): Schema
    {
        $showRundownFiles = $options['showRundownFiles'] ?? true;
        $showRundownLinks = $options['showRundownLinks'] ?? true;
        $showPublicRundownCta = $options['showPublicRundownCta'] ?? false;

        return $schema
            ->components([
                Grid::make(['default' => 1, 'md' => 2])->schema([
                    Group::make([
                        Section::make(__('Event Guidelines'))
                            ->schema([
                                TextEntry::make('title')->weight('bold')->size('lg'),
                                TextEntry::make('description')->markdown()->prose(),
                                TextEntry::make('event_date')->date('d M Y')->placeholder('-')->icon('heroicon-m-calendar-days'),
                                TextEntry::make('event_type')
                                    ->badge()
                                    ->color('primary')
                                    ->icon('heroicon-m-tag'),
                                TextEntry::make('place')
                                    ->icon('heroicon-m-map-pin')
                                    ->visible(fn ($state) => filled($state)),
                                TextEntry::make('city')->visible(fn ($state) => filled($state)),
                                TextEntry::make('province')->visible(fn ($state) => filled($state)),
                                TextEntry::make('nation')->visible(fn ($state) => filled($state)),
                                TextEntry::make('google_maps_url')
                                    ->label(__('Map URL'))
                                    ->url(fn ($state) => $state, true)
                                    ->color('primary')
                                    ->icon('heroicon-m-map')
                                    ->visible(fn ($state) => filled($state)),
                                TextEntry::make('duration_days')
                                    ->label(__('Duration'))
                                    ->icon('heroicon-m-clock')
                                    ->formatStateUsing(fn ($state) => "{$state} ".__('Days'))
                                    ->visible(fn ($state) => filled($state)),
                                TextEntry::make('allow_registration')
                                    ->badge()
                                    ->color(fn ($state) => $state ? 'success' : 'danger')
                                    ->formatStateUsing(fn ($state) => $state ? __('Open') : __('Closed')),
                                TextEntry::make('tags')
                                    ->badge()
                                    ->separator(',')
                                    ->columnSpanFull(),
                                TextEntry::make('capacity')
                                    ->label(__('Availability'))
                                    ->icon('heroicon-m-user-group')
                                    ->state(function (Event $event) {
                                        if (is_null($event->max_capacity)) {
                                            return __('Unlimited Spots');
                                        }
                                        $available = $event->availableSpots();

                                        return $available > 0
                                            ? __(':available spots remaining out of :max', ['available' => $available, 'max' => $event->max_capacity])
                                            : __('Sold Out (:max capacity reached)', ['max' => $event->max_capacity]);
                                    })
                                    ->badge()
                                    ->color(fn (Event $event) => $event->isFull() ? 'danger' : 'success'),
                            ]),
                    ])->columnSpan(['md' => 1]),

                    Group::make([
                        Section::make(__('Promotional Image'))
                            ->schema([
                                ViewEntry::make('cover_image')
                                    ->hiddenLabel()
                                    ->view('filament.infolists.components.image-zoom'),
                                ViewEntry::make('photos')
                                    ->hiddenLabel()
                                    ->view('filament.infolists.components.image-zoom')
                                    ->viewData([
                                        'stacked' => true,
                                        'circular' => true,
                                    ]),
                            ]),
                        Section::make(__('Additional Documents'))
                            ->schema([
                                TextEntry::make('proposal')
                                    ->label(__('Event Proposal'))
                                    ->formatStateUsing(fn () => __('View Proposal'))
                                    ->url(fn ($record) => $record?->proposal ? asset('storage/'.$record->proposal) : null)
                                    ->openUrlInNewTab()
                                    ->icon('heroicon-m-document-text')
                                    ->badge()
                                    ->color('primary')
                                    ->visible(fn ($record) => filled($record?->proposal)),
                            ]),
                    ])->columnSpan(['md' => 1]),
                ])
                    ->columnSpanFull(),

                Section::make(__('Sessions & Rundown'))
                    ->headerActions([
                        Action::make('download_all_session_files')
                            ->label(__('Download All Materials'))
                            ->icon('heroicon-m-arrow-down-tray')
                            ->color('primary')
                            ->url(fn (Event $record): string => route('events.sessions.download-all', $record))
                            ->visible(function (Event $record) use ($showRundownFiles): bool {
                                if (! $showRundownFiles) {
                                    return false;
                                }

                                if (! auth()->check()) {
                                    return false;
                                }

                                return collect($record->rundown)
                                    ->pluck('data.session_files')
                                    ->flatten()
                                    ->filter()
                                    ->isNotEmpty();
                            }),
                    ])
                    ->schema([
                        RepeatableEntry::make('rundown')
                            ->hiddenLabel()
                            ->schema([
                                TextEntry::make('data.title')
                                    ->label(__('Session Title'))
                                    ->weight('bold')
                                    ->size('lg')
                                    ->columnSpanFull(),
                                TextEntry::make('data.date')
                                    ->label(__('Date'))
                                    ->date('d M Y')
                                    ->icon('heroicon-m-calendar')
                                    ->visible(fn ($state) => filled($state)),
                                TextEntry::make('data.place')
                                    ->label(__('Location'))
                                    ->icon('heroicon-m-map-pin')
                                    ->visible(fn ($state) => filled($state)),
                                TextEntry::make('data.start_time')
                                    ->label(__('Start'))
                                    ->icon('heroicon-m-clock')
                                    ->visible(fn ($state) => filled($state)),
                                TextEntry::make('data.end_time')
                                    ->label(__('End'))
                                    ->icon('heroicon-m-clock')
                                    ->visible(fn ($state) => filled($state)),
                                TextEntry::make('data.speaker')
                                    ->label(__('Speaker'))
                                    ->icon('heroicon-m-user')
                                    ->badge()
                                    ->color('info')
                                    ->visible(fn ($state) => filled($state)),
                                TextEntry::make('data.description')
                                    ->label(__('Description'))
                                    ->html()
                                    ->prose()
                                    ->columnSpanFull()
                                    ->visible(fn ($state) => filled($state)),
                                ViewEntry::make('data.session_files')
                                    ->label(__('Files & Materials'))
                                    ->view('filament.infolists.components.file-list-simple')
                                    ->columnSpanFull()
                                    ->visible(fn ($state) => $showRundownFiles && filled($state)),
                                RepeatableEntry::make('data.links')
                                    ->label(__('External Resources'))
                                    ->schema([
                                        TextEntry::make('label')->label(__('Resource')),
                                        TextEntry::make('url')->label('URL')->url(fn ($state) => $state, true)->color('primary'),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->visible(fn ($state) => $showRundownLinks && filled($state)),
                                TextEntry::make('data.public_rundown_cta')
                                    ->hiddenLabel()
                                    ->html()
                                    ->state(function () {
                                        $message = e(__('To see files and shared links, please login or make a free account'));
                                        $loginLabel = e(__('Login'));
                                        $registerLabel = e(__('Register'));
                                        $loginUrl = route('filament.user.auth.login');
                                        $registerUrl = route('filament.user.auth.register');

                                        return <<<HTML
<div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-900/40">
    <p class="text-sm text-gray-700 dark:text-gray-200">{$message}</p>
    <div class="mt-3 flex flex-wrap gap-2" style="margin-top:5px;">
        <a href="{$loginUrl}" class="fi-btn fi-btn-size-sm fi-color-gray fi-btn-color-gray fi-btn-outlined" style="margin: 3px; margin-left:1px;">{$loginLabel}</a>
        <a href="{$registerUrl}" class="fi-btn fi-btn-size-sm fi-color-primary fi-btn-color-primary" style="margin: 3px;">{$registerLabel}</a>
    </div>
</div>
HTML;
                                    })
                                    ->columnSpanFull()
                                    ->visible(fn () => $showPublicRundownCta),
                            ])
                            ->columns(2),
                    ])
                    ->visible(fn ($record) => filled($record?->rundown))
                    ->columnSpanFull(),

                Section::make(__('Event Documentation'))
                    ->schema([
                        RepeatableEntry::make('event_docs')
                            ->hiddenLabel()
                            ->getStateUsing(function ($record) {
                                if (! $record) {
                                    return [];
                                }
                                $docs = $record->documentation;
                                if (! is_array($docs)) {
                                    return [];
                                }

                                return collect($docs)->map(fn ($doc) => [
                                    'file_name' => basename($doc),
                                    'file_url' => asset('storage/'.$doc),
                                ])->values()->all();
                            })
                            ->grid([
                                'default' => 1,
                                'sm' => 2,
                                'md' => 3,
                            ])
                            ->schema([
                                Group::make([
                                    TextEntry::make('file_name')
                                        ->hiddenLabel()
                                        ->color('primary')
                                        ->weight('bold')
                                        ->icon('heroicon-m-document-text'),
                                    TextEntry::make('file_url')
                                        ->label(__('Action'))
                                        ->formatStateUsing(fn () => __('View/Download'))
                                        ->url(fn ($state) => $state, true)
                                        ->badge()
                                        ->color('info')
                                        ->icon('heroicon-m-eye'),
                                ])
                                    ->extraAttributes([
                                        'class' => 'bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 h-full overflow-hidden transition-all hover:ring-2 hover:ring-primary-500',
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull()
                    ->visible(fn ($record) => filled($record?->documentation)),

                Section::make(__('Registration Packages'))
                    ->schema([
                        RepeatableEntry::make('registration_packages')
                            ->hiddenLabel()
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('Package'))
                                    ->weight('bold'),
                                TextEntry::make('price')
                                    ->label(__('Price'))
                                    ->money('IDR'),
                                TextEntry::make('max_quota')
                                    ->label(__('Quota'))
                                    ->formatStateUsing(fn ($state) => $state ?: __('Unlimited')),
                                TextEntry::make('description')
                                    ->label(__('Description'))
                                    ->columnSpanFull()
                                    ->visible(fn ($state) => filled($state)),
                            ])
                            ->columns(3),
                    ])
                    ->visible(fn ($record) => $record?->allow_registration && filled($record?->registration_packages))
                    ->columnSpanFull(),
            ]);
    }
}
