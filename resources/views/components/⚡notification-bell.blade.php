<?php

use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;

new class extends Component {
    public Collection $notifications;
    public int $unreadCount = 0;
    public bool $isOpen = false;

    public function mount(): void
    {
        $this->loadNotifications();
    }

    public function loadNotifications(): void
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        if (!$user) {
            $this->notifications = collect();
            $this->unreadCount = 0;
            return;
        }

        $this->notifications = $user->notifications()
            ->latest()
            ->limit(20)
            ->get();

        $this->unreadCount = $user->unreadNotifications()->count();
    }

    public function toggleOpen(): void
    {
        $this->isOpen = !$this->isOpen;
    }

    public function markAsRead(string $id): void
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        if (!$user) {
            return;
        }

        $notification = $user->notifications()->find($id);
        $notification?->markAsRead();

        $this->loadNotifications();
    }

    public function markAllAsRead(): void
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        $user?->unreadNotifications()->update(['read_at' => now()]);

        $this->loadNotifications();
    }
};

?>

<div class="relative" style="margin-right: 10px; margin-left: 10px;">
    <x-filament::dropdown placement="bottom-end" shift>
        <x-slot name="trigger">
            <button wire:click="toggleOpen" type="button"
                class="relative inline-flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-white/5 transition-colors duration-150"
                aria-label="{{ __('Notifications') }}" wire:poll.30s="loadNotifications">
                <x-filament::icon icon="heroicon-o-bell" class="w-5 h-5" />

                {{-- Unread badge --}}
                @if ($unreadCount > 0)
                    <span
                        class="absolute top-0.5 right-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-danger-500 px-1 text-[10px] font-bold text-white leading-none">
                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                    </span>
                @endif
            </button>
        </x-slot>

        <div class="w-80 overflow-hidden">
            {{-- Header --}}
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-white/10">
                <span class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ __('Notifications') }}
                    @if ($unreadCount > 0)
                        <span class="ml-1 text-xs font-normal text-gray-500 dark:text-gray-400">({{ $unreadCount }}
                            {{ __('unread') }})</span>
                    @endif
                </span>
                @if ($unreadCount > 0)
                    <button wire:click="markAllAsRead" type="button"
                        class="text-xs text-primary-600 dark:text-primary-400 hover:underline">
                        {{ __('Mark all as read') }}
                    </button>
                @endif
            </div>

            {{-- Notification List --}}
            <div class="max-h-80 overflow-y-auto divide-y divide-gray-100 dark:divide-white/5">
                @forelse ($notifications as $notification)
                    @php
                        $data = $notification->data;
                        $isUnread = is_null($notification->read_at);
                        $type = $data['type'] ?? '';
                        $message = $data['message'] ?? '';

                        // Default link
                        $url = '#';

                        if (in_array($type, ['payment_upload_reminder', 'payment_approved', 'empty_participant_spot_reminder']) && isset($data['registration_id'])) {
                            $url = \App\Filament\User\Resources\EventRegistrations\EventRegistrationResource::getUrl('view', ['record' => $data['registration_id']]);
                        } elseif (in_array($type, ['new_event_open_for_registration', 'past_event_posted_or_updated']) && isset($data['event_slug'])) {
                            $url = \App\Filament\User\Resources\Events\EventResource::getUrl('view', ['record' => $data['event_slug']]);
                        }

                        $icon = match ($type) {
                            'new_event_open_for_registration' => 'heroicon-o-calendar-days',
                            'past_event_posted_or_updated' => 'heroicon-o-photo',
                            'payment_upload_reminder' => 'heroicon-o-credit-card',
                            'payment_approved' => 'heroicon-o-check-badge',
                            'empty_participant_spot_reminder' => 'heroicon-o-user-plus',
                            default => 'heroicon-o-bell',
                        };
                        $iconColor = match ($type) {
                            'payment_approved' => 'text-success-500',
                            'payment_upload_reminder', 'empty_participant_spot_reminder' => 'text-warning-500',
                            default => 'text-primary-500',
                        };
                    @endphp
                    <a href="{{ $url }}"
                        class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-white/5 cursor-pointer transition-colors {{ $isUnread ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' }}"
                        wire:click.prevent="markAsRead('{{ $notification->id }}'); window.location.href = '{{ $url }}'">
                        {{-- Icon --}}
                        <div class="flex-shrink-0 mt-0.5">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 dark:bg-white/10">
                                <x-filament::icon :icon="$icon" class="w-4 h-4 {{ $iconColor }}" />
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <p
                                class="text-sm text-gray-800 dark:text-gray-200 leading-snug {{ $isUnread ? 'font-medium' : '' }}">
                                {{ $message }}
                            </p>
                            <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>

                        {{-- Unread dot --}}
                        @if ($isUnread)
                            <div class="flex-shrink-0 mt-2">
                                <span class="block h-2 w-2 rounded-full bg-primary-500"></span>
                            </div>
                        @endif
                    </a>
                @empty
                    <div class="flex flex-col items-center justify-center gap-2 py-10 text-center">
                        <x-filament::icon icon="heroicon-o-bell-slash" class="w-8 h-8 text-gray-300 dark:text-gray-600" />
                        <p class="text-sm text-gray-400 dark:text-gray-500">{{ __('No notifications yet') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </x-filament::dropdown>
</div>