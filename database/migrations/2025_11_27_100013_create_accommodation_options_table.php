<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_accommodation_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('option_type_id')->constrained('survey_accommodation_option_types')->onDelete('cascade')->comment('FK to survey_accommodation_option_types');
            $table->string('value', 150)->comment('The option value (e.g., Plasterboard, Cracks)');
            $table->enum('scope_type', ['global', 'component'])->default('global')->comment('Scope type: global (all components) or component (specific component)');
            $table->unsignedBigInteger('scope_id')->nullable()->comment('FK ID depends on scope_type: null for global, component_id for component scope');
            $table->unsignedInteger('sort_order')->default(0)->comment('Display order for sorting');
            $table->boolean('is_active')->default(true)->comment('Whether this option is currently active');
            $table->timestamps();
            
            $table->index(['option_type_id', 'scope_type', 'scope_id'], 'acc_options_scope_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_accommodation_options');
    }
};

