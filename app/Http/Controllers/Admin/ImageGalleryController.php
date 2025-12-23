<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ImageGalleryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:image gallery index,admin'])->only(['index', 'show']);
        $this->middleware(['permission:image gallery create,admin'])->only(['create', 'store']);
        $this->middleware(['permission:image gallery update,admin'])->only(['edit', 'update']);
        $this->middleware(['permission:image gallery delete,admin'])->only(['destroy']);
    }

    /**
     * Display a listing of image galleries.
     */
    public function index(Request $request)
    {
        $query = Gallery::images()->with(['media', 'creator'])->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
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
            $gallerySlugs = Gallery::images()
                ->select('gallery_slug')
                ->whereNotNull('gallery_slug')
                ->distinct()
                ->pluck('gallery_slug');
        } catch (\Exception $e) {
            // Column doesn't exist yet, return empty collection
            $gallerySlugs = collect();
        }

        return view('admin.image-gallery.index', compact('galleries', 'gallerySlugs'));
    }

    /**
     * Show the form for creating a new image gallery.
     */
    public function create()
    {
        return view('admin.image-gallery.create');
    }

    /**
     * Store a newly created image gallery.
     */
    public function store(Request $request)
    {
        // Convert comma-separated string to array if needed
        if ($request->has('media_ids') && is_string($request->media_ids)) {
            $mediaIds = array_filter(array_map('trim', explode(',', $request->media_ids)));
            $request->merge(['media_ids' => $mediaIds]);
        }

        $request->validate([
            'media_ids' => ['required', 'array', 'min:1'],
            'media_ids.*' => ['required', 'exists:media,id'],
            'gallery_slug' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_exclusive' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'boolean'],
            'language' => ['nullable', 'string', 'max:10'],
        ]);

        $gallerySlug = $request->gallery_slug ?: 'gallery-' . Str::random(8);
        $mediaIds = $request->media_ids;
        $sortOrder = 0;

        foreach ($mediaIds as $mediaId) {
            $media = Media::findOrFail($mediaId);
            
            // Ensure it's an image
            if ($media->file_type !== 'image') {
                continue;
            }

            Gallery::create([
                'type' => 'image',
                'media_id' => $mediaId,
                'title' => $request->title ?: $media->title,
                'description' => $request->description ?: $media->description,
                'alt_text' => $media->alt_text, // Always use from media library
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

        toast(__('admin.Created Successfully'), 'success')->width('400');
        return redirect()->route('admin.image-gallery.index');
    }

    /**
     * Display the specified image gallery.
     */
    public function show(string $id)
    {
        $gallery = Gallery::images()->with(['media', 'creator'])->findOrFail($id);
        return view('admin.image-gallery.show', compact('gallery'));
    }

    /**
     * Show the form for editing the specified image gallery.
     */
    public function edit(string $id)
    {
        $gallery = Gallery::images()->with('media')->findOrFail($id);
        return view('admin.image-gallery.edit', compact('gallery'));
    }

    /**
     * Update the specified image gallery.
     */
    public function update(Request $request, string $id)
    {
        $gallery = Gallery::images()->findOrFail($id);

        $request->validate([
            'media_id' => ['nullable', 'exists:media,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'gallery_slug' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
            'is_exclusive' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'boolean'],
            'language' => ['nullable', 'string', 'max:10'],
        ]);

        // If media_id is provided, validate it's an image
        if ($request->filled('media_id')) {
            $media = Media::findOrFail($request->media_id);
            if ($media->file_type !== 'image') {
                return redirect()->back()->withErrors(['media_id' => 'Selected media must be an image.']);
            }
            $gallery->media_id = $request->media_id;
        }

        $gallery->title = $request->title;
        $gallery->description = $request->description;
        // Alt text and caption are taken from media library, update if media changed
        if ($request->filled('media_id') && $gallery->media_id != $request->media_id) {
            $newMedia = Media::findOrFail($request->media_id);
            $gallery->alt_text = $newMedia->alt_text;
            $gallery->caption = $newMedia->caption;
        }
        $gallery->gallery_slug = $request->gallery_slug;
        $gallery->sort_order = $request->sort_order ?? $gallery->sort_order;
        $gallery->is_exclusive = $request->boolean('is_exclusive', $gallery->is_exclusive);
        $gallery->status = $request->boolean('status', $gallery->status);
        $gallery->language = $request->language ?? $gallery->language;
        $gallery->save();

        toast(__('admin.Updated Successfully'), 'success')->width('400');
        return redirect()->route('admin.image-gallery.index');
    }

    /**
     * Remove the specified image gallery.
     */
    public function destroy(string $id)
    {
        $gallery = Gallery::images()->findOrFail($id);
        $gallery->delete();

        toast(__('admin.Deleted Successfully'), 'success')->width('400');
        return redirect()->route('admin.image-gallery.index');
    }
}
