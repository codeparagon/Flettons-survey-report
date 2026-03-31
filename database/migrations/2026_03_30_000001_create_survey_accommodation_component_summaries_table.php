<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_accommodation_component_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->comment('FK to surveys');
            $table->foreignId('accommodation_type_id')->comment('FK to survey_accommodation_types');
            $table->foreignId('component_id')->comment('FK to survey_accommodation_components');
            $table->longText('content')->nullable();
            $table->string('input_hash', 64)->nullable();
            $table->timestamps();

            $table->foreign('survey_id', 'acc_comp_sum_survey_fk')
                ->references('id')->on('surveys')->onDelete('cascade');
            $table->foreign('accommodation_type_id', 'acc_comp_sum_type_fk')
                ->references('id')->on('survey_accommodation_types')->onDelete('cascade');
            $table->foreign('component_id', 'acc_comp_sum_component_fk')
                ->references('id')->on('survey_accommodation_components')->onDelete('cascade');

            $table->unique(['survey_id', 'accommodation_type_id', 'component_id'], 'acc_comp_sum_unique');
            $table->index(['survey_id', 'accommodation_type_id'], 'acc_comp_sum_survey_type_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_accommodation_component_summaries');
    }
};

