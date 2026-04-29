<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('survey_accommodation_component_assessments', function (Blueprint $table) {
            if (Schema::hasColumn('survey_accommodation_component_assessments', 'location_id')) {
                return;
            }

            $table->foreignId('location_id')
                ->nullable()
                ->after('material_id')
                ->comment('FK to survey_accommodation_options (option type location; global + optional component scope)');

            $table->foreign('location_id', 'acc_comp_assess_location_fk')
                ->references('id')
                ->on('survey_accommodation_options')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('survey_accommodation_component_assessments', function (Blueprint $table) {
            if (!Schema::hasColumn('survey_accommodation_component_assessments', 'location_id')) {
                return;
            }
            $table->dropForeign('acc_comp_assess_location_fk');
            $table->dropColumn('location_id');
        });
    }
};

