<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('survey_accommodation_types', function (Blueprint $table) {
            $table->boolean('counts_toward_property')
                ->default(false)
                ->after('is_active')
                ->comment('When true, this type appears in survey property counts for its survey levels');
        });

        Schema::table('surveys', function (Blueprint $table) {
            $table->json('property_accommodation_counts')
                ->nullable()
                ->after('bathrooms')
                ->comment('Counts keyed by accommodation type id for property summary');
        });

        // Sensible defaults for common seeded types
        DB::table('survey_accommodation_types')
            ->whereIn('key_name', ['bedroom', 'bathroom', 'living_room'])
            ->update(['counts_toward_property' => true]);
    }

    public function down(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropColumn('property_accommodation_counts');
        });

        Schema::table('survey_accommodation_types', function (Blueprint $table) {
            $table->dropColumn('counts_toward_property');
        });
    }
};
