<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_section_required_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_definition_id')->constrained('survey_section_definitions')->onDelete('cascade')->comment('FK to survey_section_definitions');
            $table->foreignId('option_type_id')->constrained('survey_option_types')->onDelete('cascade')->comment('FK to survey_option_types - defines which option types are required for this section');
            $table->timestamps();
            
            $table->unique(['section_definition_id', 'option_type_id'], 'section_def_option_type_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_section_required_fields');
    }
};

