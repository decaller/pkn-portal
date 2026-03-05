<?php

namespace App\Filament\User\Auth;

use App\Models\Organization;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class Register extends BaseRegister
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('Name'))
                    ->required()
                    ->maxLength(255)
                    ->autofocus(),
                TextInput::make('phone_number')
                    ->label(__('Phone number'))
                    ->required()
                    ->maxLength(30)
                    ->unique($this->getUserModel(), 'phone_number'),
                Select::make('registration_type')
                    ->label(__('Registration type'))
                    ->options([
                        'personal' => __('Personal'),
                        'existing' => __('Join existing organization'),
                        'new' => __('Create new organization'),
                    ])
                    ->default('personal')
                    ->helperText(__('Choose whether you are participating individually or representing an organization.'))
                    ->required()
                    ->live(),
                Select::make('existing_organization_id')
                    ->label(__('Choose existing organization'))
                    ->options(
                        fn () => Organization::query()
                            ->whereRaw('LOWER(slug) NOT LIKE ?', ['%pkn%'])
                            ->orderBy('name')
                            ->pluck('name', 'id'),
                    )
                    ->helperText(__('Search for your organization by its official name.'))
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
                    ->label(__('New organization name'))
                    ->helperText(__('Enter the official name of the new organization.'))
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
                    ->label(__('Organization logo'))
                    ->image()
                    ->imageResizeMode('cover')
                    ->imageResizeTargetWidth('1200')
                    ->disk('public')
                    ->visibility('public')
                    ->directory('organization-logos')
                    ->maxSize(10240)
                    ->helperText(__('Upload a square image (JPG, PNG). Recommended size: 256x256 pixels. Max 10MB.'))
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
                    ->helperText(__('Minimum 8 characters. Your password will be used so you can upload your payment proof and add participant details later.'))
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->same('passwordConfirmation'),
                \Filament\Forms\Components\Placeholder::make('account_benefits')
                    ->label('')
                    ->content(__('By creating a PKN Portal account, you can easily register for future events, download your invoices, access your certificates, and manage your organization\'s members all in one place.')),
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
            ])
            ->statePath('data');
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getRegisterFormAction(),
            $this->getHelpAction(),
        ];
    }

    protected function getHelpAction(): Action
    {
        return Action::make('help')
            ->label(app()->getLocale() === 'id' ? 'Bantuan' : 'Help')
            ->color('gray')
            ->outlined()
            ->url(
                fn (): ?string => Setting::defaultContactWhatsAppUrl('Hello, I need help with registration on PKN Portal.'),
                shouldOpenInNewTab: true,
            )
            ->visible(fn (): bool => filled(Setting::defaultContactWhatsAppUrl()));
    }

    protected function handleRegistration(array $data): Model
    {
        if (($data['registration_type'] ?? 'personal') === 'new') {
            $newSlug = Str::slug((string) ($data['organization_name'] ?? ''));

            if (Str::contains($newSlug, 'pkn')) {
                throw ValidationException::withMessages([
                    'data.organization_name' => __('Organization slug cannot contain "pkn".'),
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
