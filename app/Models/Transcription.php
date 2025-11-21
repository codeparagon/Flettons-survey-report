<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transcription extends Model
{
    use HasFactory;
    protected $fillable=[
        'survey_id',
        'transcription_text',
        's3_uri',
        'transcription_status',
    ];
}
