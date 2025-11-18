<?php

namespace App\Http\Controllers\Surveyor;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Show media upload page for a survey.
     */
    public function index(Survey $survey)
    {
        // Only assigned surveyor can access media
        if ($survey->surveyor_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Get existing media files (for now, we'll simulate this)
        $mediaFiles = $this->getMediaFiles($survey);

        return view('surveyor.survey.media', compact('survey', 'mediaFiles'));
    }

    /**
     * Handle media upload for a survey.
     */
    public function upload(Request $request, Survey $survey)
    {
        // Only assigned surveyor can upload media
        if ($survey->surveyor_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Handle file uploads - Laravel receives 'files[]' as 'files' when sent via FormData
        $files = [];
        if ($request->hasFile('files')) {
            $requestedFiles = $request->file('files');
            // Laravel always returns an array when files are sent as files[]
            // But check if it's actually an array or single file
            $files = is_array($requestedFiles) ? $requestedFiles : [$requestedFiles];
            // Filter out null values (if array has gaps)
            $files = array_filter($files, function($file) {
                return $file !== null && $file->isValid();
            });
            $files = array_values($files); // Re-index array
        }

        if (empty($files)) {
            return response()->json([
                'success' => false,
                'message' => 'No files provided',
                'uploaded_files' => [],
                'errors' => []
            ], 400);
        }

        // Validate files
        foreach ($files as $file) {
            if (!$file->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file: ' . $file->getClientOriginalName(),
                    'uploaded_files' => [],
                    'errors' => []
                ], 400);
            }

            // Check file size (100MB max)
            if ($file->getSize() > 102400 * 1024) {
                return response()->json([
                    'success' => false,
                    'message' => 'File too large: ' . $file->getClientOriginalName() . ' (max 100MB)',
                    'uploaded_files' => [],
                    'errors' => []
                ], 400);
            }
        }

        $uploadedFiles = [];
        $errors = [];

        foreach ($files as $file) {
            try {
                // Determine file type based on MIME type
                $type = str_starts_with($file->getMimeType(), 'video/') ? 'video' : 'image';
                
                // Upload to S3
                $uploadedFile = $this->uploadToS3($file, $survey, $type);
                $uploadedFiles[] = $uploadedFile;
            } catch (\Exception $e) {
                $errors[] = [
                    'file' => $file->getClientOriginalName(),
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => count($uploadedFiles) . ' file(s) uploaded successfully',
            'uploaded_files' => $uploadedFiles,
            'errors' => $errors,
            'upload_count' => count($uploadedFiles),
            'error_count' => count($errors)
        ]);
    }

    /**
     * Delete a media file.
     */
    public function delete(Request $request, Survey $survey)
    {
        // Only assigned surveyor can delete media
        if ($survey->surveyor_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'media_id' => 'required|string',
            'type' => 'required|in:video,image',
            'section' => 'nullable|string',
        ]);

        try {
            // Simulate S3 deletion
            $deleted = $this->simulateS3Delete($request->media_id, $survey, $request->type, $request->section);
            
            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Media file deleted successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete media file'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting media: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all media files for a survey.
     */
    public function getMedia(Survey $survey)
    {
        // Only assigned surveyor can view media
        if ($survey->surveyor_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $mediaFiles = $this->getMediaFiles($survey);

        return response()->json([
            'success' => true,
            'media_files' => $mediaFiles
        ]);
    }

    /**
     * Upload file to S3 bucket.
     * Future-ready: This method can be easily extended to store metadata in database.
     */
    private function uploadToS3($file, $survey, $type, $section = null)
    {
        // Generate unique filename to prevent conflicts
        $filename = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
        
        // S3 path structure: surveys/{survey_id}/media/{type}/{filename}
        $s3Path = "surveys/{$survey->id}/media/{$type}/{$filename}";
        
        // Determine disk (use S3 if configured, otherwise use public for local development)
        $disk = env('FILESYSTEM_DISK', 'local') === 's3' ? 's3' : 'public';
        
        // Upload file to storage
        $storedPath = Storage::disk($disk)->putFileAs(
            dirname($s3Path),
            $file,
            basename($s3Path),
            'public' // Public visibility
        );
        
        // Get file URL
        $fileUrl = Storage::disk($disk)->url($storedPath);
        
        // For S3, construct proper URL if not returned correctly
        if ($disk === 's3') {
            $bucket = config('filesystems.disks.s3.bucket');
            $region = config('filesystems.disks.s3.region');
            $fileUrl = "https://{$bucket}.s3.{$region}.amazonaws.com/{$storedPath}";
        }
        
        // Return file metadata (can be stored in database later for transcription tracking)
        return [
            'id' => uniqid('media_', true),
            'original_name' => $file->getClientOriginalName(),
            'filename' => $filename,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            's3_path' => $storedPath,
            's3_url' => $fileUrl,
            'type' => $type,
            'section' => $section,
            'survey_id' => $survey->id,
            'uploaded_by' => auth()->id(),
            'uploaded_at' => now()->toISOString(),
            'status' => 'completed',
            'needs_transcription' => $type === 'video', // Flag for transcription processing
        ];
    }

    /**
     * Simulate S3 deletion.
     */
    private function simulateS3Delete($mediaId, $survey, $type, $section = null)
    {
        // Simulate processing delay
        usleep(rand(200000, 800000)); // 0.2-0.8 seconds

        // Simulate occasional failure (2% chance)
        if (rand(1, 100) <= 2) {
            return false;
        }

        // In a real implementation, you would delete from S3 here
        // For now, just return success
        return true;
    }

    /**
     * Get media files for a survey (simulated).
     */
    private function getMediaFiles($survey)
    {
        // This would normally query a database table for media files
        // For now, return empty array as we're simulating
        return [];
    }
}