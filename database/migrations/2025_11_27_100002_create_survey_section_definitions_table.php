<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_section_definitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subcategory_id')->constrained('survey_subcategories')->onDelete('cascade')->comment('FK to survey_subcategories');
            $table->string('name', 100)->comment('Unique identifier key within subcategory');
            $table->string('display_name', 150)->comment('Human-readable display name');
            $table->boolean('is_clonable')->default(true)->comment('Whether this section can be cloned/duplicated');
            $table->unsignedInteger('max_clones')->nullable()->comment('Maximum number of clones allowed (null = unlimited)');
            $table->unsignedInteger('sort_order')->default(0)->comment('Display order for sorting within subcategory');
            $table->boolean('is_active')->default(true)->comment('Whether this section definition is currently active');
            $table->timestamps();
            
            $table->index(['subcategory_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_section_definitions');
    }
};

