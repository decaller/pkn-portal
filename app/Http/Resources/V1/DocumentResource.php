<?php

namespace App\Http\Resources\V1;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @property-read Document $resource
 */
class DocumentResource extends JsonResource
{
    private function absoluteUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return url(Storage::url($path));
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (! $this->resource) {
            return [];
        }

        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'slug' => $this->resource->slug,
            'file_url' => $this->absoluteUrl($this->resource->file_path),
            'original_filename' => $this->resource->original_filename,
            'cover_image' => $this->absoluteUrl($this->resource->cover_image),
            'mime_type' => $this->resource->mime_type,
            'description' => $this->resource->description,
            'tags' => $this->resource->tags ?? [],
            'is_active' => $this->resource->is_active,
            'is_featured' => $this->resource->is_featured,
            'event_id' => $this->resource->event_id,
            'created_at' => $this->resource->created_at?->toIso8601String(),
        ];
    }
}
