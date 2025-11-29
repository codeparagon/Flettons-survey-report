<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_accommodation_component_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accommodation_assessment_id')->comment('FK to survey_accommodation_assessments');
            $table->foreignId('component_id')->comment('FK to survey_accommodation_components');
            $table->foreignId('material_id')->nullable()->comment('FK to survey_accommodation_options where option_type_id = material option type');
            $table->timestamps();
            
            $table->foreign('accommodation_assessment_id', 'acc_comp_assess_fk')
                ->references('id')->on('survey_accommodation_assessments')->onDelete('cascade');
            $table->foreign('component_id', 'acc_comp_component_fk')
                ->references('id')->on('survey_accommodation_components')->onDelete('restrict');
            $table->foreign('material_id', 'acc_comp_material_fk')
                ->references('id')->on('survey_accommodation_options')->onDelete('set null');
            
            $table->unique(['accommodation_assessment_id', 'component_id'], 'acc_assess_component_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_accommodation_component_assessments');
    }
};

