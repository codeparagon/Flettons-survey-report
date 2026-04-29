<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('survey_accommodation_assessments', function (Blueprint $table) {
            $table->foreignId('location_id')
                ->nullable()
                ->after('notes')
                ->comment('FK to survey_accommodation_options (option type location, global scope)');
            $table->foreign('location_id', 'acc_assess_location_fk')
                ->references('id')
                ->on('survey_accommodation_options')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('survey_accommodation_assessments', function (Blueprint $table) {
            $table->dropForeign('acc_assess_location_fk');
            $table->dropColumn('location_id');
        });
    }
};
