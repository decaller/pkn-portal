<?php

namespace App\Models;

use App\Enums\EventType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Validation\ValidationException;

class Event extends Model
{
    use HasFactory;

    // 1. Allow mass assignment
    protected $guarded = [];

    protected $casts = [
        "event_date" => "date",
        "event_type" => EventType::class,
        "is_published" => "boolean",
        "allow_registration" => "boolean",
        "registration_packages" => "array",
        "photos" => "array",
        "files" => "array",
        "rundown" => "array", // <--- CRITICAL: This makes the JSON Repeater work
    ];

    protected static function booted(): void
    {
        static::saving(function (Event $event): void {
            if (!$event->allow_registration || !$event->event_date) {
                return;
            }

            if ($event->event_date->isBefore(now()->startOfDay())) {
                throw ValidationException::withMessages([
                    "event_date" =>
                        "Event date cannot be in the past when registration is enabled.",
                ]);
            }
        });
    }

    // 2. Link to Analytics (Polymorphic)
    public function analytics(): MorphMany
    {
        return $this->morphMany(Analytic::class, "trackable");
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    // 3. Helper: Get the full MinIO path
    // Usage: $event->getStoragePath() -> "events/graduation-2026"
    public function getStoragePath(): string
    {
        return "events/" . $this->slug;
    }
}
