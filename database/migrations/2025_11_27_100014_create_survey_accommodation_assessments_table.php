<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_accommodation_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->comment('FK to surveys');
            $table->foreignId('accommodation_type_id')->comment('FK to survey_accommodation_types');
            $table->string('custom_name', 150)->nullable()->comment('User-defined custom name for this accommodation (e.g., Bedroom 1)');
            $table->unsignedInteger('clone_index')->default(0)->comment('Index number for cloned accommodations (0 = original, 1+ = clones)');
            $table->text('notes')->nullable()->comment('Additional notes for this accommodation');
            $table->boolean('is_completed')->default(false)->comment('Whether this accommodation assessment is completed');
            $table->timestamp('completed_at')->nullable()->comment('Timestamp when assessment was completed');
            $table->timestamps();
            
            $table->foreign('survey_id', 'acc_assess_survey_fk')
                ->references('id')->on('surveys')->onDelete('cascade');
            $table->foreign('accommodation_type_id', 'acc_assess_type_fk')
                ->references('id')->on('survey_accommodation_types')->onDelete('restrict');
            
            $table->index(['survey_id', 'accommodation_type_id'], 'acc_assess_survey_type_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_accommodation_assessments');
    }
};

