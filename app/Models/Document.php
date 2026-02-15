<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Analytic; // Import the Analytic model
use App\Models\Event;    // Import the Event model
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
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