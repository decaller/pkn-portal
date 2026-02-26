<div class="w-full h-[70vh] bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden flex items-center justify-center">
    @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
        <img src="{{ $url }}" class="max-w-full max-h-full object-contain p-4" />
    @elseif(in_array($ext, ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx']))
        <iframe src="https://view.officeapps.live.com/op/view.aspx?src={{ urlencode($url) }}&wdOrigin=BROWSELINK"
            class="w-full h-full border-0"></iframe>
    @elseif($ext === 'pdf')
        <iframe src="{{ $url }}" class="w-full h-full border-0"></iframe>
    @else
        <div class="text-center p-8">
            <x-heroicon-o-document class="w-16 h-16 mx-auto text-gray-400 mb-4" />
            <p class="text-gray-500 mb-4">No rich preview available for this file type.</p>
            <a href="{{ $url }}" target="_blank"
                class="px-4 py-2 bg-primary-600 text-white rounded-md font-medium text-sm">Download Instead</a>
        </div>
    @endif
</div>