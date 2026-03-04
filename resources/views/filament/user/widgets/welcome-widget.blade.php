<x-filament-widgets::widget>
    <x-filament::section>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                <h2 class="text-gray-900 dark:text-white"
                    style="font-size: 1.25rem; font-weight: 600; line-height: 1.75rem; margin: 0;">
                    {{ __('Welcome') }}, {{ auth()->user()?->name ?? __('User') }}
                </h2>
                <p class="text-gray-600 dark:text-gray-400" style="font-size: 0.875rem; margin: 0;">
                    {{ __('Manage your event registrations from this dashboard.') }}
                </p>
            </div>

            @if($lastRegistration)
                @php
                    $added = (int) ($lastRegistration->participants_count ?? $lastRegistration->participants->count());
                    $max = (int) collect($lastRegistration->package_breakdown ?? [])->sum('participant_count');
                    $isVerified = $lastRegistration->payment_status === \App\Enums\PaymentStatus::Verified;
                    $isComplete = $isVerified && $added >= $max;
                @endphp

                <div class="bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700"
                    style="padding: 1rem; border-radius: 0.5rem; border-width: 1px; border-style: solid; margin-top: 1rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; width: 100%; box-sizing: border-box;">
                    <div style="flex: 1 1 0%; min-width: 0; padding-right: 1rem;">
                        <h3 class="text-gray-900 dark:text-gray-100"
                            style="font-weight: 500; font-size: 1rem; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ __('Latest Registration') }}: {{ $lastRegistration->event?->title ?? __('Event') }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400"
                            style="font-size: 0.875rem; margin-top: 0.25rem; margin-bottom: 0;">
                            @if($isComplete)
                                {{ __('Your registration is complete! You can now access the event page.') }}
                            @else
                                {{ __('Please upload your payment proof and add all participants to complete your registration.') }}
                            @endif
                        </p>
                    </div>
                    <div style="flex: none; display: flex; align-items: center; justify-content: flex-end;">
                        @if($isComplete)
                            <x-filament::button tag="a"
                                href="{{ \App\Filament\User\Resources\Events\EventResource::getUrl('view', ['record' => $lastRegistration->event_id]) }}">
                                {{ __('Go to Event Page') }}
                            </x-filament::button>
                        @else
                            <x-filament::button tag="a"
                                href="{{ \App\Filament\User\Resources\EventRegistrations\EventRegistrationResource::getUrl('view', ['record' => $lastRegistration->id]) }}"
                                color="warning">
                                {{ __('View Registration') }}
                            </x-filament::button>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>