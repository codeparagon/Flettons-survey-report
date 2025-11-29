<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_section_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_assessment_id')->constrained('survey_section_assessments')->onDelete('cascade')->comment('FK to survey_section_assessments');
            $table->string('category', 100)->comment('Cost category (e.g., Essential, Recommended, Optional)');
            $table->text('description')->comment('Description of the cost item');
            $table->unsignedInteger('due_year')->nullable()->comment('Year when this cost is due');
            $table->decimal('amount', 10, 2)->default(0.00)->comment('Cost amount in currency');
            $table->boolean('is_active')->default(true)->comment('Whether this cost entry is currently active');
            $table->timestamps();
            
            $table->index('section_assessment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_section_costs');
    }
};

