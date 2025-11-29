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
            $table->unsignedTinyInteger('condition_rating')->nullable()->after('notes')->comment('Condition rating: 1=Excellent, 2=Good, 3=Fair/Poor, null=Not Inspected');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survey_accommodation_assessments', function (Blueprint $table) {
            $table->dropColumn('condition_rating');
        });
    }
};
