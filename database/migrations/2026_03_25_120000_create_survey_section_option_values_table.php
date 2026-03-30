<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_section_option_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_assessment_id')
                ->constrained('survey_section_assessments')
                ->cascadeOnDelete();
            $table->foreignId('option_type_id')
                ->constrained('survey_option_types')
                ->cascadeOnDelete();
            $table->foreignId('option_id')
                ->constrained('survey_options')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(
                ['section_assessment_id', 'option_type_id', 'option_id'],
                'section_opt_val_assess_type_opt_unique'
            );
            $table->index(['section_assessment_id', 'option_type_id'], 'section_opt_val_assess_type_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_section_option_values');
    }
};
