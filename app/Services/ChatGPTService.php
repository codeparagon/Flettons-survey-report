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

            if (! empty($assessmentData['selected_location_accommodation_titles']) && is_array($assessmentData['selected_location_accommodation_titles'])) {
                $locTitles = array_values(array_unique(array_filter(
                    array_map(static fn ($s) => trim((string) $s), $assessmentData['selected_location_accommodation_titles']),
                    static fn ($s) => $s !== ''
                )));
                if ($locTitles !== []) {
                    $sectionPayload['selected_location_accommodation_titles'] = $locTitles;
                }
            }

            Log::info('ChatGPT section payload', [
                'payload' => $sectionPayload,
            ]);
            $instruction = 'You are ' . $surveyorName . ', an experienced property surveyor. '
                . 'You will be given JSON data describing one survey section in the exact field order: '
                . 'section, element, location, structure, material, defects, remaining_life, costs, notes, surveyor_name. '
                . 'If selected_location_accommodation_titles is present, it lists every accommodation room title the surveyor tied to this row via the Location field; mention each title explicitly when relating this section to those rooms. '
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
     * Combined UK survey narrative and bullet observations for all rooms of an accommodation type.
     *
     * @param array $payload Must include accommodation_type, rooms[], and component_keys (list of component_key strings for this type).
     *                        May include selected_location_accommodation_titles: list of accommodation row titles from the section Location field (same strings as the Location multi-select).
     * @return array{narrative: string, observations: array<int, string>, component_observations: array<string, array<int, string>>}
     * @throws \Exception
     */
    public function generateAccommodationCombinedReport(array $payload): array
    {
        try {
            $surveyorName = $this->getSurveyorName();
            $payload['surveyor_name'] = $surveyorName;

            Log::info('ChatGPT accommodation combined report payload', [
                'accommodation_type' => $payload['accommodation_type'] ?? null,
                'room_count' => isset($payload['rooms']) && is_array($payload['rooms']) ? count($payload['rooms']) : 0,
            ]);

            $instruction = 'You are ' . $surveyorName . ', an experienced UK residential surveyor. '
                . 'You will receive JSON with accommodation_type, surveyor_name, component_keys (array of component_key strings), and rooms[]. Each room has room_label, accommodation_title (same wording as each other and as the Location multi-select for that row), clone_index (0-based row order), location, notes, condition_rating, and components (component_key, component_name, material, defects, location). '
                . 'If selected_location_accommodation_titles is present, it lists every accommodation room title the surveyor selected in this section\'s Location field for this submission; you must explicitly reference each listed title in the narrative where it relates to the supplied room data, and reflect that scope in general observations. '
                . 'Write ONE professional UK-English narrative in "narrative" covering all rooms; when multiple rooms exist, structure by room: each time you move to another row, name it using accommodation_title (and room_label — they match) so main and cloned rows are never conflated. Do not reuse identical long sentences across rooms unless their supplied materials, defects, room location, component locations, and notes are truly the same; if any field differs, the prose must differ and stay tied to that room. Only use supplied data. '
                . 'Put cross-element or general bullets only in "observations" (array of strings); may be empty. '
                . 'Put element-specific factual bullets in "component_observations": an object whose keys are EXACTLY the strings in component_keys, each value an array of short bullet strings for that element across all rooms (use [] if nothing notable). When the same component differs between rooms, prefix bullets with the accommodation_title they apply to. '
                . 'Respond with ONLY valid JSON (no markdown fences) with keys: "narrative" (string), "observations" (array of strings), "component_observations" (object).';

            $responseText = $this->callChatCompletionsJsonObject($payload, $instruction);

            return $this->decodeCombinedAccommodationReportJson($responseText);
        } catch (\Exception $e) {
            Log::error('ChatGPT accommodation combined report failed', [
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('Failed to generate combined accommodation report: ' . $e->getMessage());
        }
    }

    /**
     * Room-specific bullet observations for each component for one accommodation room row.
     *
     * @param array $payload Must include accommodation_type, room (object), and component_keys (list of component_key strings for this type).
     *                        May include selected_location_accommodation_titles: every accommodation title selected in the section Location for this submission.
     * @return array{component_observations: array<string, array<int, string>>}
     * @throws \Exception
     */
    public function generateAccommodationRoomComponentObservations(array $payload): array
    {
        $surveyorName = $this->getSurveyorName();
        $payload['surveyor_name'] = $surveyorName;

        $instruction = 'You are ' . $surveyorName . ', an experienced UK residential surveyor. '
            . 'You will receive JSON with accommodation_type, surveyor_name, component_keys (array of component_key strings), and room (object). '
            . 'Room has: room_label, accommodation_title (same wording as the Location multi-select for this row), clone_index, location, notes, condition_rating, and components (component_key, component_name, material, defects, location). '
            . 'If selected_location_accommodation_titles is present, it lists every accommodation title the surveyor selected in the section Location field for this submission; use that list for overall scope and cross-room context, while bullets remain specific to this single room only. '
            . 'Return ONLY valid JSON with key "component_observations" where keys are EXACTLY the strings in component_keys and each value is an array of short factual bullet strings '
            . 'ONLY for this single room (do not combine with other rooms; do not include general observations). Bullets must be unique to this room\'s supplied data — avoid generic boilerplate that could apply unchanged to another row. '
            . 'IMPORTANT: write at least 3 bullets per component when there is any input for it (material and/or defects and/or location and/or relevant notes). '
            . 'Bullets must be specific to the supplied data: reference the selected material/defects/location, and use cautious professional phrasing (e.g. "noted", "recorded", "no significant defects recorded"). '
            . 'Do NOT invent measurements, causes, or remediation unless explicitly provided. If there is genuinely no input for a component, use an empty list [].';

        $text = $this->callChatCompletionsJsonObject($payload, $instruction);
        $decoded = json_decode($text, true);
        if (!is_array($decoded)) {
            throw new \Exception('Room component observations response was not valid JSON.');
        }

        $raw = $decoded['component_observations'] ?? [];
        $out = [];

        $componentKeys = isset($payload['component_keys']) && is_array($payload['component_keys'])
            ? array_values(array_filter(array_map(static fn ($v) => is_string($v) ? trim($v) : '', $payload['component_keys']), static fn ($v) => $v !== ''))
            : [];

        $roomComponents = [];
        if (isset($payload['room']) && is_array($payload['room']) && isset($payload['room']['components']) && is_array($payload['room']['components'])) {
            foreach ($payload['room']['components'] as $c) {
                if (!is_array($c)) continue;
                $ck = isset($c['component_key']) ? trim((string) $c['component_key']) : '';
                if ($ck === '') continue;
                $roomComponents[$ck] = $c;
            }
        }

        // Normalize GPT output into string bullet arrays.
        if (is_array($raw)) {
            foreach ($raw as $key => $items) {
                if (!is_string($key) || $key === '') {
                    continue;
                }
                $list = [];
                if (is_array($items)) {
                    foreach ($items as $bullet) {
                        if (is_string($bullet)) {
                            $bt = trim($bullet);
                            if ($bt !== '') {
                                $list[] = $bt;
                            }
                        }
                    }
                }
                $out[$key] = array_values($list);
            }
        }

        // Safety net: ensure every component_key exists, and ensure we have meaningful multi-bullets
        // derived from saved data (without inventing new facts).
        foreach ($componentKeys as $ck) {
            $existing = $out[$ck] ?? [];
            if (!is_array($existing)) {
                $existing = [];
            }
            $existing = array_values(array_filter(array_map('strval', $existing), static fn ($s) => trim($s) !== ''));

            $comp = $roomComponents[$ck] ?? null;
            $material = is_array($comp) ? trim((string) ($comp['material'] ?? '')) : '';
            $location = is_array($comp) ? trim((string) ($comp['location'] ?? '')) : '';
            $defects = [];
            if (is_array($comp) && isset($comp['defects']) && is_array($comp['defects'])) {
                $defects = array_values(array_filter(array_map(static fn ($d) => trim((string) $d), $comp['defects']), static fn ($d) => $d !== ''));
            }

            $hasInput = ($material !== '') || ($location !== '') || ($defects !== []);

            if (!$hasInput) {
                $out[$ck] = $existing;
                continue;
            }

            if (count($existing) >= 3) {
                $out[$ck] = $existing;
                continue;
            }

            $fallback = $existing;
            if ($material !== '') {
                $fallback[] = 'Material recorded as: ' . $material . '.';
            }
            if ($location !== '') {
                $fallback[] = 'Location/position recorded as: ' . $location . '.';
            }
            if ($defects !== []) {
                $fallback[] = 'Defects recorded: ' . implode(', ', $defects) . '.';
                $meaningful = array_filter($defects, static fn ($d) => !in_array($d, ['None', 'No Defects'], true));
                if ($meaningful === []) {
                    $fallback[] = 'No significant defects were recorded for this component.';
                }
            } else {
                $fallback[] = 'No defects were selected/recorded for this component.';
            }

            // Deduplicate while preserving order, and clamp to a sensible size.
            $dedup = [];
            foreach ($fallback as $b) {
                $t = trim((string) $b);
                if ($t === '') continue;
                if (in_array($t, $dedup, true)) continue;
                $dedup[] = $t;
            }
            $out[$ck] = array_slice($dedup, 0, 6);
        }

        return ['component_observations' => $out];
    }

    /**
     * Chat Completions with JSON object response (structured accommodation outputs).
     *
     * @throws \Exception
     */
    protected function callChatCompletionsJsonObject(array $payload, string $instruction): string
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
                    'content' => 'You are a UK residential building surveyor. Reply with a single valid JSON object only. Do not wrap in markdown code fences.',
                ],
                [
                    'role' => 'user',
                    'content' => $instruction . "\n\nJSON input:\n" . json_encode($payload),
                ],
            ],
            'temperature' => 0.35,
            'response_format' => ['type' => 'json_object'],
        ]);

        if (!$response->successful()) {
            throw new \Exception('Chat Completions JSON request failed: ' . $response->body());
        }

        $json = $response->json();
        $text = $json['choices'][0]['message']['content'] ?? null;
        if ($text === null || $text === '') {
            throw new \Exception('Chat Completions returned an empty JSON response.');
        }

        return trim($text);
    }

    /**
     * @return array{narrative: string, observations: array<int, string>, component_observations: array<string, array<int, string>>}
     */
    protected function decodeCombinedAccommodationReportJson(string $text): array
    {
        $text = trim($text);
        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/', $text, $m)) {
            $text = trim($m[1]);
        }

        $decoded = json_decode($text, true);
        if (!is_array($decoded)) {
            throw new \Exception('Combined accommodation report response was not valid JSON.');
        }

        $narrative = isset($decoded['narrative']) ? (string) $decoded['narrative'] : '';
        $obs = $decoded['observations'] ?? [];
        if (!is_array($obs)) {
            $obs = [];
        }

        $observations = [];
        foreach ($obs as $item) {
            if (is_string($item)) {
                $t = trim($item);
                if ($t !== '') {
                    $observations[] = $t;
                }
            }
        }

        $rawByComponent = $decoded['component_observations'] ?? [];
        $componentObservations = [];
        if (is_array($rawByComponent)) {
            foreach ($rawByComponent as $key => $items) {
                if (! is_string($key) || $key === '') {
                    continue;
                }
                $list = [];
                if (is_array($items)) {
                    foreach ($items as $bullet) {
                        if (is_string($bullet)) {
                            $bt = trim($bullet);
                            if ($bt !== '') {
                                $list[] = $bt;
                            }
                        }
                    }
                }
                $componentObservations[$key] = array_values($list);
            }
        }

        return [
            'narrative' => $narrative,
            'observations' => array_values($observations),
            'component_observations' => $componentObservations,
        ];
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
                . 'You will receive JSON with accommodation_type, component_name, component_key, and rooms (each room has room_label, accommodation_title, clone_index, material, defects, notes, location, condition_rating). '
                . 'If selected_location_accommodation_titles is present, it lists every accommodation room title chosen in the section Location field; reference each title in the narrative when tying rooms together. '
                . 'Write ONE combined UK-English narrative for this single component across all listed rooms. '
                . 'When more than one room is listed, treat each row distinctly: name the accommodation_title (same as room_label) whenever you discuss that row\'s finishes, and do not copy-paste identical paragraphs where materials, defects, or locations differ — each room must read as its own assessment within the whole. '
                . 'Mention location when provided for a room. Only use supplied data; do not invent defects or materials. '
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


