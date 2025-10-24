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
        Schema::create('survey_level_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_level_id')->constrained('survey_levels')->onDelete('cascade');
            $table->foreignId('survey_section_id')->constrained('survey_sections')->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->unique(['survey_level_id', 'survey_section_id']);
            $table->index(['survey_level_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_level_sections');
    }
};
