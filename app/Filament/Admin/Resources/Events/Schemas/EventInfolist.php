<?php

namespace App\Filament\Admin\Resources\Events\Schemas;

use App\Filament\Shared\Schemas\EventInfolist as SharedEventInfolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EventInfolist
{
    public static function configure(Schema $schema): Schema
    {
        // 1. Pass a fresh Schema instance to your shared class
        // and extract the configured components as an array.
        $sharedComponents = SharedEventInfolist::configure(Schema::make())->getComponents(withHidden: true);

        // 2. Spread those extracted components into your new schema
        return $schema
            ->components([
                ...$sharedComponents,

                // 3. Add your new Admin-specific items right below it
                Section::make(__('Admin Extras'))->schema([
                    TextEntry::make('admin_notes')->label(__('Admin Notes')),
                    TextEntry::make('created_at')->label(__('Created At'))->dateTime(),
                ]),
            ]);
    }
}
