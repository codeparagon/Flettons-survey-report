<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('survey_section_assessments')) {
            return;
        }

        Schema::table('survey_section_assessments', function (Blueprint $table) {
            if (!Schema::hasColumn('survey_section_assessments', 'report_content')) {
                $table->text('report_content')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('survey_section_assessments', 'material')) {
                $table->string('material', 255)->nullable()->after('report_content');
            }
            if (!Schema::hasColumn('survey_section_assessments', 'defects')) {
                $table->json('defects')->nullable()->after('material');
            }
            if (!Schema::hasColumn('survey_section_assessments', 'remaining_life')) {
                $table->string('remaining_life', 50)->nullable()->after('defects');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('survey_section_assessments')) {
            return;
        }

        Schema::table('survey_section_assessments', function (Blueprint $table) {
            if (Schema::hasColumn('survey_section_assessments', 'remaining_life')) {
                $table->dropColumn('remaining_life');
            }
            if (Schema::hasColumn('survey_section_assessments', 'defects')) {
                $table->dropColumn('defects');
            }
            if (Schema::hasColumn('survey_section_assessments', 'material')) {
                $table->dropColumn('material');
            }
            if (Schema::hasColumn('survey_section_assessments', 'report_content')) {
                $table->dropColumn('report_content');
            }
        });
    }
};



