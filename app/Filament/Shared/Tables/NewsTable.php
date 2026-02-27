<?php

namespace App\Filament\Shared\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->columns([
                Stack::make([
                    ImageColumn::make('thumbnail')
                        ->height('200px')
                        ->width('100%')
                        ->extraImgAttributes(['class' => 'object-cover rounded-t-xl w-full']),

                    Stack::make([
                        TextColumn::make('title')
                            ->weight('bold')
                            ->size('lg')
                            ->limit(50),

                        Split::make([
                            TextColumn::make('created_at')
                                ->dateTime('M d, Y')
                                ->color('gray')
                                ->icon('heroicon-m-calendar'),

                            TextColumn::make('analytics_count')
                                ->counts('analytics')
                                ->badge()
                                ->color('info')
                                ->icon('heroicon-m-eye'),
                        ]),
                    ])->space(3)->extraAttributes(['class' => 'p-4']),
                ])->space(0)->extraAttributes(['class' => 'bg-white shadow rounded-xl dark:bg-gray-800 ring-1 ring-gray-950/5 dark:ring-white/10']),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
