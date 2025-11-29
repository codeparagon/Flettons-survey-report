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
            $table->foreignId('survey_id')->constrained('surveys')->onDelete('cascade')->comment('FK to surveys');
            $table->foreignId('section_definition_id')->constrained('survey_section_definitions')->onDelete('restrict')->comment('FK to survey_section_definitions');
            $table->unsignedTinyInteger('condition_rating')->nullable()->comment('Condition rating: 1=Excellent, 2=Good, 3=Fair/Poor, null=Not Inspected');
            
            // Option selections (foreign keys to survey_options)
            $table->foreignId('section_type_id')->nullable()->constrained('survey_options')->onDelete('set null')->comment('FK to survey_options where option_type_id = section_type option type');
            $table->foreignId('location_id')->nullable()->constrained('survey_options')->onDelete('set null')->comment('FK to survey_options where option_type_id = location option type');
            $table->foreignId('structure_id')->nullable()->constrained('survey_options')->onDelete('set null')->comment('FK to survey_options where option_type_id = structure option type');
            $table->foreignId('material_id')->nullable()->constrained('survey_options')->onDelete('set null')->comment('FK to survey_options where option_type_id = material option type');
            $table->foreignId('remaining_life_id')->nullable()->constrained('survey_options')->onDelete('set null')->comment('FK to survey_options where option_type_id = remaining_life option type');
            
            $table->text('notes')->nullable()->comment('Additional notes for this section assessment');
            $table->text('report_content')->nullable()->comment('Generated report content for this section');
            
            // Clone tracking
            $table->boolean('is_clone')->default(false)->comment('Whether this assessment is a clone of another');
            $table->foreignId('cloned_from_id')->nullable()->constrained('survey_section_assessments')->onDelete('set null')->comment('FK to parent survey_section_assessment if this is a clone');
            $table->unsignedInteger('clone_index')->default(0)->comment('Index number for cloned sections (0 = original, 1+ = clones)');
            
            $table->boolean('is_completed')->default(false)->comment('Whether this assessment is completed');
            $table->timestamp('completed_at')->nullable()->comment('Timestamp when assessment was completed');
            $table->timestamps();
            
            $table->index(['survey_id', 'section_definition_id'], 'section_assess_survey_def_idx');
            $table->index(['survey_id', 'is_completed'], 'section_assess_survey_completed_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_section_assessments');
    }
};

