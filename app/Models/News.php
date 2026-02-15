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
    ];
    // This allows $news->analytics
    public function analytics(): MorphMany
    {
        return $this->morphMany(Analytic::class, 'trackable');
    }
}
