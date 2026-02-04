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
        Schema::create('survey_level_accommodation_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_level_id')->constrained('survey_levels')->onDelete('cascade');
            $table->foreignId('accommodation_type_id')->constrained('survey_accommodation_types')->onDelete('cascade');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['survey_level_id', 'accommodation_type_id'], 'level_accommodation_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_level_accommodation_types');
    }
};
