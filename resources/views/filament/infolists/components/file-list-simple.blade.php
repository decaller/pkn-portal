<div class="grid grid-cols-[repeat(auto-fit,minmax(280px,1fr))] gap-6">
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

            // Changed view.aspx to embed.aspx for iframe compatibility
            $viewerUrl = $isDoc
                ? 'https://view.officeapps.live.com/op/embed.aspx?src=' . urlencode($fileUrl)
                : $fileUrl;

            $fileId = md5($fileUrl); // Unique ID for modal
        @endphp

        <x-filament::section class="flex flex-col h-full">
            {{-- Card Header: Icon and File Info --}}
            <div class="flex items-start gap-4 mb-4">

                {{-- File Type Icon --}}
                <div class="p-3 rounded-lg shrink-0 bg-gray-100 dark:bg-gray-800">
                    @if($isPdf)
                        <x-filament::icon icon="heroicon-o-document-text" class="w-8 h-8 text-danger-500" />
                    @elseif($isDoc)
                        <x-filament::icon icon="heroicon-o-table-cells" class="w-8 h-8 text-success-500" />
                    @elseif($isImage)
                        <x-filament::icon icon="heroicon-o-photo" class="w-8 h-8 text-primary-500" />
                    @else
                        <x-filament::icon icon="heroicon-o-document" class="w-8 h-8 text-gray-500" />
                    @endif
                </div>

                {{-- File Name and Size --}}
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-medium whitespace-nowrap overflow-hidden text-ellipsis text-gray-900 dark:text-white m-0" title="{{ basename($file) }}">
                        {{ basename($file) }}
                    </h3>
                    <p class="text-xs mt-1 uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-0">
                        {{ strtoupper($ext) }}
                    </p>
                </div>
            </div>

            {{-- Spacer to push buttons to the bottom if file names wrap --}}
            <div class="flex-grow"></div>

            {{-- Card Actions: Buttons --}}
            <div class="flex items-center gap-3 pt-4 mt-4 border-t border-gray-200 dark:border-white/10">

                {{-- Preview Modal --}}
                <x-filament::modal id="preview-modal-{{ $fileId }}" width="5xl">
                    <x-slot name="trigger">
                        {{-- Trigger Button --}}
                        <div class="flex-1 flex items-center justify-center">
                            <x-filament::button color="gray" size="sm" icon="heroicon-m-eye" class="w-full justify-center">
                                Preview
                            </x-filament::button>
                        </div>
                    </x-slot>

                    <x-slot name="heading">
                        Preview: {{ basename($file) }}
                    </x-slot>

                    {{-- Modal Content --}}
                    <div class="flex items-center justify-center p-8 rounded-xl border border-dashed border-gray-300 dark:border-gray-700 min-h-[60vh] relative overflow-hidden bg-gray-50 dark:bg-gray-800/50">
                        @if($isImage)
                            <img src="{{ $viewerUrl }}" class="max-w-full max-h-full object-contain absolute inset-0 m-auto p-4" />
                        @else
                            <iframe src="{{ $viewerUrl }}" class="w-full h-full border-0 absolute inset-0"></iframe>
                        @endif
                    </div>

                    <x-slot name="footerActions">
                        <x-filament::button tag="a" href="{{ $viewerUrl }}" target="_blank" color="primary">
                            Open in New Tab
                        </x-filament::button>
                        <x-filament::button color="gray" x-on:click="close()">
                            Close
                        </x-filament::button>
                    </x-slot>
                </x-filament::modal>

                {{-- Download Button --}}
                <div class="flex-1 flex items-center justify-center">
                    <x-filament::button color="primary" size="sm" icon="heroicon-m-arrow-down-tray" tag="a"
                        href="{{ $fileUrl }}" download="{{ basename($file) }}" class="w-full justify-center">
                        Download
                    </x-filament::button>
                </div>

            </div>
        </x-filament::section>
    @endforeach
</div>