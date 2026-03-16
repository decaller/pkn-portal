<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// Import the Analytic model
// Import the Event model
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Document extends Model
{
    use HasFactory;

    // This line solves the MassAssignmentException
    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function (Document $document) {
            if (empty($document->slug)) {
                $document->slug = Str::slug(($document->title ?? 'doc').'-'.Str::random(5));
            }
        });
    }

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
        'tags' => 'array',
    ];

    /**
     * Link to the Event (The Folder)
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Link to the Universal Analytics
     */
    public function analytics(): MorphMany
    {
        return $this->morphMany(Analytic::class, 'trackable');
    }
}
