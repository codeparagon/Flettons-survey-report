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
        Schema::create('section_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_section_id')->constrained('survey_sections')->onDelete('cascade');
            $table->string('field_key'); // Internal identifier like 'defects_noted', 'inspection_date'
            $table->string('field_label'); // Display label
            $table->enum('field_type', ['textarea', 'date', 'numeric', 'dropdown', 'single-text', 'rating']);
            $table->integer('field_order')->default(0);
            $table->boolean('is_required')->default(false);
            $table->json('validation_rules')->nullable(); // Laravel validation rules
            $table->json('options')->nullable(); // For dropdowns, option lists
            $table->text('default_value')->nullable();
            $table->text('help_text')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index('survey_section_id');
            $table->index('field_order');
            $table->unique(['survey_section_id', 'field_key']); // Ensure unique field keys per section
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_fields');
    }
};
