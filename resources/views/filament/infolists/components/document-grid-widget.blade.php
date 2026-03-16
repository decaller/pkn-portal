<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @php
        $state = $getState() ?? [];
        $items = $state['items'] ?? [];
        $empty = $state['empty'] ?? __('No documents found.');
        $prevUrl = $state['prev_url'] ?? null;
        $nextUrl = $state['next_url'] ?? null;
        $eventUrl = $state['event_url'] ?? null;
        $eventLabel = $state['event_label'] ?? __('View Related Event');
    @endphp

    @if ($eventUrl)
        <div class="mb-4 flex justify-end">
            <x-filament::button
                tag="a"
                href="{{ $eventUrl }}"
                size="sm"
                color="info"
                icon="heroicon-o-link"
            >
                {{ $eventLabel }}
            </x-filament::button>
        </div>
    @endif

    @if (count($items) === 0)
        <div class="text-sm italic text-gray-500 dark:text-gray-400">
            {{ $empty }}
        </div>
    @else
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($items as $item)
                <x-filament::section class="h-full">
                    <div class="flex h-full flex-col gap-3">
                        <div>
                            <div class="text-base font-semibold text-gray-900 dark:text-white">
                                {{ $item['title'] ?? '-' }}
                            </div>
                            <div class="mt-1 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                {{ $item['type'] ?? __('Other File') }}
                            </div>
                            @if (! empty($item['event']))
                                <div class="mt-2 text-xs text-gray-600 dark:text-gray-300">
                                    {{ __('Event') }}: {{ $item['event'] }}
                                </div>
                            @endif
                            @if (! empty($item['created_at']))
                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('Added') }}: {{ $item['created_at'] }}
                                </div>
                            @endif
                        </div>

                        <div class="mt-auto flex items-center gap-2">
                            @if (! empty($item['view_url']))
                                <x-filament::button
                                    tag="a"
                                    href="{{ $item['view_url'] }}"
                                    size="sm"
                                    color="primary"
                                    icon="heroicon-o-eye"
                                >
                                    {{ __('View') }}
                                </x-filament::button>
                            @endif
                            @if (! empty($item['download_url']))
                                <x-filament::button
                                    tag="a"
                                    href="{{ $item['download_url'] }}"
                                    size="sm"
                                    color="gray"
                                    icon="heroicon-o-arrow-down-tray"
                                    download
                                >
                                    {{ __('Download') }}
                                </x-filament::button>
                            @endif
                        </div>
                    </div>
                </x-filament::section>
            @endforeach
        </div>

        @if ($prevUrl || $nextUrl)
            <div class="mt-4 flex items-center justify-between">
                <div>
                    @if ($prevUrl)
                        <x-filament::button tag="a" href="{{ $prevUrl }}" size="sm" color="gray" icon="heroicon-o-arrow-left">
                            {{ __('Previous') }}
                        </x-filament::button>
                    @endif
                </div>
                <div>
                    @if ($nextUrl)
                        <x-filament::button tag="a" href="{{ $nextUrl }}" size="sm" color="gray" icon="heroicon-o-arrow-right">
                            {{ __('Next') }}
                        </x-filament::button>
                    @endif
                </div>
            </div>
        @endif
    @endif
</x-dynamic-component>
