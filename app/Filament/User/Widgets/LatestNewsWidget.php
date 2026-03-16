<?php

namespace App\Filament\User\Widgets;

use App\Models\News;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestNewsWidget extends TableWidget
{
    protected int|string|array $columnSpan = 2;

    protected static ?int $sort = 6;

    protected static ?string $heading = null;

    public function getHeading(): string
    {
        return __('Latest news');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                News::query()->where('is_published', true)->latest()->limit(5),
            )
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->columns([
                Stack::make([
                    ImageColumn::make('thumbnail')
                        ->height('150px')
                        ->width('100%')
                        ->extraImgAttributes([
                            'class' => 'object-cover rounded-xl w-full',
                        ]),
                    Stack::make([
                        TextColumn::make('title')->weight('bold')->limit(45)->size('lg'),
                        TextColumn::make('created_at')
                            ->since()
                            ->label(__('Published'))
                            ->color('gray')
                            ->size('sm'),
                    ])->space(1)->extraAttributes(['class' => 'pt-4']),
                ])->space(0),
            ])
            ->paginated(false)
            ->emptyStateHeading(__('No published news yet.'))
            ->actions([
                ViewAction::make('view_news')
                    ->label(__('Read Article'))
                    ->modalHeading(fn ($record) => $record->title)
                    ->infolist([
                        ImageEntry::make('thumbnail')
                            ->hiddenLabel()
                            ->extraImgAttributes(['class' => 'rounded-xl w-full object-cover max-h-96'])
                            ->columnSpanFull(),
                        TextEntry::make('created_at')
                            ->hiddenLabel()
                            ->date('F j, Y, g:i a')
                            ->color('gray')
                            ->columnSpanFull(),
                        TextEntry::make('content')
                            ->hiddenLabel()
                            ->html()
                            ->prose()
                            ->columnSpanFull(),
                    ])
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('Close')),
            ])
            ->recordAction('view_news');
    }
}
