<?php

namespace App\Filament\User\Resources\EventRegistrations;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Filament\User\Resources\EventRegistrations\Pages\CreateEventRegistration;
use App\Filament\User\Resources\EventRegistrations\Pages\EditEventRegistration;
use App\Filament\User\Resources\EventRegistrations\Pages\ListEventRegistrations;
use App\Filament\User\Resources\EventRegistrations\Pages\ViewEventRegistration;
use App\Filament\User\Resources\EventRegistrations\Schemas\EventRegistrationForm;
use App\Filament\User\Resources\EventRegistrations\Schemas\EventRegistrationInfolist;
use App\Filament\User\Resources\EventRegistrations\Tables\EventRegistrationsTable;
use App\Models\EventRegistration;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EventRegistrationResource extends Resource
{
    protected static ?string $model = EventRegistration::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static ?string $navigationLabel = 'My registrations';

    public static function form(Schema $schema): Schema
    {
        return EventRegistrationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EventRegistrationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EventRegistrationsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('booker_user_id', auth()->id());
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['booker_user_id'] = auth()->id();
        $data['status'] = RegistrationStatus::Draft;
        $data['payment_status'] = PaymentStatus::Unpaid;

        return $data;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEventRegistrations::route('/'),
            'create' => CreateEventRegistration::route('/create'),
            'view' => ViewEventRegistration::route('/{record}'),
            'edit' => EditEventRegistration::route('/{record}/edit'),
        ];
    }
}
