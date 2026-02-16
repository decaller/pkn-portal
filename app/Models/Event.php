<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Event extends Model
{
    use HasFactory;

    // 1. Allow mass assignment
    protected $guarded = [];

    protected $casts = [
        'event_date' => 'date',
        'is_published' => 'boolean',
    ];

    // 2. Link to Analytics (Polymorphic)
    public function analytics(): MorphMany
    {
        return $this->morphMany(Analytic::class, 'trackable');
    }

    // 3. Helper: Get the full MinIO path
    // Usage: $event->getStoragePath() -> "events/graduation-2026"
    public function getStoragePath(): string
    {
        return 'events/' . $this->slug;
    }
}