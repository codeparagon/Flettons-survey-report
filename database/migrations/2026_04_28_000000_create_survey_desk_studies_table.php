<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('survey_desk_studies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->unique()->constrained()->cascadeOnDelete();

            $table->string('address')->nullable();
            $table->string('job_reference')->nullable();

            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->string('map_image_path')->nullable();

            $table->json('flood_risks')->nullable();
            $table->json('planning')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_desk_studies');
    }
};

