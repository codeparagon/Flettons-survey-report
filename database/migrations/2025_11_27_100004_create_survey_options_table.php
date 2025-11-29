<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('option_type_id')->constrained('survey_option_types')->onDelete('cascade')->comment('FK to survey_option_types');
            $table->string('value', 150)->comment('The option value (e.g., Main Roof, Front, Pitched, Slate)');
            $table->enum('scope_type', ['global', 'category', 'subcategory', 'section'])->default('global')->comment('Scope type: global (all), category (specific category), subcategory (specific subcategory), or section (specific section)');
            $table->unsignedBigInteger('scope_id')->nullable()->comment('FK ID depends on scope_type: null for global, category_id for category, subcategory_id for subcategory, section_definition_id for section');
            $table->unsignedInteger('sort_order')->default(0)->comment('Display order for sorting');
            $table->boolean('is_active')->default(true)->comment('Whether this option is currently active');
            $table->timestamps();
            
            $table->index(['option_type_id', 'scope_type', 'scope_id'], 'survey_options_scope_idx');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_options');
    }
};

