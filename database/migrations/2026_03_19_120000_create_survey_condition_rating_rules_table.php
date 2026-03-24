<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_condition_rating_rules', function (Blueprint $table) {
            $table->id();

            // Which form field the rule applies to
            $table->enum('option_type', ['material', 'defects'])->comment('Rule applies to material or defects option values');

            // Store normalized option value (trimmed, lowercased) to make matching case-insensitive.
            $table->string('option_value', 150)->comment('Normalized option value (trimmed, lowercased)');

            // 1|2|3 => condition rating; null => Not Inspected (NI)
            $table->unsignedTinyInteger('condition_rating')->nullable()->comment('1/2/3 or null for NI');

            $table->timestamps();

            $table->unique(['option_type', 'option_value'], 'scrules_option_type_value_unique');
            $table->index(['option_type', 'condition_rating'], 'scrules_type_rating_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_condition_rating_rules');
    }
};

