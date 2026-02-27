<?php

namespace App\Filament\Public\Widgets;

use App\Filament\Public\Resources\Events\EventResource;
use App\Models\Event;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class AvailableRegistrationEventsWidget extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    protected static ?string $heading = 'Available events to register';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Event::query()
                    ->where('is_published', true)
                    ->where('allow_registration', true)
                    ->whereDate('event_date', '>=', now()->toDateString())
                    ->orderBy('event_date'),
            )
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->columns([
                \Filament\Tables\Columns\Layout\Stack::make([
                    \Filament\Tables\Columns\ImageColumn::make('cover_image')
                        ->height('150px')
                        ->width('100%')
                        ->extraImgAttributes([
                            'class' => 'object-cover rounded-xl w-full',
                        ]),
                    \Filament\Tables\Columns\Layout\Stack::make([
                        TextColumn::make('title')->label('Event')->weight('bold')->size('lg'),
                        TextColumn::make('event_date')
                            ->label('Date')
                            ->date('d M Y')
                            ->icon('heroicon-m-calendar')
                            ->color('gray')
                            ->size('sm'),
                    ])->space(1)->extraAttributes(['class' => 'pt-4']),
                ])->space(0),
            ])
            ->recordUrl(fn (Event $record): string => EventResource::getUrl('view', ['record' => $record]))
            ->recordActions([
                ViewAction::make()->url(fn (Event $record): string => EventResource::getUrl('view', ['record' => $record])),
                Action::make('register')->icon('heroicon-o-ticket')->url(
                    fn (Event $record): string => '/user/register?event_id='.$record->getKey()
                ),
            ])
            ->defaultSort('event_date', 'asc')
            ->paginated(false)
            ->emptyStateHeading(
                'No events are open for registration right now.',
            );
    }
}
