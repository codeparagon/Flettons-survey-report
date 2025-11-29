<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'option_type_id',
        'value',
        'scope_type',
        'scope_id',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'scope_id' => 'integer',
    ];

    public function optionType()
    {
        return $this->belongsTo(SurveyOptionType::class, 'option_type_id');
    }
}


