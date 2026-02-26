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
        'event_date' => 'date',
        'event_type' => EventType::class,
        'is_published' => 'boolean',
        'allow_registration' => 'boolean',
        'registration_packages' => 'array',
        'photos' => 'array',
        'files' => 'array',
        'documentation' => 'array',
        'rundown' => 'array', // <--- CRITICAL: This makes the JSON Repeater work
        'tags' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (Event $event): void {
            if (! $event->allow_registration || ! $event->event_date) {
                return;
            }

            if (($event->isDirty('event_date') || $event->isDirty('allow_registration')) 
                && $event->event_date->isBefore(now()->startOfDay())) {
                throw ValidationException::withMessages([
                    'event_date' => 'Event date cannot be in the past when registration is enabled.',
                ]);
            }
        });
    }

    // 2. Link to Analytics (Polymorphic)
    public function analytics(): MorphMany
    {
        return $this->morphMany(Analytic::class, 'trackable');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function surveyTemplate(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SurveyTemplate::class);
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    public function approvedTestimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class)->where('is_approved', true);
    }

    // 3. Helper: Get the full MinIO path
    public function getStoragePath(): string
    {
        return 'events/'.$this->slug;
    }

    public function getApprovedParticipantsCount(): int
    {
        // Total participants inside EventRegistrations that are either Submitted, Verified, or Paid.
        return $this->registrations()
            ->whereIn('payment_status', [\App\Enums\PaymentStatus::Submitted, \App\Enums\PaymentStatus::Verified])
            ->orWhere('status', \App\Enums\RegistrationStatus::Paid)
            ->withCount('participants')
            ->get()
            ->sum('participants_count');
    }

    public function availableSpots(): ?int
    {
        if (is_null($this->max_capacity)) {
            return null; // Unlimited
        }

        $takenSpots = $this->getApprovedParticipantsCount();

        return max(0, $this->max_capacity - $takenSpots);
    }

    public function isFull(): bool
    {
        if (is_null($this->max_capacity)) {
            return false;
        }

        return $this->availableSpots() === 0;
    }
}
