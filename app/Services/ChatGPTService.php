<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ChatGPTService
{
    /**
     * Get the name of the currently logged-in surveyor for report attribution.
     *
     * @return string
     */
    protected function getSurveyorName(): string
    {
        $user = Auth::user();

        return $user && !empty($user->name) ? trim($user->name) : 'Surveyor';
    }

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
            $defects = $assessmentData['defects'] ?? [];
            if (!is_array($defects)) {
                $defects = array_filter([$defects]);
            }

            $costs = $assessmentData['costs'] ?? [];
            $surveyorName = $this->getSurveyorName();

            $sectionPayload = [
                'section'        => $assessmentData['section'] ?? $sectionName,
                'element'        => $assessmentData['element'] ?? $categoryName,
                'location'       => $assessmentData['location'] ?? null,
                'structure'      => $assessmentData['structure'] ?? null,
                'material'       => $assessmentData['material'] ?? null,
                'defects'        => $defects,
                'remaining_life' => $assessmentData['remaining_life'] ?? null,
                'costs'          => $costs,
                'notes'          => $assessmentData['notes'] ?? null,
                'condition_rating' => $assessmentData['condition_rating'] ?? null,
                'raw_assessment' => $assessmentData,
                'surveyor_name'  => $surveyorName,
            ];

            Log::info('ChatGPT section payload', [
                'payload' => $sectionPayload,
            ]);
            $instruction = 'You are ' . $surveyorName . ', an experienced property surveyor. '
                . 'You will be given JSON data describing one survey section in the exact field order: '
                . 'section, element, location, structure, material, defects, remaining_life, costs, notes, surveyor_name. '
                . 'Use this data to write a clear, UK-English narrative for a residential survey report. '
                . 'Keep the tone professional and concise, and only describe what is in the data without inventing additional facts.';

            $responseText = $this->callAssistantApi($sectionPayload, $instruction);

            Log::info('ChatGPT report generated via assistant', [
                'section'  => $sectionName,
                'category' => $categoryName,
            ]);

            return $responseText;
            
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
            $accommodationType = $assessmentData['accommodation_type'] ?? $accommodationName;
            $notes = $assessmentData['notes'] ?? null;
            $conditionRating = $assessmentData['condition_rating'] ?? null;
            $surveyorName = $this->getSurveyorName();

            $components = [];
            foreach ($assessmentData['components'] ?? [] as $item) {
                $defects = $item['defects'] ?? [];
                if (!is_array($defects)) {
                    $defects = array_filter([$defects]);
                }
                $components[] = [
                    'component'       => $item['component'] ?? 'Unknown',
                    'material'        => $item['material'] ?? null,
                    'defects'         => $defects,
                ];
            }

            $accommodationPayload = [
                'accommodation_name'  => $accommodationName,
                'accommodation_type'  => $accommodationType,
                'components'          => $components,
                'notes'               => $notes,
                'condition_rating'    => $conditionRating,
                'surveyor_name'       => $surveyorName,
                'raw_assessment'      => $assessmentData,
            ];

            Log::info('ChatGPT accommodation payload', [
                'payload' => $accommodationPayload,
            ]);

            $instruction = 'You are ' . $surveyorName . ', an experienced property surveyor. '
                . 'You will be given JSON data describing one accommodation (e.g. Bedroom, Bathroom) with components. '
                . 'Fields: accommodation_name, accommodation_type, components (each with component, material, defects, remaining_life, costs, notes), notes, condition_rating, surveyor_name. '
                . 'Use this data to write a clear, UK-English narrative for a residential survey report. '
                . 'Keep the tone professional and concise, and only describe what is in the data without inventing additional facts.';

            $responseText = $this->callAssistantApi($accommodationPayload, $instruction);

            Log::info('ChatGPT accommodation report generated via assistant', [
                'accommodation' => $accommodationName,
                'type'         => $accommodationType,
            ]);

            return $responseText;

        } catch (\Exception $e) {
            Log::error('ChatGPT accommodation report generation failed', [
                'error'         => $e->getMessage(),
                'accommodation' => $accommodationName,
            ]);

            throw new \Exception('Failed to generate accommodation report: ' . $e->getMessage());
        }
    }

    /**
     * Combined narrative for one component across all rooms of an accommodation type (e.g. all bedrooms).
     *
     * @param array $payload accommodation_type, component_name, component_key, rooms[], surveyor_name set inside
     */
    public function generateAccommodationGroupComponentReport(array $payload): string
    {
        try {
            $surveyorName = $this->getSurveyorName();
            $payload['surveyor_name'] = $surveyorName;

            Log::info('ChatGPT accommodation group-component payload', [
                'accommodation_type' => $payload['accommodation_type'] ?? null,
                'component_key' => $payload['component_key'] ?? null,
            ]);

            $instruction = 'You are ' . $surveyorName . ', an experienced UK residential surveyor. '
                . 'You will receive JSON with accommodation_type, component_name, component_key, and rooms (each room has room_label, material, defects, notes, location, condition_rating). '
                . 'Write ONE combined UK-English narrative for this single component across all listed rooms. '
                . 'Reference rooms by their room_label when comparing; mention location when provided for a room. Only use supplied data; do not invent defects or materials. '
                . 'Keep a professional survey tone; no preamble.';

            $responseText = $this->callAssistantApi($payload, $instruction);

            return $responseText;
        } catch (\Exception $e) {
            Log::error('ChatGPT accommodation group-component report failed', [
                'error' => $e->getMessage(),
                'component_key' => $payload['component_key'] ?? null,
            ]);

            throw new \Exception('Failed to generate combined component narrative: ' . $e->getMessage());
        }
    }

    /**
     * Chat Completions API — works with OPENAI_API_KEY only (no assistant required).
     * Used when OPENAI_ASSISTANT_ID is not set or Assistants API fails.
     *
     * @param array $payload
     * @param string $instruction
     * @return string
     * @throws \Exception
     */
    protected function callChatCompletionsApi(array $payload, string $instruction): string
    {
        $apiKey = config('services.openai.key');
        if (empty($apiKey)) {
            throw new \Exception('OpenAI API key is not configured.');
        }

        $model = config('services.openai.chat_model', 'gpt-4o-mini');
        $baseUrl = 'https://api.openai.com/v1';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(120)->post($baseUrl . '/chat/completions', [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a UK residential building surveyor. Reply with only the report narrative requested — no preamble, no markdown code fences unless the narrative itself needs them.',
                ],
                [
                    'role' => 'user',
                    'content' => $instruction . "\n\nJSON:\n" . json_encode($payload),
                ],
            ],
            'temperature' => 0.35,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Chat Completions request failed: ' . $response->body());
        }

        $json = $response->json();
        $text = $json['choices'][0]['message']['content'] ?? null;
        if ($text === null || $text === '') {
            throw new \Exception('Chat Completions returned an empty response.');
        }

        return trim($text);
    }

    /**
     * Call the OpenAI Assistants API with the structured assessment data.
     * Falls back to Chat Completions when assistant_id is missing or the run fails.
     *
     * @param array $payload
     * @param string $instruction
     * @return string
     * @throws \Exception
     */
    protected function callAssistantApi(array $payload, string $instruction): string
    {
        $apiKey = config('services.openai.key');
        $assistantId = config('services.openai.assistant_id');
        if (empty($apiKey)) {
            throw new \Exception('OpenAI API key is not configured.');
        }

        if (empty($assistantId)) {
            Log::info('OPENAI_ASSISTANT_ID not set; using Chat Completions for report generation');

            return $this->callChatCompletionsApi($payload, $instruction);
        }

        $baseUrl = 'https://api.openai.com/v1';

        try {
        // Create a new thread with the user message including our JSON payload
        $threadResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2',
        ])->timeout(120)->post($baseUrl . '/threads', [
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $instruction . "\n\nJSON:\n" . json_encode($payload),
                        ],
                    ],
                ],
            ],
        ]);

        if (!$threadResponse->successful()) {
            throw new \Exception('Failed to create assistant thread: ' . $threadResponse->body());
        }

        $thread = $threadResponse->json();
        $threadId = $thread['id'] ?? null;
        
        if (!$threadId) {
            throw new \Exception('Assistant thread ID not found in response.');
        }

        // Start a run for this thread using the configured assistant
        $runResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2',
        ])->post($baseUrl . '/threads/' . $threadId . '/runs', [
            'assistant_id' => $assistantId,
        ]);

        if (!$runResponse->successful()) {
            throw new \Exception('Failed to start assistant run: ' . $runResponse->body());
        }

        $run = $runResponse->json();
        $runId = $run['id'] ?? null;

        if (!$runId) {
            throw new \Exception('Assistant run ID not found in response.');
        }

        // Poll the run status until it is completed (or times out)
        $maxAttempts = 20;
        $attempt = 0;
        $status = $run['status'] ?? 'queued';

        while (in_array($status, ['queued', 'in_progress', 'cancelling'], true) && $attempt < $maxAttempts) {
            sleep(1);

            $checkResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
                'OpenAI-Beta' => 'assistants=v2',
            ])->get($baseUrl . '/threads/' . $threadId . '/runs/' . $runId);

            if (!$checkResponse->successful()) {
                throw new \Exception('Failed to check assistant run status: ' . $checkResponse->body());
            }

            $run = $checkResponse->json();
            $status = $run['status'] ?? 'queued';
            $attempt++;
        }

        if ($status !== 'completed') {
            $lastError = $run['last_error']['message'] ?? null;
            $errorMessage = 'Assistant run did not complete. Final status: ' . $status;

            if ($runId) {
                $errorMessage .= ' (run_id: ' . $runId . ')';
            }

            if ($lastError) {
                $errorMessage .= ' - last_error: ' . $lastError;
            }

            throw new \Exception($errorMessage);
        }

        // Retrieve the latest assistant message from the thread
        $messagesResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2',
        ])->get($baseUrl . '/threads/' . $threadId . '/messages', [
            'limit' => 1,
        ]);

        if (!$messagesResponse->successful()) {
            throw new \Exception('Failed to retrieve assistant messages: ' . $messagesResponse->body());
        }

        $messages = $messagesResponse->json();
        Log::info('ChatGPT generated message via assistant', [
            'messages' => $messages,
        ]);
        $data = $messages['data'][0]['content'][0]['text']['value'] ?? null;

        if (empty($data)) {
            throw new \Exception('Assistant returned an empty response.');
        }

        return trim($data);
        } catch (\Exception $e) {
            Log::warning('OpenAI Assistants API failed; falling back to Chat Completions', [
                'error' => $e->getMessage(),
            ]);

            return $this->callChatCompletionsApi($payload, $instruction);
        }
    }
}


