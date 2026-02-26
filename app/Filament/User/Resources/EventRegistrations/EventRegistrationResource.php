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
use UnitEnum;

class EventRegistrationResource extends Resource
{
    protected static ?string $model = EventRegistration::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static string|UnitEnum|null $navigationGroup = 'Events';

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
        $userId = auth()->id();

        return parent::getEloquentQuery()
            ->with(['event', 'organization', 'invoices', 'participants', 'booker'])
            ->where(function (Builder $query) use ($userId): void {
                $query->where('booker_user_id', $userId)
                    ->orWhereHas('participants', fn (Builder $q) => $q->where('user_id', $userId));
            });
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $rows = is_array($data['package_breakdown'] ?? null)
            ? $data['package_breakdown']
            : [];

        $data['total_amount'] = array_reduce(
            $rows,
            fn (float $carry, mixed $row): float => $carry +
                (float) (is_array($row) ? $row['unit_price'] ?? 0 : 0),
            0.0,
        );

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
