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
        Schema::create('survey_accommodation_type_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accommodation_type_id')
                ->constrained('survey_accommodation_types')
                ->onDelete('cascade')
                ->name('acc_type_comp_type_fk');
            $table->foreignId('component_id')
                ->constrained('survey_accommodation_components')
                ->onDelete('cascade')
                ->name('acc_type_comp_comp_fk');
            $table->boolean('is_required')->default(false)->comment('Whether this component is required for this accommodation type');
            $table->unsignedInteger('sort_order')->default(0)->comment('Display order for sorting components within this type');
            $table->timestamps();
            
            // Unique constraint to prevent duplicate component assignments
            $table->unique(['accommodation_type_id', 'component_id'], 'acc_type_comp_unique');
            
            // Index for faster lookups
            $table->index(['accommodation_type_id', 'sort_order'], 'acc_type_sort_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_accommodation_type_components');
    }
};
