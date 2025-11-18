<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyNote extends Model
{
    use HasFactory;
    protected $fillable = [
        'survey_id',
        'note',
        'created_by',
        'dated_at',
    ];
}
