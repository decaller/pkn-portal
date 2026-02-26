<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class News extends Model
{

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

    public function event(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    // This allows $news->analytics
    public function analytics(): MorphMany
    {
        return $this->morphMany(Analytic::class, 'trackable');
    }
}
