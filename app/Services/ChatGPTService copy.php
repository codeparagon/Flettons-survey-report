<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ChatGPTService
{
    /**
     * Generate report content from section assessment data.
     * 
     * This is a dummy function that will be replaced with actual ChatGPT API integration.
     * 
     * @param array $assessmentData Formatted assessment data
     * @param string $sectionName Section name for context
     * @param string $categoryName Category name for context
     * @return string Generated report content
     * @throws \Exception If report generation fails
     */
    public function generateReport(array $assessmentData, string $sectionName, string $categoryName): string
    {
        try {
            // TODO: Replace with actual ChatGPT API call
            // For now, generate a mock report based on the assessment data
            
            $location = $assessmentData['location'] ?? 'the property';
            $structure = $assessmentData['structure'] ?? 'standard';
            $material = $assessmentData['material'] ?? 'standard materials';
            $defects = $assessmentData['defects'] ?? [];
            $remainingLife = $assessmentData['remaining_life'] ?? 'not specified';
            $notes = $assessmentData['notes'] ?? '';
            $costs = $assessmentData['costs'] ?? [];
            
            // Build report content
            $report = "**{$sectionName}**\n\n";
            $report .= "The {$sectionName} at {$location} has been inspected and assessed. ";
            $report .= "The structure is of {$structure} construction with {$material} materials. ";
            
            // Add defects information
            if (!empty($defects) && !in_array('None', $defects)) {
                $defectsList = implode(', ', $defects);
                $report .= "During the inspection, the following defects were identified: {$defectsList}. ";
            } else {
                $report .= "The inspection revealed no significant defects. ";
            }
            
            // Add remaining life
            if ($remainingLife !== 'not specified' && $remainingLife !== '') {
                $report .= "The estimated remaining life is {$remainingLife} years. ";
            }
            
            // Add notes if present
            if (!empty($notes)) {
                $report .= "\n\n**Additional Notes:**\n{$notes}\n";
            }
            
            // Add costs if present
            if (!empty($costs)) {
                $report .= "\n\n**Estimated Costs:**\n";
                foreach ($costs as $cost) {
                    $due = $cost['due'] ?? 'Not specified';
                    $amount = $cost['cost'] ?? '0.00';
                    $report .= "- {$cost['category']}: {$cost['description']} - Due {$due} - £{$amount}\n";
                }
            }
            
            // Add recommendations
            $report .= "\n\n**Recommendations:**\n";
            if (!empty($defects) && !in_array('None', $defects)) {
                $report .= "It is recommended that the identified defects be addressed in a timely manner to prevent further deterioration. ";
                $report .= "Regular maintenance and monitoring of the {$sectionName} is advised. ";
            } else {
                $report .= "The {$sectionName} is in good condition. Regular maintenance is recommended to maintain its current state. ";
            }
            $report .= "Any necessary repairs should be carried out by qualified professionals in accordance with current building regulations.";
            
            // Log the report generation (for debugging)
            Log::info('ChatGPT report generated', [
                'section' => $sectionName,
                'category' => $categoryName,
            ]);
            
            return $report;
            
        } catch (\Exception $e) {
            Log::error('ChatGPT report generation failed', [
                'error' => $e->getMessage(),
                'section' => $sectionName,
                'category' => $categoryName,
            ]);
            
            throw new \Exception('Failed to generate report: ' . $e->getMessage());
        }
    }

    /**
     * Generate report content from accommodation assessment data.
     * 
     * This is a dummy function that will be replaced with actual ChatGPT API integration.
     * 
     * @param array $assessmentData Formatted assessment data
     * @param string $accommodationName Accommodation name for context
     * @return string Generated report content
     * @throws \Exception If report generation fails
     */
    public function generateAccommodationReport(array $assessmentData, string $accommodationName): string
    {
        try {
            // TODO: Replace with actual ChatGPT API call
            // For now, generate a mock report based on the assessment data
            
            $accommodationType = $assessmentData['accommodation_type'] ?? 'accommodation';
            $components = $assessmentData['components'] ?? [];
            $notes = $assessmentData['notes'] ?? '';
            
            // Build report content
            $report = "**{$accommodationName}**\n\n";
            $report .= "The {$accommodationName} ({$accommodationType}) has been inspected and assessed. ";
            
            // Add components information
            if (!empty($components)) {
                $report .= "\n\n**Component Assessment:**\n";
                foreach ($components as $component) {
                    $componentName = $component['component'] ?? 'Unknown';
                    $material = $component['material'] ?? 'not specified';
                    $defects = $component['defects'] ?? [];
                    
                    $report .= "\n**{$componentName}:**\n";
                    $report .= "- Material: {$material}\n";
                    
                    if (!empty($defects) && !in_array('None', $defects) && !in_array('No Defects', $defects)) {
                        $defectsList = implode(', ', $defects);
                        $report .= "- Defects identified: {$defectsList}\n";
                    } else {
                        $report .= "- No significant defects identified\n";
                    }
                }
            }
            
            // Add notes if present
            if (!empty($notes)) {
                $report .= "\n\n**Additional Notes:**\n{$notes}\n";
            }
            
            // Add recommendations
            $report .= "\n\n**Recommendations:**\n";
            $hasDefects = false;
            foreach ($components as $component) {
                $defects = $component['defects'] ?? [];
                if (!empty($defects) && !in_array('None', $defects) && !in_array('No Defects', $defects)) {
                    $hasDefects = true;
                    break;
                }
            }
            
            if ($hasDefects) {
                $report .= "It is recommended that the identified defects be addressed in a timely manner to prevent further deterioration. ";
                $report .= "Regular maintenance and monitoring of the {$accommodationName} is advised. ";
            } else {
                $report .= "The {$accommodationName} is in good condition. Regular maintenance is recommended to maintain its current state. ";
            }
            $report .= "Any necessary repairs should be carried out by qualified professionals in accordance with current building regulations.";
            
            // Log the report generation (for debugging)
            Log::info('ChatGPT accommodation report generated', [
                'accommodation' => $accommodationName,
                'type' => $accommodationType,
            ]);
            
            return $report;
            
        } catch (\Exception $e) {
            Log::error('ChatGPT accommodation report generation failed', [
                'error' => $e->getMessage(),
                'accommodation' => $accommodationName,
            ]);
            
            throw new \Exception('Failed to generate accommodation report: ' . $e->getMessage());
        }
    }
}


