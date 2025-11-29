<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('survey_categories')->onDelete('cascade')->comment('FK to survey_categories');
            $table->string('name', 100)->comment('Unique identifier key within category (e.g., roofing, chimneys)');
            $table->string('display_name', 150)->comment('Human-readable display name');
            $table->unsignedInteger('sort_order')->default(0)->comment('Display order for sorting within category');
            $table->boolean('is_active')->default(true)->comment('Whether this subcategory is currently active');
            $table->timestamps();
            
            $table->unique(['category_id', 'name']);
            $table->index(['category_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_subcategories');
    }
};

