<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique()->comment('Unique identifier key (e.g., exterior, interior)');
            $table->string('display_name', 150)->comment('Human-readable display name');
            $table->string('icon', 50)->nullable()->comment('Font Awesome icon class name');
            $table->unsignedInteger('sort_order')->default(0)->comment('Display order for sorting');
            $table->boolean('is_active')->default(true)->comment('Whether this category is currently active');
            $table->timestamps();
            
            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_categories');
    }
};

