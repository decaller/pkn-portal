<?php

namespace App\Filament\User\Auth;

use App\Filament\User\Resources\EventRegistrations\Schemas\EventRegistrationForm;
use App\Models\Event;
use App\Models\Organization;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RegisterEvent extends BaseRegister
{
    protected Width|string|null $maxWidth = Width::FourExtraLarge;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Checkbox::make('is_registering_for_event')
                    ->label('I also want to register for an event')
                    ->default(true)
                    ->live(),

                // 2. Wrap your event schema in a Group
                Section::make()
                    ->schema(EventRegistrationForm::schema())
                    // 3. Make the entire group visible ONLY if the checkbox is true
                    ->visible(
                        fn (Get $get) => $get('is_registering_for_event') ===
                            true,
                    ),
                TextInput::make('name')
                    ->label('Full Name')
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),
                TextInput::make('phone_number')
                    ->label('Phone number')
                    ->required()
                    ->maxLength(30)
                    ->unique($this->getUserModel(), 'phone_number'),
                Select::make('registration_type')
                    ->label('Registration type')
                    ->options([
                        'personal' => 'Personal',
                        'existing' => 'Join existing organization',
                        'new' => 'Create new organization',
                    ])
                    ->default('personal')
                    ->required()
                    ->live(),
                Select::make('existing_organization_id')
                    ->label('Choose existing organization')
                    ->options(
                        fn () => Organization::query()
                            ->whereRaw('LOWER(slug) NOT LIKE ?', ['%pkn%'])
                            ->orderBy('name')
                            ->pluck('name', 'id'),
                    )
                    ->visible(
                        fn (Get $get): bool => $get('registration_type') ===
                            'existing',
                    )
                    ->required(
                        fn (Get $get): bool => $get('registration_type') ===
                            'existing',
                    )
                    ->rule(
                        Rule::exists('organizations', 'id')->where(
                            fn ($query) => $query->whereRaw(
                                'LOWER(slug) NOT LIKE ?',
                                ['%pkn%'],
                            ),
                        ),
                    ),
                TextInput::make('organization_name')
                    ->label('New organization name')
                    ->visible(
                        fn (Get $get): bool => $get('registration_type') ===
                            'new',
                    )
                    ->required(
                        fn (Get $get): bool => $get('registration_type') ===
                            'new',
                    )
                    ->maxLength(255),
                FileUpload::make('organization_logo')
                    ->label('Organization logo')
                    ->image()
                    ->disk('public')
                    ->visibility('public')
                    ->directory('organization-logos')
                    ->imageEditor()
                    ->visible(
                        fn (Get $get): bool => $get('registration_type') ===
                            'new',
                    )
                    ->dehydrated(
                        fn (Get $get): bool => $get('registration_type') ===
                            'new',
                    ),
                TextInput::make('password')
                    ->label(
                        __(
                            'filament-panels::auth/pages/register.form.password.label',
                        ),
                    )
                    ->password()
                    ->revealable(filament()->arePasswordsRevealable())
                    ->required()
                    ->minLength(8)
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->same('passwordConfirmation'),
                // TextInput::make("passwordConfirmation")
                //     ->label(
                //         __(
                //             "filament-panels::auth/pages/register.form.password_confirmation.label",
                //         ),
                //     )
                //     ->password()
                //     ->revealable(filament()->arePasswordsRevealable())
                //     ->required()
                //     ->dehydrated(false),
                CheckboxList::make('past_events')
                    ->label('Were you attending these past events? (Optional)')
                    ->options(fn () => Event::where('event_date', '<', now())->pluck('title', 'id'))
                    ->columns(2)
                    ->gridDirection('row')
                    ->visible(fn () => Event::where('event_date', '<', now())->exists()),
            ])

            ->statePath('data');
    }

    protected function handleRegistration(array $data): Model
    {
        if (($data['registration_type'] ?? 'personal') === 'new') {
            $newSlug = Str::slug((string) ($data['organization_name'] ?? ''));

            if (Str::contains($newSlug, 'pkn')) {
                throw ValidationException::withMessages([
                    'data.organization_name' => 'Organization slug cannot contain "pkn".',
                ]);
            }
        }

        return DB::transaction(function () use ($data): Model {
            $phone = trim((string) ($data['phone_number'] ?? ''));

            $userClass = $this->getUserModel();

            /** @var Model $user */
            $user = $userClass::create([
                'name' => $data['name'],
                'phone_number' => $phone,
                'email' => $this->makeGeneratedEmail($phone, $userClass),
                'password' => $data['password'],
                'past_events' => $data['past_events'] ?? null,
            ]);

            $registrationType = $data['registration_type'] ?? 'personal';

            if ($registrationType === 'existing') {
                $organization = Organization::query()
                    ->whereKey($data['existing_organization_id'])
                    ->whereRaw('LOWER(slug) NOT LIKE ?', ['%pkn%'])
                    ->firstOrFail();

                $organization->users()->syncWithoutDetaching([
                    $user->getKey() => ['role' => 'member'],
                ]);
            }

            if ($registrationType === 'new') {
                $baseSlug = Str::slug($data['organization_name']);
                $slug = $this->makeUniqueOrganizationSlug($baseSlug);

                $organization = Organization::create([
                    'name' => $data['organization_name'],
                    'slug' => $slug,
                    'logo' => $data['organization_logo'] ?? null,
                    'admin_user_id' => $user->getKey(),
                ]);

                $organization->users()->syncWithoutDetaching([
                    $user->getKey() => ['role' => 'admin'],
                ]);
            }

            return $user;
        });
    }

    /**
     * @param  class-string<Model>  $userClass
     */
    private function makeGeneratedEmail(
        string $phoneNumber,
        string $userClass,
    ): string {
        $normalized =
            preg_replace("/\D+/", '', $phoneNumber) ?:
            Str::lower(Str::random(8));
        $base = "phone-{$normalized}";
        $candidate = "{$base}@local.pkn";
        $counter = 1;

        while ($userClass::where('email', $candidate)->exists()) {
            $candidate = "{$base}-{$counter}@local.pkn";
            $counter++;
        }

        return $candidate;
    }

    private function makeUniqueOrganizationSlug(string $baseSlug): string
    {
        $slug = $baseSlug;
        $counter = 1;

        while (Organization::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
