<?php

namespace App\Filament\User\Resources\Users\Tables;

use App\Filament\User\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('phone_number')
                    ->label(__('Phone'))
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('organizations')
                    ->label(__('Organizations'))
                    ->state(fn (User $record): string => $record->organizations
                        ->map(fn ($organization): string => $organization->name)
                        ->join(', ')
                    )
                    ->toggleable(),
                IconColumn::make('is_super_admin')
                    ->label(__('Main admin'))
                    ->boolean(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('organization')
                    ->label(__('Organization'))
                    ->relationship('organizations', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('activities')
                    ->label(__('Activities'))
                    ->icon('heroicon-o-clock')
                    ->url(fn (User $record) => UserResource::getUrl('activities', ['record' => $record]))
                    ->visible(fn (User $record) => UserResource::canViewActivities($record)),
            ]);
    }
}
