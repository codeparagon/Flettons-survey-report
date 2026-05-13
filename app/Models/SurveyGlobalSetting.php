<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyGlobalSetting extends Model
{
    public const KEY_ACCOMMODATION_COMPONENTS_CATEGORY_ID = 'accommodation_components_category_id';

    protected $table = 'survey_global_settings';

    protected $fillable = [
        'key',
        'value',
    ];

    public static function getValue(string $key): ?string
    {
        $row = static::query()->where('key', $key)->first();

        return $row?->value;
    }

    public static function setValue(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
