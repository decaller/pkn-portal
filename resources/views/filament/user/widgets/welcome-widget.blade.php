<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-col gap-4">
            <div class="flex flex-col gap-1">
                <h2 class="text-gray-900 dark:text-white text-xl font-semibold leading-7 m-0">
                    {{ __('Welcome') }}, {{ auth()->user()?->name ?? __('User') }}
                </h2>
                <p class="text-gray-600 dark:text-gray-400 text-sm m-0">
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

                <div
                    class="bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 p-4 rounded-lg border border-solid mt-4 flex items-center justify-between gap-4 w-full box-border">
                    <div class="flex-1 min-w-0 pr-4">
                        <h3
                            class="text-gray-900 dark:text-gray-100 font-medium text-base m-0 whitespace-nowrap overflow-hidden text-ellipsis">
                            {{ __('Latest Registration') }}: {{ $lastRegistration->event?->title ?? __('Event') }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mt-1 mb-0">
                            @if($isComplete)
                                {{ __('Your registration is complete! You can now access the event page.') }}
                            @else
                                {{ __('Please upload your payment proof and add all participants to complete your registration.') }}
                            @endif
                        </p>
                    </div>
                    <div class="flex-none flex items-center justify-end">
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