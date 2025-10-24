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
        Schema::table('survey_sections', function (Blueprint $table) {
            // Drop the old foreign key and index
            $table->dropForeign(['category_id']);
            $table->dropIndex(['category_id']);
            
            // Add the new column with correct naming convention
            $table->unsignedBigInteger('survey_category_id')->nullable()->after('display_name');
            $table->foreign('survey_category_id')->references('id')->on('survey_categories')->onDelete('set null');
            $table->index('survey_category_id');
            
            // Drop the old column
            $table->dropColumn('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survey_sections', function (Blueprint $table) {
            // Drop the new foreign key and index
            $table->dropForeign(['survey_category_id']);
            $table->dropIndex(['survey_category_id']);
            
            // Add back the old column
            $table->unsignedBigInteger('category_id')->nullable()->after('display_name');
            $table->foreign('category_id')->references('id')->on('survey_categories')->onDelete('set null');
            $table->index('category_id');
            
            // Drop the new column
            $table->dropColumn('survey_category_id');
        });
    }
};