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

        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|max:102400', // 100MB max
            'type' => 'required|in:video,image',
            'section' => 'nullable|string|max:255',
        ]);

        $uploadedFiles = [];
        $errors = [];

        foreach ($request->file('files') as $file) {
            try {
                // Simulate S3 upload with static response
                $uploadedFile = $this->simulateS3Upload($file, $survey, $request->type, $request->section);
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
            'message' => 'Files uploaded successfully',
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
     * Simulate S3 upload with static response structure.
     */
    private function simulateS3Upload($file, $survey, $type, $section = null)
    {
        // Simulate processing delay
        usleep(rand(500000, 2000000)); // 0.5-2 seconds

        // Simulate occasional failure (5% chance)
        if (rand(1, 100) <= 5) {
            throw new \Exception('Network error during upload');
        }

        // Generate S3-like URL structure
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $s3Path = "surveys/{$survey->id}/media/{$type}/" . ($section ? "{$section}/" : '') . $filename;
        
        return [
            'id' => uniqid(),
            'original_name' => $file->getClientOriginalName(),
            'filename' => $filename,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            's3_path' => $s3Path,
            's3_url' => "https://your-bucket.s3.amazonaws.com/{$s3Path}",
            'type' => $type,
            'section' => $section,
            'uploaded_at' => now()->toISOString(),
            'status' => 'completed'
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