<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyTemplate extends Model
{
    protected $guarded = [];

    protected $casts = [
        'questions' => 'array',
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function responses()
    {
        return $this->hasMany(SurveyResponse::class);
    }
}
