<?php

namespace App\Filament\Admin\Resources\Users\Tables;

use App\Models\User;
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
                        ->join(', '))
                    ->toggleable(),
                IconColumn::make('is_super_admin')
                    ->label(__('Main admin'))
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->since(),
            ])
            ->filters([
                SelectFilter::make('organization')
                    ->label(__('Organization'))
                    ->relationship('organizations', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
