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
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'slug' => $this->resource->slug,
            'file_url' => $this->resource->file_path ? Storage::url($this->resource->file_path) : null,
            'original_filename' => $this->resource->original_filename,
            'cover_image' => $this->resource->cover_image ? Storage::url($this->resource->cover_image) : null,
            'mime_type' => $this->resource->mime_type,
            'description' => $this->resource->description,
            'tags' => $this->resource->tags,
            'is_active' => $this->resource->is_active,
            'event_id' => $this->resource->event_id,
        ];
    }
}
