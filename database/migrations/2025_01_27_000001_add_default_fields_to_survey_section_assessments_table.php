<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('survey_section_assessments')) {
            Schema::table('survey_section_assessments', function (Blueprint $table) {
                $table->text('report_content')->nullable()->after('notes');
                $table->string('material', 255)->nullable()->after('report_content');
                $table->json('defects')->nullable()->after('material');
                $table->string('remaining_life', 50)->nullable()->after('defects');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('survey_section_assessments')) {
            Schema::table('survey_section_assessments', function (Blueprint $table) {
                $table->dropColumn(['report_content', 'material', 'defects', 'remaining_life']);
            });
        }
    }
};
