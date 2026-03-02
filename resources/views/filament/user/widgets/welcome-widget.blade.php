<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-1">
            <h2 class="text-xl font-semibold">
                {{ __('Welcome') }}, {{ auth()->user()?->name ?? __('User') }}
            </h2>
            <p class="text-sm text-gray-600">
                {{ __('Manage your event registrations from this dashboard.') }}
            </p>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>