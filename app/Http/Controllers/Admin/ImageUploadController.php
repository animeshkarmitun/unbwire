<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
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
            'file' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'], // 5MB max
            'alt' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $file = $request->file('file');

            // Generate unique filename
            $extension = $file->getClientOriginalExtension();
            $filename = 'summernote_' . Str::random(40) . '.' . $extension;

            // Store file in public disk
            $storedPath = $file->storeAs('uploads/media', $filename, 'public');

            // Return full URL for Summernote
            $url = asset('storage/' . $storedPath);

            // Get image dimensions
            $width = null;
            $height = null;
            $imageInfo = @getimagesize($file->getRealPath());
            if ($imageInfo) {
                $width = $imageInfo[0];
                $height = $imageInfo[1];
            }

            // Create Media record for tracking
            try {
                Media::create([
                    'filename' => $filename,
                    'original_filename' => $file->getClientOriginalName(),
                    'file_path' => 'storage/' . $storedPath,
                    'file_url' => $url,
                    'file_type' => 'image',
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'width' => $width,
                    'height' => $height,
                    'alt_text' => $request->input('alt', ''),
                    'caption' => $request->input('caption', ''),
                    'uploaded_by' => Auth::guard('admin')->id(),
                    'uploaded_by_type' => 'App\Models\Admin',
                ]);
            } catch (\Exception $e) {
                // Log error but don't fail the upload
                \Log::warning('Failed to create Media record: ' . $e->getMessage());
            }

            return response()->json([
                'url' => $url,
                'alt' => $request->input('alt', ''),
                'caption' => $request->input('caption', '')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error uploading image: ' . $e->getMessage()
            ], 500);
        }
    }
}

