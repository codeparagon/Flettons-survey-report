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
        Schema::create('survey_content_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->comment('Section title');
            $table->longText('content')->comment('Section content/text');
            $table->foreignId('category_id')->nullable()->constrained('survey_categories')->onDelete('set null')->comment('FK to survey_categories (optional)');
            $table->foreignId('subcategory_id')->nullable()->constrained('survey_subcategories')->onDelete('set null')->comment('FK to survey_subcategories (optional)');
            $table->json('tags')->nullable()->comment('Metadata/tags as JSON array');
            $table->unsignedInteger('sort_order')->default(0)->comment('Display order for sorting');
            $table->boolean('is_active')->default(true)->comment('Whether this section is currently active');
            $table->timestamps();
            
            $table->index(['category_id', 'sort_order']);
            $table->index(['subcategory_id', 'sort_order']);
            $table->index(['is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_content_sections');
    }
};
