<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class VideoGalleryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:video gallery index,admin'])->only(['index', 'show']);
        $this->middleware(['permission:video gallery create,admin'])->only(['create', 'store']);
        $this->middleware(['permission:video gallery update,admin'])->only(['edit', 'update']);
        $this->middleware(['permission:video gallery delete,admin'])->only(['destroy']);
    }

    /**
     * Display a listing of video galleries.
     */
    public function index(Request $request)
    {
        $query = Gallery::videos()->with(['media', 'creator'])->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by source (media library or external)
        if ($request->filled('source')) {
            if ($request->source === 'media') {
                $query->whereNotNull('media_id');
            } elseif ($request->source === 'external') {
                $query->whereNotNull('video_url');
            }
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('video_url', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by gallery slug (group)
        if ($request->filled('gallery_slug')) {
            $query->where('gallery_slug', $request->gallery_slug);
        }

        $galleries = $query->paginate(20);
        
        // Get unique gallery slugs for filter (check if column exists first)
        $gallerySlugs = collect();
        try {
            $gallerySlugs = Gallery::videos()
                ->select('gallery_slug')
                ->whereNotNull('gallery_slug')
                ->distinct()
                ->pluck('gallery_slug');
        } catch (\Exception $e) {
            // Column doesn't exist yet, return empty collection
            $gallerySlugs = collect();
        }

        return view('admin.video-gallery.index', compact('galleries', 'gallerySlugs'));
    }

    /**
     * Show the form for creating a new video gallery.
     */
    public function create()
    {
        return view('admin.video-gallery.create');
    }

    /**
     * Store a newly created video gallery.
     */
    public function store(Request $request)
    {
        // Convert comma-separated string to array if needed
        if ($request->has('media_ids') && is_string($request->media_ids)) {
            $mediaIds = array_filter(array_map('trim', explode(',', $request->media_ids)));
            $request->merge(['media_ids' => $mediaIds]);
        }

        $request->validate([
            'source_type' => ['required', 'in:media,external'],
            'media_ids' => ['nullable', 'array', 'min:1'],
            'media_ids.*' => ['required_if:source_type,media', 'exists:media,id'],
            'uploaded_files' => ['nullable', 'array', 'min:1'],
            'uploaded_files.*' => ['file', 'mimes:mp4,webm,ogg,mov', 'max:51200'],
            'video_urls' => ['required_if:source_type,external', 'array'],
            'video_urls.*' => ['required_if:source_type,external', 'url'],
            'gallery_slug' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_exclusive' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'boolean'],
            'language' => ['nullable', 'string', 'max:10'],
            'category' => ['required', 'in:UNB,AP'],
        ]);

        $gallerySlug = $request->gallery_slug ?: 'gallery-' . Str::random(8);
        $sortOrder = 0;

        if ($request->source_type === 'media') {
            $mediaIds = is_array($request->media_ids) ? $request->media_ids : [];

            if ($request->hasFile('uploaded_files')) {
                foreach ($request->file('uploaded_files') as $uploadedFile) {
                    $media = $this->createMediaFromUpload($uploadedFile, 'video');
                    if ($media) {
                        $mediaIds[] = $media->id;
                    }
                }
            }

            $mediaIds = array_values(array_unique($mediaIds));
            if (count($mediaIds) === 0) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['media_ids' => 'Please select or upload at least one video.']);
            }

            // From media library
            foreach ($mediaIds as $mediaId) {
                $media = Media::findOrFail($mediaId);
                
                // Ensure it's a video
                if ($media->file_type !== 'video') {
                    continue;
                }

                Gallery::create([
                    'type' => 'video',
                    'category' => $request->category,
                    'media_id' => $mediaId,
                    'title' => $request->title ?: $media->title,
                    'description' => $request->description ?: $media->description,
                    'caption' => $media->caption, // Always use from media library
                    'gallery_slug' => $gallerySlug,
                    'sort_order' => $sortOrder++,
                    'is_exclusive' => $request->boolean('is_exclusive', false),
                    'status' => $request->boolean('status', true),
                    'language' => $request->language ?: getLangauge(),
                    'created_by' => Auth::guard('admin')->id(),
                    'created_by_type' => 'App\Models\Admin',
                ]);
            }
        } else {
            // External video URLs
            foreach ($request->video_urls as $videoUrl) {
                $videoInfo = Gallery::extractVideoInfo($videoUrl);
                
                Gallery::create([
                    'type' => 'video',
                    'category' => $request->category,
                    'video_url' => $videoUrl,
                    'video_platform' => $videoInfo['platform'],
                    'video_id' => $videoInfo['video_id'],
                    'title' => $request->title,
                    'description' => $request->description,
                    'caption' => null, // No caption for external videos
                    'gallery_slug' => $gallerySlug,
                    'sort_order' => $sortOrder++,
                    'is_exclusive' => $request->boolean('is_exclusive', false),
                    'status' => $request->boolean('status', true),
                    'language' => $request->language ?: getLangauge(),
                    'created_by' => Auth::guard('admin')->id(),
                    'created_by_type' => 'App\Models\Admin',
                ]);
            }
        }

        toast(__('admin.Created Successfully'), 'success')->width('400');
        return redirect()->route('admin.video-gallery.index');
    }

    /**
     * Display the specified video gallery.
     */
    public function show(string $id)
    {
        $gallery = Gallery::videos()->with(['media', 'creator'])->findOrFail($id);
        return view('admin.video-gallery.show', compact('gallery'));
    }

    /**
     * Show the form for editing the specified video gallery.
     */
    public function edit(string $id)
    {
        $gallery = Gallery::videos()->with('media')->findOrFail($id);
        return view('admin.video-gallery.edit', compact('gallery'));
    }

    /**
     * Update the specified video gallery.
     */
    public function update(Request $request, string $id)
    {
        $gallery = Gallery::videos()->findOrFail($id);

        $request->validate([
            'source_type' => ['required', 'in:media,external'],
            'uploaded_file' => ['nullable', 'file', 'mimes:mp4,webm,ogg,mov', 'max:51200'],
            'video_url' => ['required_if:source_type,external', 'nullable', 'url'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'gallery_slug' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
            'is_exclusive' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'boolean'],
            'language' => ['nullable', 'string', 'max:10'],
            'category' => ['required', 'in:UNB,AP'],
        ]);

        if ($request->source_type === 'media') {
            // Re-upload video
            if ($request->hasFile('uploaded_file')) {
                $media = $this->createMediaFromUpload($request->file('uploaded_file'), 'video');
                if ($media) {
                    $gallery->media_id = $media->id;
                    $gallery->caption = $media->caption;
                }
            }
            
            $gallery->video_url = null;
            $gallery->video_platform = null;
            $gallery->video_id = null;
        } else {
            // External video
            $videoInfo = Gallery::extractVideoInfo($request->video_url);
            
            $gallery->media_id = null;
            $gallery->video_url = $request->video_url;
            $gallery->video_platform = $videoInfo['platform'];
            $gallery->video_id = $videoInfo['video_id'];
            $gallery->caption = null;
        }

        $gallery->title = $request->title;
        $gallery->description = $request->description;
        $gallery->gallery_slug = $request->gallery_slug;
        $gallery->sort_order = $request->sort_order ?? $gallery->sort_order;
        $gallery->is_exclusive = $request->boolean('is_exclusive', $gallery->is_exclusive);
        $gallery->status = $request->boolean('status', $gallery->status);
        $gallery->language = $request->language ?? $gallery->language;
        $gallery->category = $request->category;
        $gallery->save();

        toast(__('admin.Updated Successfully'), 'success')->width('400');
        return redirect()->route('admin.video-gallery.index');
    }

    /**
     * Remove the specified video gallery.
     */
    public function destroy(string $id)
    {
        $gallery = Gallery::videos()->findOrFail($id);
        $gallery->delete();

        toast(__('admin.Deleted Successfully'), 'success')->width('400');
        return redirect()->route('admin.video-gallery.index');
    }

    private function createMediaFromUpload($file, string $expectedType): ?Media
    {
        if (!$file || !$file->isValid()) {
            return null;
        }

        $mimeType = $file->getMimeType();
        if (!str_starts_with((string) $mimeType, 'video/')) {
            return null;
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $filename = Str::random(40) . '.' . $extension;
        $storedPath = $file->storeAs('uploads/media', $filename, 'public');

        return Media::create([
            'filename' => $filename,
            'original_filename' => $file->getClientOriginalName(),
            'file_path' => 'storage/' . $storedPath,
            'file_url' => asset('storage/' . $storedPath),
            'file_type' => $expectedType,
            'mime_type' => $mimeType,
            'file_size' => $file->getSize(),
            'title' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'uploaded_by' => Auth::guard('admin')->id(),
            'uploaded_by_type' => 'App\Models\Admin',
        ]);
    }
}
