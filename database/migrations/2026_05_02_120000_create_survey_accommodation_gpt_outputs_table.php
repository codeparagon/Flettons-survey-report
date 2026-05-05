<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_accommodation_gpt_outputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('accommodation_type_id');
            $table->foreign('accommodation_type_id')
                ->references('id')
                ->on('survey_accommodation_types')
                ->cascadeOnDelete();
            $table->longText('narrative')->nullable();
            $table->json('observations')->nullable();
            $table->timestamps();

            $table->unique(['survey_id', 'accommodation_type_id'], 'sa_gpt_out_survey_type_uid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_accommodation_gpt_outputs');
    }
};
