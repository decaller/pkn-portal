<?php

namespace App\Http\Resources\V1;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Testimonial $resource
 */
class TestimonialResource extends JsonResource
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
            'content' => $this->resource->content,
            'rating' => $this->resource->rating,
            'user' => [
                'name' => $this->resource->user->name ?? $this->resource->guest_name,
            ],
            'event_id' => $this->resource->event_id,
            'is_approved' => $this->resource->is_approved,
        ];
    }
}
