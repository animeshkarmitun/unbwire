<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\ImageProcessingService;
use App\Traits\FileUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadController extends Controller
{
    use FileUploadTrait;

    /**
     * Handle image upload from Summernote editor with Alt and Caption
     * Also creates a Media record for tracking in media library
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'], // 10MB max (matching media library)
            'alt' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'], // Support both alt and alt_text
            'caption' => ['nullable', 'string', 'max:500'],
            'title' => ['nullable', 'string', 'max:255'], // For media library uploads
            'description' => ['nullable', 'string', 'max:1000'], // For media library uploads
            'crop_data' => ['nullable', 'array'],
            'convert_to_webp' => ['nullable', 'boolean'],
            'add_watermark' => ['nullable', 'boolean'],
            'watermark_position' => ['nullable', 'string'],
            'keep_original' => ['nullable', 'boolean'],
        ]);

        try {
            $file = $request->file('file');
            $cropData = $request->input('crop_data');
            $convertToWebp = $request->boolean('convert_to_webp', true);
            $addWatermark = $request->boolean('add_watermark', false);
            $watermarkPosition = $request->input('watermark_position', 'center');
            $keepOriginal = $request->boolean('keep_original', true);

            $imageService = new ImageProcessingService();
            $results = $imageService->processImage(
                $file,
                $cropData,
                $convertToWebp,
                $addWatermark,
                $watermarkPosition,
                'uploads/media',
                $keepOriginal
            );

            // Use WebP if available, otherwise use processed image
            $finalPath = $results['webp_path'] ?? $results['processed_path'];
            $url = asset($finalPath);

            // Get image dimensions
            $dimensions = $imageService->getImageDimensions($finalPath);

            // Get alt_text (support both 'alt' and 'alt_text' fields)
            $altText = $request->input('alt_text') ?: $request->input('alt', '');

            // Create Media record for tracking
            try {
                $media = Media::create([
                    'filename' => basename($finalPath),
                    'original_filename' => $file->getClientOriginalName(),
                    'file_path' => $finalPath,
                    'file_url' => $url,
                    'file_type' => 'image',
                    'mime_type' => $convertToWebp && isset($results['webp_path']) ? 'image/webp' : $file->getMimeType(),
                    'file_size' => file_exists(public_path($finalPath)) ? filesize(public_path($finalPath)) : $file->getSize(),
                    'width' => $dimensions['width'],
                    'height' => $dimensions['height'],
                    'title' => $request->input('title'),
                    'alt_text' => $altText,
                    'caption' => $request->input('caption'),
                    'description' => $request->input('description'),
                    'uploaded_by' => Auth::guard('admin')->id(),
                    'uploaded_by_type' => 'App\Models\Admin',
                ]);
            } catch (\Exception $e) {
                // Log error but don't fail the upload
                \Log::warning('Failed to create Media record: ' . $e->getMessage());
            }

            // Return response - check if this is a media library upload (has title/description) or editor upload
            $isMediaLibraryUpload = $request->has('title') || $request->has('description');
            
            if ($isMediaLibraryUpload) {
                // Return media library format
                return response()->json([
                    'success' => true,
                    'message' => 'Media uploaded successfully',
                    'media' => isset($media) ? $media->load('uploader') : null,
                ], 201);
            } else {
                // Return editor format (for backward compatibility)
                return response()->json([
                    'url' => $url,
                    'alt' => $altText,
                    'caption' => $request->input('caption', ''),
                    'original_path' => $results['original_path'] ?? null,
                    'webp_path' => $results['webp_path'] ?? null,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Image upload error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error uploading image: ' . $e->getMessage()
            ], 500);
        }
    }
}

