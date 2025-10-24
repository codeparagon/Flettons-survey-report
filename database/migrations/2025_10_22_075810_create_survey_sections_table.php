<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_sections', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., 'roofs', 'walls', 'floors'
            $table->string('display_name'); // e.g., 'Roofs', 'Walls', 'Floors'
            $table->string('icon')->nullable(); // Icon filename or class
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0); // For ordering in UI
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_sections');
    }
};