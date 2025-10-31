<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Check table and column existence before migrating
        if (Schema::hasTable('survey_sections') && Schema::hasColumn('survey_sections', 'ai_prompt_template')) {
            // First, migrate existing ai_prompt_template data to field_config
            $sections = DB::table('survey_sections')
                ->whereNotNull('ai_prompt_template')
                ->get();
            
            foreach ($sections as $section) {
                $fieldConfig = json_decode($section->field_config, true) ?? [];
                if (!empty($section->ai_prompt_template)) {
                    $fieldConfig['ai_prompt_template'] = $section->ai_prompt_template;
                }
                
                DB::table('survey_sections')
                    ->where('id', $section->id)
                    ->update([
                        'field_config' => json_encode($fieldConfig)
                    ]);
            }
            
            // Now drop the ai_prompt_template column
            Schema::table('survey_sections', function (Blueprint $table) {
                $table->dropColumn('ai_prompt_template');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('survey_sections') && !Schema::hasColumn('survey_sections', 'ai_prompt_template')) {
            Schema::table('survey_sections', function (Blueprint $table) {
                $table->text('ai_prompt_template')->nullable()->after('field_config');
            });
            
            // Migrate data back from field_config
            $sections = DB::table('survey_sections')
                ->whereNotNull('field_config')
                ->get();
            
            foreach ($sections as $section) {
                $fieldConfig = json_decode($section->field_config, true) ?? [];
                if (isset($fieldConfig['ai_prompt_template'])) {
                    DB::table('survey_sections')
                        ->where('id', $section->id)
                        ->update([
                            'ai_prompt_template' => $fieldConfig['ai_prompt_template']
                        ]);
                }
            }
        }
    }
};
