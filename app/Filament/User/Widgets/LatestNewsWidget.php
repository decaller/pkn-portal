<?php

namespace App\Filament\User\Widgets;

use App\Models\News;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestNewsWidget extends TableWidget
{
    protected int|string|array $columnSpan = 2;

    protected static ?int $sort = 6;

    protected static ?string $heading = "Latest news";

    public function table(Table $table): Table
    {
        return $table
            ->query(
                News::query()->where("is_published", true)->latest()->limit(5),
            )
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->columns([
                \Filament\Tables\Columns\Layout\Stack::make([
                    \Filament\Tables\Columns\ImageColumn::make('thumbnail')
                        ->height('150px')
                        ->width('100%')
                        ->extraImgAttributes([
                            'class' => 'object-cover rounded-xl w-full',
                        ]),
                    \Filament\Tables\Columns\Layout\Stack::make([
                        TextColumn::make("title")->weight("bold")->limit(45)->size('lg'),
                        TextColumn::make("created_at")
                            ->since()
                            ->label("Published")
                            ->color('gray')
                            ->size('sm'),
                    ])->space(1)->extraAttributes(['class' => 'pt-4']),
                ])->space(0),
            ])
            ->paginated(false)
            ->emptyStateHeading("No published news yet.")
            ->actions([
                \Filament\Actions\ViewAction::make('view_news')
                    ->label('Read Article')
                    ->modalHeading(fn ($record) => $record->title)
                    ->infolist([
                        \Filament\Infolists\Components\ImageEntry::make('thumbnail')
                            ->hiddenLabel()
                            ->extraImgAttributes(['class' => 'rounded-xl w-full object-cover max-h-96'])
                            ->columnSpanFull(),
                        \Filament\Infolists\Components\TextEntry::make('created_at')
                            ->hiddenLabel()
                            ->date('F j, Y, g:i a')
                            ->color('gray')
                            ->columnSpanFull(),
                        \Filament\Infolists\Components\TextEntry::make('content')
                            ->hiddenLabel()
                            ->html()
                            ->prose()
                            ->columnSpanFull(),
                    ])
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ])
            ->recordAction('view_news');
    }
}
