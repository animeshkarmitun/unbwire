<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ApPhoto;
use App\Models\ApPhotoItem;
use App\Models\ApPhotoCategory;
use App\Models\ApPhotoTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $photo = ApPhoto::with(['category', 'items', 'tags'])->findOrFail($id);
        
        return view('frontend.ap-photo.show', compact('photo'));
    }
}
