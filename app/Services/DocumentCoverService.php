<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentCoverService
{
    private const COVER_SIZE = 512;

    public function __construct(
        protected LibreOfficeService $libreOffice,
    ) {}

    public function ensureCover(Document $document, int $sourceLastModified, bool $force = false): void
    {
        $diskName = 'public';
        $disk = Storage::disk($diskName);

        if (! $disk->exists($document->file_path)) {
            Log::warning("Cover generation skipped: source file missing ({$document->file_path}).");

            return;
        }

        $coverPath = $this->coverPath($document);
        if (! $force && $disk->exists($coverPath)) {
            $coverModified = $disk->lastModified($coverPath);
            if ($coverModified >= $sourceLastModified && $document->cover_image === $coverPath) {
                return;
            }
        }

        $mimeType = $document->mime_type ?: $disk->mimeType($document->file_path);
        $coverBinary = $this->buildCoverBinary($diskName, $document->file_path, $mimeType);

        if (! $coverBinary) {
            Log::warning("Cover generation failed: unable to build image for {$document->file_path}.");

            return;
        }

        $disk->put($coverPath, $coverBinary);

        if ($document->cover_image !== $coverPath) {
            $document->cover_image = $coverPath;
            $document->save();
        }
    }

    private function buildCoverBinary(string $diskName, string $filePath, ?string $mimeType): ?string
    {
        $fileContent = Storage::disk($diskName)->get($filePath);
        if (! $fileContent) {
            return null;
        }

        if ($mimeType && str_contains($mimeType, 'image')) {
            return $this->makeSquarePng($fileContent);
        }

        $converted = $this->libreOffice->convertToPng($fileContent, basename($filePath));
        if (! $converted) {
            return null;
        }

        return $this->makeSquarePng($converted);
    }

    private function makeSquarePng(string $binary): ?string
    {
        if (! function_exists('imagecreatefromstring')) {
            Log::error('Cover generation failed: GD extension is not available.');

            return null;
        }

        $image = @imagecreatefromstring($binary);
        if (! $image) {
            return null;
        }

        $width = imagesx($image);
        $height = imagesy($image);
        $size = min($width, $height);
        $srcX = (int) floor(($width - $size) / 2);
        $srcY = (int) floor(($height - $size) / 2);

        $cropped = imagecrop($image, [
            'x' => $srcX,
            'y' => $srcY,
            'width' => $size,
            'height' => $size,
        ]) ?: $image;

        $resized = imagescale($cropped, self::COVER_SIZE, self::COVER_SIZE, IMG_BICUBIC);
        if (! $resized) {
            if ($cropped !== $image) {
                imagedestroy($cropped);
            }
            imagedestroy($image);

            return null;
        }

        ob_start();
        imagepng($resized);
        $png = ob_get_clean();

        if ($cropped !== $image) {
            imagedestroy($cropped);
        }
        imagedestroy($image);
        imagedestroy($resized);

        return $png ?: null;
    }

    private function coverPath(Document $document): string
    {
        $slug = $document->slug ?: Str::slug($document->title ?? 'document');

        return "document-covers/{$document->id}-{$slug}.png";
    }
}
