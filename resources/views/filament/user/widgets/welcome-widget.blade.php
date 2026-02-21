<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-1">
            <h2 class="text-xl font-semibold">
                Welcome, {{ auth()->user()?->name ?? 'User' }}
            </h2>
            <p class="text-sm text-gray-600">
                Manage your event registrations from this dashboard.
            </p>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
