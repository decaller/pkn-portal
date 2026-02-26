<?php

namespace App\Filament\Resources\News\Tables;

use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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
                \Filament\Tables\Columns\Layout\Stack::make([
                    ImageColumn::make('thumbnail')
                        ->height('200px')
                        ->width('100%')
                        ->extraImgAttributes(['class' => 'object-cover rounded-t-xl w-full']),
                    
                    \Filament\Tables\Columns\Layout\Stack::make([
                        TextColumn::make('title')
                            ->searchable()
                            ->sortable()
                            ->weight('bold')
                            ->size('lg')
                            ->limit(50),
                            
                        \Filament\Tables\Columns\Layout\Split::make([
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
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
