<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('survey_accommodation_assessments', function (Blueprint $table) {
            $table->text('report_content')->nullable()->after('notes')->comment('Generated report content for this accommodation assessment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survey_accommodation_assessments', function (Blueprint $table) {
            $table->dropColumn('report_content');
        });
    }
};




