<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_accommodation_component_defects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('component_assessment_id')->comment('FK to survey_accommodation_component_assessments');
            $table->foreignId('defect_option_id')->comment('FK to survey_accommodation_options where option_type_id = defects option type');
            $table->timestamps();
            
            $table->foreign('component_assessment_id', 'acc_comp_defect_assess_fk')
                ->references('id')->on('survey_accommodation_component_assessments')->onDelete('cascade');
            $table->foreign('defect_option_id', 'acc_comp_defect_option_fk')
                ->references('id')->on('survey_accommodation_options')->onDelete('cascade');
            
            $table->unique(['component_assessment_id', 'defect_option_id'], 'comp_assess_defect_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_accommodation_component_defects');
    }
};

