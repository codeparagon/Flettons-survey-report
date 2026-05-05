<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyAccommodationGptOutput extends Model
{
    protected $fillable = [
        'survey_id',
        'accommodation_type_id',
        'narrative',
        'observations',
    ];

    protected $casts = [
        'observations' => 'array',
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }

    public function accommodationType(): BelongsTo
    {
        return $this->belongsTo(SurveyAccommodationType::class, 'accommodation_type_id');
    }
}
