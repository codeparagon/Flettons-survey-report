<?php

namespace App\Http\Controllers\Surveyor;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\AwsCredential;
use App\Models\Transcription;
use Aws\S3\S3Client;
use Aws\TranscribeService\TranscribeServiceClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenAI;

class AwsTranscriptionController extends Controller
{
    protected $AWS_ACCESS_KEY_ID;
    protected $AWS_SECRET_ACCESS_KEY;
    protected $AWS_BUCKET;
    protected $AWS_DEFAULT_REGION;

    public function __construct()
    {
        $credential = AwsCredential::first();
        if ($credential) {
            $this->AWS_ACCESS_KEY_ID = $credential->access_key_id;
            $this->AWS_SECRET_ACCESS_KEY = $credential->secret_access_key;
            $this->AWS_BUCKET = $credential->bucket_name;
            $this->AWS_DEFAULT_REGION = $credential->region;
        }
    }

    public function showUploadForm()
    {
        return view('surveyor.test');
    }

    public function uploadMedia(Request $request)
    {
        $uploadedFiles = [];

        if (!$request->hasFile('files')) {
            return response()->json([
                'success' => false,
                'message' => 'No files received.'
            ], 400);
        }

        foreach ($request->file('files') as $file) {
            $mime = $file->getMimeType();

            /* -------------------------------------------------------
            | PROCESS IMAGES
            --------------------------------------------------------*/
            if (str_starts_with($mime, 'image/')) {
                $savedImage = ImageHelper::saveImage($file, 'images');

                $uploadedFiles[] = [
                    'type' => 'image',
                    'original_name' => $file->getClientOriginalName(),
                    'id' => null,
                    's3_url' => null,
                    's3_path' => $savedImage,
                    'needs_transcription' => false
                ];

                continue;
            }

            /* -------------------------------------------------------
            | PROCESS VIDEOS (Upload + transcribe + store DB)
            --------------------------------------------------------*/
            if (str_starts_with($mime, 'video/')) {
                $fileName = time() . '_' . $file->getClientOriginalName();

                // AWS S3 Client
                $s3 = new S3Client([
                    'version' => 'latest',
                    'region' => $this->AWS_DEFAULT_REGION,
                    'credentials' => [
                        'key' => $this->AWS_ACCESS_KEY_ID,
                        'secret' => $this->AWS_SECRET_ACCESS_KEY,
                    ],
                ]);

                // Upload video to S3
                $s3->putObject([
                    'Bucket' => $this->AWS_BUCKET,
                    'Key' => "videos/{$fileName}",
                    'SourceFile' => $file->getPathname(),
                    'ACL' => 'private'
                ]);

                $s3Uri = "s3://{$this->AWS_BUCKET}/videos/{$fileName}";

                /* -------------------------------
                | START AWS TRANSCRIBE JOB
                --------------------------------*/
                $transcribe = new TranscribeServiceClient([
                    'version' => 'latest',
                    'region' => $this->AWS_DEFAULT_REGION,
                    'credentials' => [
                        'key' => $this->AWS_ACCESS_KEY_ID,
                        'secret' => $this->AWS_SECRET_ACCESS_KEY,
                    ],
                ]);

                $jobName = 'transcription_' . time() . '_' . rand(1000, 9999);

                $transcribe->startTranscriptionJob([
                    'TranscriptionJobName' => $jobName,
                    'LanguageCode' => 'en-US',
                    'MediaFormat' => 'mp4',
                    'Media' => ['MediaFileUri' => $s3Uri],
                    'OutputBucketName' => $this->AWS_BUCKET,
                ]);

                /* -------------------------------
                | WAIT FOR TRANSCRIPTION
                --------------------------------*/
                $status = 'IN_PROGRESS';
                $attempts = 0;

                while ($status === 'IN_PROGRESS' && $attempts < 30) {
                    sleep(10);

                    $result = $transcribe->getTranscriptionJob([
                        'TranscriptionJobName' => $jobName
                    ]);

                    $status = $result['TranscriptionJob']['TranscriptionJobStatus'];
                    $attempts++;
                }

                $transcriptText = null;

                if ($status === 'COMPLETED') {
                    $transcriptFileUri = $result['TranscriptionJob']['Transcript']['TranscriptFileUri'];
                    $parts = parse_url($transcriptFileUri);
                    $path = ltrim($parts['path'], '/');

                    $object = $s3->getObject([
                        'Bucket' => $this->AWS_BUCKET,
                        'Key' => str_replace("{$this->AWS_BUCKET}/", '', $path),
                    ]);

                    $data = json_decode($object['Body'], true);
                    $transcriptText = $data['results']['transcripts'][0]['transcript'] ?? '';
                }

                /* -----------------------------------------------------
                | STORE TRANSCRIPTION IN DATABASE
                ------------------------------------------------------*/
                $transcriptionRecord = Transcription::create([
                    'survey_id' => $request->survey_id,
                    'file_name' => $fileName,
                    's3_uri' => $s3Uri,
                    'transcription_text' => $transcriptText,
                    'transcription_status' => $status,
                ]);

                /* -----------------------------------------------------
                | RETURN TO FRONTEND
                ------------------------------------------------------*/
                $uploadedFiles[] = [
                    'type' => 'video',
                    'original_name' => $file->getClientOriginalName(),
                    'id' => $transcriptionRecord->id,
                    's3_url' => $s3Uri,
                    's3_path' => "videos/{$fileName}",
                    'needs_transcription' => false,
                    'transcription_status' => $status,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'uploaded_files' => $uploadedFiles,
        ]);
    }

    public function analyzeSurveyTranscriptions($surveyId)
    {
        // Step 1: Fetch all transcriptions for the given survey
        $transcriptions = Transcription::where('survey_id', $surveyId)->pluck('transcription_text')->toArray();

        if (empty($transcriptions)) {
            return response()->json([
                'success' => false,
                'message' => 'No transcriptions found for this survey ID.'
            ], 404);
        }

        // Step 2: Combine all transcriptions
        $combinedText = implode("\n\n", $transcriptions);

        // Step 3: Send to GPT Assistant
        try {
            $client = OpenAI::client('');

            // Create a thread with the message content
            $thread = $client->threads()->create([
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => 'Please the following information in template : property type, material used, condition, defects and additional notes' . $combinedText,
                    ],
                ],
            ]);

            // Step 4: Run the assistant
            $run = $client->threads()->runs()->create(
                threadId: $thread->id,
                parameters: [
                    'assistant_id' => 'asst_OAwHcRjLrNM8hLZikN8ypCf9',
                ]
            );

            // Wait for completion (polling)
            do {
                sleep(2);
                $runStatus = $client->threads()->runs()->retrieve(
                    threadId: $thread->id,
                    runId: $run->id
                );
            } while ($runStatus->status !== 'completed');

            // Step 5: Retrieve messages (assistant response)
            $messages = $client->threads()->messages()->list(threadId: $thread->id);
            $responseText = $messages->data[0]->content[0]->text->value ?? 'No response generated.';

            return response()->json([
                'success' => true,
                'message' => 'Analysis completed successfully.',
                'assistant_response' => $responseText
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
