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
        $request->validate([
            'source_type' => ['required', 'in:media,external'],
            'media_ids' => ['required_if:source_type,media', 'array'],
            'media_ids.*' => ['required_if:source_type,media', 'exists:media,id'],
            'video_urls' => ['required_if:source_type,external', 'array'],
            'video_urls.*' => ['required_if:source_type,external', 'url'],
            'gallery_slug' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_exclusive' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'boolean'],
            'language' => ['nullable', 'string', 'max:10'],
        ]);

        $gallerySlug = $request->gallery_slug ?: 'gallery-' . Str::random(8);
        $sortOrder = 0;

        if ($request->source_type === 'media') {
            // From media library
            foreach ($request->media_ids as $mediaId) {
                $media = Media::findOrFail($mediaId);
                
                // Ensure it's a video
                if ($media->file_type !== 'video') {
                    continue;
                }

                Gallery::create([
                    'type' => 'video',
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
            'media_id' => ['required_if:source_type,media', 'nullable', 'exists:media,id'],
            'video_url' => ['required_if:source_type,external', 'nullable', 'url'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'gallery_slug' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
            'is_exclusive' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'boolean'],
            'language' => ['nullable', 'string', 'max:10'],
        ]);

        if ($request->source_type === 'media') {
            // From media library
            if ($request->filled('media_id')) {
                $media = Media::findOrFail($request->media_id);
                if ($media->file_type !== 'video') {
                    return redirect()->back()->withErrors(['media_id' => 'Selected media must be a video.']);
                }
            }
            
            $gallery->media_id = $request->media_id;
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
        }

        $gallery->title = $request->title;
        $gallery->description = $request->description;
        // Caption is taken from media library for media sources, null for external
        if ($request->source_type === 'media' && $request->filled('media_id') && $gallery->media_id != $request->media_id) {
            $newMedia = Media::findOrFail($request->media_id);
            $gallery->caption = $newMedia->caption;
        } elseif ($request->source_type === 'external') {
            $gallery->caption = null;
        }
        $gallery->gallery_slug = $request->gallery_slug;
        $gallery->sort_order = $request->sort_order ?? $gallery->sort_order;
        $gallery->is_exclusive = $request->boolean('is_exclusive', $gallery->is_exclusive);
        $gallery->status = $request->boolean('status', $gallery->status);
        $gallery->language = $request->language ?? $gallery->language;
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
}
