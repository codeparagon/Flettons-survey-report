<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_section_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_assessment_id')->constrained('survey_section_assessments')->onDelete('cascade')->comment('FK to survey_section_assessments');
            $table->string('file_path', 500)->comment('Full path to the photo file');
            $table->string('file_name', 255)->comment('Original filename');
            $table->unsignedInteger('file_size')->nullable()->comment('File size in bytes');
            $table->string('mime_type', 100)->nullable()->comment('MIME type of the file (e.g., image/jpeg)');
            $table->unsignedInteger('sort_order')->default(0)->comment('Display order for sorting photos');
            $table->timestamps();
            
            $table->index(['section_assessment_id', 'sort_order'], 'section_photos_assess_sort_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_section_photos');
    }
};

