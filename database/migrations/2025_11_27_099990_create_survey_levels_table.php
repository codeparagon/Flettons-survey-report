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
        Schema::create('survey_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique()->comment('Unique identifier (e.g., level_1, level_2, level_3)');
            $table->string('display_name', 100)->comment('Human readable name (e.g., Level 1, Level 2, Level 3)');
            $table->text('description')->nullable()->comment('Description of this survey level');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_levels');
    }
};



