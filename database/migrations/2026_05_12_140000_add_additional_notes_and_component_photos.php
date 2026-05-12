<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('survey_accommodation_component_assessments', function (Blueprint $table) {
            $table->text('additional_notes')->nullable()->after('gpt_observations');
        });

        Schema::table('survey_accommodation_photos', function (Blueprint $table) {
            $table->foreignId('component_assessment_id')
                ->nullable()
                ->after('accommodation_assessment_id')
                ->constrained('survey_accommodation_component_assessments')
                ->cascadeOnDelete();
        });

        // Attach legacy room-level photos to the first component row for each accommodation assessment.
        $photoIds = DB::table('survey_accommodation_photos')
            ->whereNull('component_assessment_id')
            ->pluck('id');

        foreach ($photoIds as $photoId) {
            $row = DB::table('survey_accommodation_photos')->where('id', $photoId)->first();
            if (! $row) {
                continue;
            }
            $firstComp = DB::table('survey_accommodation_component_assessments')
                ->where('accommodation_assessment_id', $row->accommodation_assessment_id)
                ->orderBy('id')
                ->first();
            if ($firstComp) {
                DB::table('survey_accommodation_photos')
                    ->where('id', $photoId)
                    ->update(['component_assessment_id' => $firstComp->id]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('survey_accommodation_photos', function (Blueprint $table) {
            $table->dropForeign(['component_assessment_id']);
            $table->dropColumn('component_assessment_id');
        });

        Schema::table('survey_accommodation_component_assessments', function (Blueprint $table) {
            $table->dropColumn('additional_notes');
        });
    }
};
