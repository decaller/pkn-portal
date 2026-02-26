<div class="space-y-3" x-data="{ open: false, url: '', ext: '' }">
    @foreach ((array) $getState() as $file)
        @php
            if (empty($file))
                continue;

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
        <div
            class="flex items-center justify-between p-3 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 shadow-sm transition hover:shadow-md">
            <div class="flex items-center space-x-3 overflow-hidden">
                <div class="p-2 rounded-lg bg-primary-50 dark:bg-primary-900/50 text-primary-600 dark:text-primary-400">
                    @if($isImage)
                        <x-heroicon-o-photo class="w-5 h-5 shrink-0" />
                    @elseif($isPdf)
                        <x-heroicon-o-document-text class="w-5 h-5 shrink-0" />
                    @else
                        <x-heroicon-o-document class="w-5 h-5 shrink-0" />
                    @endif
                </div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200 truncate">{{ basename($file) }}</span>
            </div>
            <div class="flex items-center space-x-2 shrink-0">
                <button type="button" x-on:click="open = true; url = '{{ $viewerUrl }}'; ext = '{{ $ext }}'"
                    class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-primary-600 bg-primary-50 rounded-lg hover:bg-primary-100 transition dark:bg-primary-900/50 dark:text-primary-400 dark:hover:bg-primary-900">
                    <x-heroicon-m-eye class="w-4 h-4 mr-1.5" />
                    Preview
                </button>
                <a href="{{ $fileUrl }}" download
                    class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition dark:bg-white/5 dark:text-gray-300 dark:hover:bg-white/10">
                    <x-heroicon-m-arrow-down-tray class="w-4 h-4 mr-1.5" />
                    Download
                </a>
            </div>
        </div>
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