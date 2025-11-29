<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_accommodation_types', function (Blueprint $table) {
            $table->id();
            $table->string('key_name', 50)->unique()->comment('Unique identifier key (e.g., bedroom, bathroom)');
            $table->string('display_name', 100)->comment('Human-readable display name');
            $table->unsignedInteger('sort_order')->default(0)->comment('Display order for sorting');
            $table->boolean('is_active')->default(true)->comment('Whether this type is currently active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_accommodation_types');
    }
};

