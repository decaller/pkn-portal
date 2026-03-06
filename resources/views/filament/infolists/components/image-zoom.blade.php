@php
    $state = $getState();
    if (!is_array($state)) {
        $state = $state ? [$state] : [];
    }

    // We pass these from viewData
    $isStacked = $stacked ?? false;
    $isCircular = $circular ?? false;
@endphp

@if(count($state))
    <div class="{{ $isStacked ? 'flex -space-x-4 items-center' : 'flex flex-wrap gap-4' }}">
        @foreach($state as $index => $item)
            @php
                $url = str_starts_with($item, 'http') ? $item : asset('storage/' . $item);
                $modalId = 'zoom-modal-' . md5($url . $index);
            @endphp

            <x-filament::modal id="{{ $modalId }}" width="5xl">
                <x-slot name="trigger">
                    <img src="{{ $url }}"
                        class="cursor-pointer hover:opacity-80 transition object-cover shadow-sm
                                               {{ $isCircular ? 'w-12 h-12 rounded-full ring-2 ring-white dark:ring-gray-900 mx-1' : 'h-40 rounded-xl' }}"
                        alt="Image preview" />
                </x-slot>

                <div class="flex items-center justify-center p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl">
                    <img src="{{ $url }}" class="max-w-full max-h-[80vh] rounded-lg shadow-lg" alt="Zoomed image" />
                </div>
            </x-filament::modal>
        @endforeach
    </div>
@endif