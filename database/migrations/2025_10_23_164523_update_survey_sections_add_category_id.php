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
            // First, add the new category_id column
            $table->unsignedBigInteger('category_id')->nullable()->after('display_name');
            $table->foreign('category_id')->references('id')->on('survey_categories')->onDelete('set null');
            $table->index('category_id');
            
            // Remove the old category column
            $table->dropColumn('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survey_sections', function (Blueprint $table) {
            // Add back the old category column
            $table->string('category')->default('exterior')->after('display_name');
            
            // Remove the foreign key and new column
            $table->dropForeign(['category_id']);
            $table->dropIndex(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
