<?php

namespace App\Filament\Admin\Resources\EventRegistrations;

use App\Enums\PaymentStatus;
use App\Filament\Admin\Resources\EventRegistrations\Pages\CreateEventRegistration;
use App\Filament\Admin\Resources\EventRegistrations\Pages\EditEventRegistration;
use App\Filament\Admin\Resources\EventRegistrations\Pages\ListEventRegistrationActivities;
use App\Filament\Admin\Resources\EventRegistrations\Pages\ListEventRegistrations;
use App\Filament\Admin\Resources\EventRegistrations\Pages\ViewEventRegistration;
use App\Filament\Admin\Resources\EventRegistrations\Schemas\EventRegistrationForm;
use App\Filament\Admin\Resources\EventRegistrations\Schemas\EventRegistrationInfolist;
use App\Filament\Admin\Resources\EventRegistrations\Tables\EventRegistrationsTable;
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

    public static function getNavigationGroup(): ?string
    {
        return __('Event Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('User registrations');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('payment_status', PaymentStatus::Submitted)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function getModelLabel(): string
    {
        return __('Event Registration');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Event Registrations');
    }

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
        return parent::getEloquentQuery()
            ->with(['event', 'organization', 'booker', 'verifier', 'invoices.latestPayment', 'latestInvoice.latestPayment'])
            ->withCount('participants');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEventRegistrations::route('/'),
            'create' => CreateEventRegistration::route('/create'),
            'view' => ViewEventRegistration::route('/{record}'),
            'activities' => ListEventRegistrationActivities::route('/{record}/activities'),
            'edit' => EditEventRegistration::route('/{record}/edit'),
        ];
    }
}
