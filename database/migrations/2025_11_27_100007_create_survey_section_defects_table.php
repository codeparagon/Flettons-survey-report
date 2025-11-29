<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_section_defects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_assessment_id')->constrained('survey_section_assessments')->onDelete('cascade')->comment('FK to survey_section_assessments');
            $table->foreignId('defect_option_id')->constrained('survey_options')->onDelete('cascade')->comment('FK to survey_options where option_type_id = defects option type');
            $table->timestamps();
            
            $table->unique(['section_assessment_id', 'defect_option_id'], 'section_assess_defect_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_section_defects');
    }
};

