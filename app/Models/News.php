<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class News extends Model
{
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     * This allows Filament to actually save data to these columns.
     */
    protected $fillable = [
        'title',
        'content',
        'thumbnail',
        'is_published',
        'event_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    // This allows $news->analytics
    public function analytics(): MorphMany
    {
        return $this->morphMany(Analytic::class, 'trackable');
    }
}
