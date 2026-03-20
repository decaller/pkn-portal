<?php

namespace App\Filament\User\Auth;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public ?string $source = null;

    public function mount(): void
    {
        parent::mount();

        $this->source = request()->query('source');
    }

    public function authenticate(): ?LoginResponse
    {
        $response = parent::authenticate();

        if ($this->source === 'mobile') {
            $this->redirect(route('api.v1.auth.token-handoff'));

            return null;
        }

        return $response;
    }

    public function getRedirectUrl(): string
    {
        if ($this->source === 'mobile') {
            return route('api.v1.auth.token-handoff');
        }

        return parent::getRedirectUrl();
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('phone_number')
            ->label(__('Phone number'))
            ->required()
            ->autocomplete()
            ->autofocus();
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'phone_number' => trim((string) ($data['phone_number'] ?? '')),
            'password' => $data['password'] ?? '',
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.phone_number' => __('filament-panels::auth/pages/login.messages.failed'),
        ]);
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getHelpAction(),
            $this->getAuthenticateFormAction(),

        ];
    }

    protected function getHelpAction(): Action
    {
        return Action::make('help')
            ->label(app()->getLocale() === 'id' ? 'Bantuan' : 'Help')
            ->color('gray')
            ->outlined()
            ->url(
                fn (): ?string => Setting::defaultContactWhatsAppUrl('Hello, I need help signing in to PKN Portal.'),
                shouldOpenInNewTab: true,
            )
            ->visible(fn (): bool => filled(Setting::defaultContactWhatsAppUrl()));
    }
}
