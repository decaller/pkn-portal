<?php

namespace App\Filament\Admin\Resources\Organizations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrganizationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->disk('public')
                    ->circular()
                    ->label(''),
                TextColumn::make('name')
                    ->translateLabel()
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('slug')->translateLabel()->searchable(),
                TextColumn::make('admin.name')
                    ->label(__('Admin'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('users_count')->label(__('Members'))->badge(),
                TextColumn::make('updated_at')->translateLabel()->since(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([ViewAction::make(), EditAction::make()])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
