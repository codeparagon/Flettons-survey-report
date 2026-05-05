<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('survey_accommodation_component_assessments', function (Blueprint $table) {
            if (! Schema::hasColumn('survey_accommodation_component_assessments', 'gpt_observations')) {
                $table->json('gpt_observations')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('survey_accommodation_component_assessments', function (Blueprint $table) {
            if (Schema::hasColumn('survey_accommodation_component_assessments', 'gpt_observations')) {
                $table->dropColumn('gpt_observations');
            }
        });
    }
};
