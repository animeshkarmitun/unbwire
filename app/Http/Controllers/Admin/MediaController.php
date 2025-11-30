<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    /**
     * Display a listing of the media library.
     */
    public function index(Request $request)
    {
        $query = Media::with('uploader')->latest();

        // Filter by file type
        if ($request->filled('type')) {
            $query->ofType($request->type);
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by uploader
        if ($request->filled('uploaded_by')) {
            $query->where('uploaded_by', $request->uploaded_by);
        }

        // Get media (paginated for API, all for view)
        if ($request->wantsJson() || $request->ajax()) {
            $perPage = $request->get('per_page', 20);
            $media = $query->paginate($perPage);
            
            return response()->json([
                'data' => $media->items(),
                'current_page' => $media->currentPage(),
                'last_page' => $media->lastPage(),
                'per_page' => $media->perPage(),
                'total' => $media->total(),
            ]);
        }

        $media = $query->paginate(24);
        $uploaders = \App\Models\Admin::select('id', 'name')->get();

        return view('admin.media-library.index', compact('media', 'uploaders'));
    }

    /**
     * Get media for editor (API endpoint for modal)
     */
    public function getMediaForEditor(Request $request)
    {
        $query = Media::latest();

        // Filter by file type (default to images for editor)
        $type = $request->get('type', 'image');
        if ($type !== 'all') {
            $query->ofType($type);
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $perPage = $request->get('per_page', 20);
        $media = $query->paginate($perPage);

        return response()->json([
            'data' => $media->items(),
            'current_page' => $media->currentPage(),
            'last_page' => $media->lastPage(),
            'per_page' => $media->perPage(),
            'total' => $media->total(),
        ]);
    }

    /**
     * Store a newly uploaded media file.
     */
    public function store(Request $request)
    {
        $isAjax = $request->ajax() || $request->wantsJson() || $request->expectsJson();
        
        // Validate file
        try {
            $request->validate([
                'file' => ['required', 'file', 'max:10240'], // 10MB max
                'title' => ['nullable', 'string', 'max:255'],
                'alt_text' => ['nullable', 'string', 'max:255'],
                'caption' => ['nullable', 'string', 'max:500'],
                'description' => ['nullable', 'string', 'max:1000'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        try {
            $file = $request->file('file');
            
            if (!$file || !$file->isValid()) {
                throw new \Exception('Invalid file uploaded');
            }
            
            $mimeType = $file->getMimeType();
            $originalFilename = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            
            // Determine file type
            $fileType = $this->determineFileType($mimeType, $extension);
            
            // Validate file type specific rules
            $this->validateFileType($file, $fileType);

            // Generate unique filename
            $filename = Str::random(40) . '.' . $extension;

            // Store file
            $storedPath = $file->storeAs('uploads/media', $filename, 'public');
            $fileUrl = asset('storage/' . $storedPath);

            // Get image dimensions if it's an image
            $width = null;
            $height = null;
            if ($fileType === 'image') {
                $imageInfo = @getimagesize($file->getRealPath());
                if ($imageInfo) {
                    $width = $imageInfo[0];
                    $height = $imageInfo[1];
                }
            }

            // Create media record
            $media = Media::create([
                'filename' => $filename,
                'original_filename' => $originalFilename,
                'file_path' => 'storage/' . $storedPath,
                'file_url' => $fileUrl,
                'file_type' => $fileType,
                'mime_type' => $mimeType,
                'file_size' => $file->getSize(),
                'width' => $width,
                'height' => $height,
                'title' => $request->input('title'),
                'alt_text' => $request->input('alt_text'),
                'caption' => $request->input('caption'),
                'description' => $request->input('description'),
                'uploaded_by' => Auth::guard('admin')->id(),
                'uploaded_by_type' => 'App\Models\Admin',
            ]);

            // Return response
            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'Media uploaded successfully',
                    'media' => $media->load('uploader'),
                ], 201);
            }

            return redirect()->route('admin.media-library.index')
                ->with('success', 'Media uploaded successfully');
        } catch (\Exception $e) {
            \Log::error('Media upload error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'error' => 'Error uploading media: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error uploading media: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified media.
     */
    public function show(string $id)
    {
        $media = Media::with('uploader')->findOrFail($id);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json($media);
        }

        return view('admin.media-library.show', compact('media'));
    }

    /**
     * Update the specified media metadata.
     */
    public function update(Request $request, string $id)
    {
        $media = Media::findOrFail($id);

        $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        $media->update($request->only([
            'title',
            'alt_text',
            'caption',
            'description',
            'is_featured',
        ]));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Media updated successfully',
                'media' => $media->load('uploader'),
            ]);
        }

        return redirect()->route('admin.media-library.index')
            ->with('success', 'Media updated successfully');
    }

    /**
     * Remove the specified media and its file.
     */
    public function destroy(string $id)
    {
        $media = Media::findOrFail($id);

        // Delete physical file
        $media->deleteFile();

        // Delete database record
        $media->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Media deleted successfully',
            ]);
        }

        return redirect()->route('admin.media-library.index')
            ->with('success', 'Media deleted successfully');
    }

    /**
     * Determine file type based on MIME type and extension
     */
    private function determineFileType(string $mimeType, string $extension): string
    {
        // Images
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        // Videos
        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }

        // Audio
        if (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        }

        // Documents (PDF, Word, Excel, etc.)
        $documentMimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'text/csv',
        ];

        if (in_array($mimeType, $documentMimes)) {
            return 'document';
        }

        // Default to document for unknown types
        return 'document';
    }

    /**
     * Validate file based on type
     */
    private function validateFileType($file, string $fileType): void
    {
        $mimeType = $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());

        switch ($fileType) {
            case 'image':
                $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
                
                if (!in_array($mimeType, $allowedMimes) || !in_array($extension, $allowedExtensions)) {
                    throw new \Exception('Invalid image file. Allowed types: JPG, PNG, GIF, WEBP, SVG');
                }
                break;

            case 'video':
                $allowedMimes = ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime'];
                $allowedExtensions = ['mp4', 'webm', 'ogg', 'mov'];
                
                if (!in_array($mimeType, $allowedMimes) || !in_array($extension, $allowedExtensions)) {
                    throw new \Exception('Invalid video file. Allowed types: MP4, WEBM, OGG, MOV');
                }
                break;

            case 'audio':
                $allowedMimes = ['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/mp4'];
                $allowedExtensions = ['mp3', 'wav', 'ogg', 'm4a'];
                
                if (!in_array($mimeType, $allowedMimes) || !in_array($extension, $allowedExtensions)) {
                    throw new \Exception('Invalid audio file. Allowed types: MP3, WAV, OGG, M4A');
                }
                break;

            case 'document':
                // More lenient for documents
                break;
        }
    }
}
