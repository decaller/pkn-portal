<div class="flex flex-wrap gap-2" x-data="{ open: false, url: '', ext: '' }">
    @foreach ((array) $getState() as $file)
        @php
            if (empty($file))
                continue;

            // Check if string is a URL or a file path
            if (str_starts_with($file, 'http')) {
                $fileUrl = $file;
                $ext = 'link';
            } else {
                $fileUrl = asset('storage/' . $file);
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            }

            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
            $isPdf = $ext === 'pdf';
            $isDoc = in_array($ext, ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx']);

            $viewerUrl = $isDoc
                ? 'https://view.officeapps.live.com/op/view.aspx?src=' . urlencode($fileUrl)
                : $fileUrl;
        @endphp
        <button type="button" x-on:click="open = true; url = '{{ $viewerUrl }}'; ext = '{{ $ext }}'"
            class="inline-flex items-center justify-center space-x-1 rounded-full bg-primary-50 px-3 py-1 text-sm font-medium text-primary-600 hover:bg-primary-100 dark:bg-primary-900/50 dark:text-primary-400 dark:hover:bg-primary-900 ring-1 ring-inset ring-primary-600/20 transition duration-200">
            @if($isImage)
                <x-heroicon-m-photo class="w-4 h-4" />
            @elseif($isPdf)
                <x-heroicon-m-document-text class="w-4 h-4" />
            @else
                <x-heroicon-m-document-arrow-down class="w-4 h-4" />
            @endif
            <span>{{ basename($file) }}</span>
        </button>
    @endforeach

    <template x-teleport="body">
        <div x-show="open"
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm"
            style="display: none;" x-transition>
            <div x-on:click.away="open = false"
                class="relative w-full max-w-6xl bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-6 flex flex-col h-[90vh]">
                <div class="flex justify-between items-center mb-4 border-b border-gray-200 dark:border-gray-800 pb-4">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">File Preview</h3>
                    <button x-on:click="open = false"
                        class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition">
                        <x-heroicon-o-x-mark class="w-8 h-8" />
                    </button>
                </div>

                <div
                    class="flex-1 w-full h-full bg-gray-100 dark:bg-gray-800/50 rounded-xl overflow-hidden flex items-center justify-center relative inner-shadow">
                    <template x-if="['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)">
                        <img :src="url" class="max-w-full max-h-full object-contain p-4" />
                    </template>
                    <template x-if="!['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)">
                        <iframe :src="url" class="w-full h-full border-0 absolute inset-0"></iframe>
                    </template>
                </div>

                <div class="mt-4 pt-4 flex justify-between items-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">If the preview fails to load natively, try
                        opening it in a new tab.</p>
                    <a :href="url" target="_blank"
                        class="px-5 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-lg hover:bg-primary-500 focus:ring-4 focus:ring-primary-500/20 transition">
                        Open in New Tab
                    </a>
                </div>
            </div>
        </div>
    </template>
</div>