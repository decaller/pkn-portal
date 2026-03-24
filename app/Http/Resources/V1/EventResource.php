<?php

namespace App\Http\Resources\V1;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @property-read Event $resource
 */
class EventResource extends JsonResource
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
            'summary' => $this->resource->summary,
            'description' => $this->resource->description,
            'event_date' => $this->resource->event_date?->format('Y-m-d'),
            'city' => $this->resource->city,
            'province' => $this->resource->province,
            'nation' => $this->resource->nation,
            'duration_days' => $this->resource->duration_days,
            'google_maps_url' => $this->resource->google_maps_url,
            'cover_image' => $this->absoluteUrl($this->resource->cover_image),
            'photos' => collect($this->resource->photos ?? [])
                ->map(fn (string $photo) => $this->absoluteUrl($photo))
                ->toArray(),
            'files' => collect($this->resource->files ?? [])
                ->map(fn (string $file) => $this->absoluteUrl($file))
                ->toArray(),
            'documentation' => collect($this->resource->documentation ?? [])
                ->map(fn (string $doc) => $this->absoluteUrl($doc))
                ->toArray(),
            'is_published' => $this->resource->is_published,
            'allow_registration' => $this->resource->allow_registration,
            'max_capacity' => $this->resource->max_capacity,
            'available_spots' => $this->resource->availableSpots(),
            'is_full' => $this->resource->isFull(),
            'registration_packages' => collect($this->resource->registration_packages ?? [])
                ->map(fn (array $package, int $index) => array_merge($package, [
                    'id' => $package['id'] ?? ($index + 1),
                ]))
                ->toArray(),
            'rundown' => $this->resource->rundown,
            'tags' => $this->resource->tags,
            'proposal' => $this->absoluteUrl($this->resource->proposal),
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
            'event_type' => $this->resource->event_type?->value ?? $this->resource->event_type,
            'survey_template_id' => $this->resource->survey_template_id,
            'place' => $this->resource->place,
            'payment_instructions' => $this->resource->payment_instructions,
            'testimonials' => TestimonialResource::collection($this->whenLoaded('approvedTestimonials')),
        ];
    }
}
