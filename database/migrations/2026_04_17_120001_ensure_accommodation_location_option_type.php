<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('survey_accommodation_option_types')
            ->where('key_name', 'location')
            ->exists();

        if (! $exists) {
            $maxSort = (int) DB::table('survey_accommodation_option_types')->max('sort_order');
            DB::table('survey_accommodation_option_types')->insert([
                'key_name' => 'location',
                'label' => 'Location',
                'is_multiple' => false,
                'sort_order' => $maxSort + 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        $typeId = DB::table('survey_accommodation_option_types')
            ->where('key_name', 'location')
            ->value('id');

        if ($typeId) {
            DB::table('survey_accommodation_options')
                ->where('option_type_id', $typeId)
                ->delete();
            DB::table('survey_accommodation_option_types')
                ->where('id', $typeId)
                ->delete();
        }
    }
};
