<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('survey_sections')) {
            return;
        }

        Schema::table('survey_sections', function (Blueprint $table) {
            if (!Schema::hasColumn('survey_sections', 'generation_method')) {
                $table->string('generation_method', 32)->default('database')->after('is_active');
            }
            if (!Schema::hasColumn('survey_sections', 'field_config')) {
                $table->json('field_config')->nullable()->after('generation_method');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('survey_sections')) {
            return;
        }

        Schema::table('survey_sections', function (Blueprint $table) {
            if (Schema::hasColumn('survey_sections', 'field_config')) {
                $table->dropColumn('field_config');
            }
            if (Schema::hasColumn('survey_sections', 'generation_method')) {
                $table->dropColumn('generation_method');
            }
        });
    }
};


