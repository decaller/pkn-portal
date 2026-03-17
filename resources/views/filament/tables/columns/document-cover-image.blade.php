<div class="w-full h-[200px] relative group overflow-hidden">
    @php
        $record = $getRecord();
        $coverImage = $record->cover_image;
        $mimeType = $record->mime_type;
        $isImage = str_contains($mimeType, 'image');
    @endphp

    @if ($coverImage)
        <img src="{{ Storage::disk('public')->url($coverImage) }}" 
             alt="{{ $record->title }}" 
             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
    @elseif ($isImage)
        <img src="{{ Storage::disk('public')->url($record->file_path) }}" 
             alt="{{ $record->title }}" 
             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
    @else
        <div class="w-full h-full flex items-center justify-center bg-gray-50 dark:bg-gray-900 text-gray-400">
            @if (str_contains($mimeType, 'pdf'))
                <x-heroicon-o-document-text class="w-16 h-16 opacity-50" />
            @elseif (str_contains($mimeType, 'word'))
                <x-heroicon-o-document-duplicate class="w-16 h-16 opacity-50" />
            @elseif (str_contains($mimeType, 'excel') || str_contains($mimeType, 'sheet'))
                <x-heroicon-o-table-cells class="w-16 h-16 opacity-50" />
            @elseif (str_contains($mimeType, 'powerpoint') || str_contains($mimeType, 'presentation'))
                <x-heroicon-o-presentation-chart-bar class="w-16 h-16 opacity-50" />
            @else
                <x-heroicon-o-document class="w-16 h-16 opacity-50" />
            @endif
        </div>
    @endif
    
    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-300"></div>
</div>
