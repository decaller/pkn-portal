<style>
    /* Structural Layout */
    .file-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
    }

    .file-card {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .file-header {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .file-info {
        flex: 1;
        min-width: 0;
    }

    .file-spacer {
        flex-grow: 1;
    }

    .file-btn-container {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .file-modal-img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        position: absolute;
        inset: 0;
        margin: auto;
        padding: 1rem;
    }

    .file-modal-iframe {
        width: 100%;
        height: 100%;
        border: 0;
        position: absolute;
        inset: 0;
    }

    /* Colors (Light Mode) */
    .file-icon-box {
        padding: 0.75rem;
        border-radius: 0.5rem;
        flex-shrink: 0;
        background-color: #f3f4f6;
    }

    .file-title {
        font-size: 0.875rem;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #111827;
        margin: 0;
    }

    .file-ext {
        font-size: 0.75rem;
        margin-top: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        color: #6b7280;
        margin-bottom: 0;
    }

    .file-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding-top: 1rem;
        margin-top: 1rem;
        border-top: 1px solid #e5e7eb;
    }

    .file-modal-bg {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
        border-radius: 0.75rem;
        border: 1px dashed #d1d5db;
        min-height: 60vh;
        position: relative;
        overflow: hidden;
        background-color: #f9fafb;
    }

    /* Colors (Dark Mode Overrides) */
    .dark .file-icon-box {
        background-color: #1f2937;
    }

    .dark .file-title {
        color: #ffffff;
    }

    .dark .file-ext {
        color: #9ca3af;
    }

    .dark .file-actions {
        border-top-color: rgba(255, 255, 255, 0.1);
    }

    .dark .file-modal-bg {
        background-color: #1f2937;
        border-color: #374151;
    }
</style>

<div class="file-grid">
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

        <x-filament::section class="file-card">
            {{-- Card Header: Icon and File Info --}}
            <div class="file-header">

                {{-- File Type Icon --}}
                <div class="file-icon-box">
                    @if($isPdf)
                        <x-filament::icon icon="heroicon-o-document-text" style="width: 2rem; height: 2rem; color: #ef4444;" />
                    @elseif($isDoc)
                        <x-filament::icon icon="heroicon-o-table-cells" style="width: 2rem; height: 2rem; color: #10b981;" />
                    @elseif($isImage)
                        <x-filament::icon icon="heroicon-o-photo" style="width: 2rem; height: 2rem; color: #3b82f6;" />
                    @else
                        <x-filament::icon icon="heroicon-o-document" style="width: 2rem; height: 2rem; color: #6b7280;" />
                    @endif
                </div>

                {{-- File Name and Size --}}
                <div class="file-info">
                    <h3 class="file-title" title="{{ basename($file) }}">
                        {{ basename($file) }}
                    </h3>
                    <p class="file-ext">
                        {{ strtoupper($ext) }}
                    </p>
                </div>
            </div>

            {{-- Spacer to push buttons to the bottom if file names wrap --}}
            <div class="file-spacer"></div>

            {{-- Card Actions: Buttons --}}
            <div class="file-actions">

                {{-- Preview Modal --}}
                <x-filament::modal id="preview-modal-{{ $fileId }}" width="5xl">
                    <x-slot name="trigger">
                        {{-- Trigger Button --}}
                        <div class="file-btn-container">
                            <x-filament::button color="gray" size="sm" icon="heroicon-m-eye" style="width: 100%;">
                                Preview
                            </x-filament::button>
                        </div>
                    </x-slot>

                    <x-slot name="heading">
                        Preview: {{ basename($file) }}
                    </x-slot>

                    {{-- Modal Content --}}
                    <div class="file-modal-bg">
                        @if($isImage)
                            <img src="{{ $viewerUrl }}" class="file-modal-img" />
                        @else
                            <iframe src="{{ $viewerUrl }}" class="file-modal-iframe"></iframe>
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
                <div class="file-btn-container">
                    <x-filament::button color="primary" size="sm" icon="heroicon-m-arrow-down-tray" tag="a"
                        href="{{ $fileUrl }}" download="{{ basename($file) }}" style="width: 100%;">
                        Download
                    </x-filament::button>
                </div>

            </div>
        </x-filament::section>
    @endforeach
</div>