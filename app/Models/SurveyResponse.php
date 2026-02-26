<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyResponse extends Model
{
    protected $guarded = [];

    protected $casts = [
        'answers' => 'array',
    ];

    public function template()
    {
        return $this->belongsTo(SurveyTemplate::class, 'survey_template_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
