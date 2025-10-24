<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_section_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->onDelete('cascade');
            $table->foreignId('survey_section_id')->constrained()->onDelete('cascade');
            
            // Assessment data
            $table->string('condition_rating')->nullable(); // excellent, good, fair, poor
            $table->text('defects_noted')->nullable();
            $table->text('recommendations')->nullable();
            $table->text('notes')->nullable();
            
            // Completion status
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Images/photos
            $table->json('photos')->nullable(); // Array of photo paths
            
            // Additional fields for specific sections
            $table->json('additional_data')->nullable(); // Flexible JSON for section-specific data
            
            $table->timestamps();
            
            // Ensure one assessment per survey per section
            $table->unique(['survey_id', 'survey_section_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_section_assessments');
    }
};