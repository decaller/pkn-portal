<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Analytic extends Model
{
    protected $guarded = [];

    // This allows $analytic->user->name
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function trackable(): MorphTo
    {
        return $this->morphTo();
    }
}
