<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyOptionType extends Model
{
    use HasFactory;

    protected $fillable = [
        'key_name',
        'label',
        'is_multiple',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_multiple' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function options()
    {
        return $this->hasMany(SurveyOption::class, 'option_type_id');
    }
}


