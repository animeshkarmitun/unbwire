<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ApPhoto;
use App\Models\ApPhotoItem;
use App\Models\ApPhotoCategory;
use App\Models\ApPhotoTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Barryvdh\DomPDF\Facade\Pdf;

class ApPhotoGalleryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display AP Photo list for subscribers (Individual Photos)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Mandatory subscription check
        if (!$user->canAccessApPhoto()) {
            return redirect()
                ->route('subscription.plans')
                ->with('error', 'You need a subscription plan that includes AP Photo access to view this gallery.');
        }

        // Query ApPhotoItem (Individual Photos) instead of ApPhoto (Collections)
        $query = ApPhotoItem::whereHas('apPhoto', function($q) {
            $q->where('status', true);
        })->with(['apPhoto.category', 'apPhoto.tags']);

        // Updated for Multi-select: Category Filtering
        if ($request->filled('categories')) {
            $categories = is_array($request->categories) ? $request->categories : explode(',', $request->categories);
            $query->whereHas('apPhoto.category', function($q) use ($categories) {
                $q->whereIn('slug', $categories);
            });
        }

        // Updated for Multi-select: Tag Filtering
        if ($request->filled('tags')) {
            $tags = is_array($request->tags) ? $request->tags : explode(',', $request->tags);
            $query->whereHas('apPhoto.tags', function($q) use ($tags) {
                $q->whereIn('slug', $tags);
            });
        }

        // Paginate individual photos
        $photos = $query->latest()->paginate(18);

        // AJAX response for Infinite Scroll
        if ($request->ajax()) {
            return view('frontend.ap-photo.partials._photo_items', compact('photos'))->render();
        }

        $categories = ApPhotoCategory::with('children')
            ->where('status', true)
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();

        $tags = ApPhotoTag::orderBy('name')->get();

        return view('frontend.ap-photo.index', compact('photos', 'categories', 'tags'));
    }

    public function show(string $id)
    {
        $user = Auth::user();
        
        if (!$user->canAccessApPhoto()) {
            return redirect()
                ->route('subscription.plans')
                ->with('error', 'You need a subscription plan that includes AP Photo access to view this content.');
        }

        $activeItem = ApPhotoItem::with(['apPhoto.category', 'apPhoto.items', 'apPhoto.tags', 'apPhoto.user'])->findOrFail($id);
        $photo = $activeItem->apPhoto;
        
        // Global navigation: Find next and previous individual photos across all categories/collections
        $prev = ApPhotoItem::where('id', '>', $id)
            ->orderBy('id', 'asc')
            ->first();
            
        $next = ApPhotoItem::where('id', '<', $id)
            ->orderBy('id', 'desc')
            ->first();
        
        return view('frontend.ap-photo.show', compact('photo', 'activeItem', 'prev', 'next'));
    }

    /**
     * Download AP Photo item in specified format
     */
    public function download($itemId, $format)
    {
        $user = Auth::user();
        
        if (!$user->canAccessApPhoto()) {
            abort(403, 'Unauthorized access.');
        }

        $item = ApPhotoItem::with('apPhoto.category')->findOrFail($itemId);
        $filePath = Storage::disk('public')->path($item->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'File not found.');
        }

        $filename = ($item->apPhoto->category->name ?? 'ap-photo') . '-' . $item->id;

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('frontend.ap-photo.pdf', compact('item'));
            return $pdf->download($filename . '.pdf');
        }

        // Handle Image formats (png, jpg, webp)
        $manager = new ImageManager(new Driver());
        $image = $manager->read($filePath);
        
        switch (strtolower($format)) {
            case 'png':
                $encoded = $image->toPng();
                $contentType = 'image/png';
                break;
            case 'webp':
                $encoded = $image->toWebp();
                $contentType = 'image/webp';
                break;
            case 'jpg':
            case 'jpeg':
            default:
                $encoded = $image->toJpeg();
                $contentType = 'image/jpeg';
                $format = 'jpg';
                break;
        }

        return response($encoded)
            ->header('Content-Type', $contentType)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '.' . $format . '"');
    }
}
