<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GalleryController extends Controller
{
    /**
     * Display UNB Image Gallery for subscribers
     */
    public function images(Request $request)
    {
        $user = Auth::user();
        
        // Subscription & Permission check
        if (!$user->hasSubscriptionAccess('images')) {
            return redirect()
                ->route('subscription.plans')
                ->with('error', 'Your current plan does not include access to the Image Gallery.');
        }

        // Fetch unique gallery groups (using gallery_slug)
        // For each group, we show the first image and the total count
        $galleries = Gallery::images()
            ->where('category', 'UNB')
            ->where('status', true)
            ->whereNotNull('gallery_slug')
            ->select('gallery_slug', 'title', 'description', 'created_at')
            ->groupBy('gallery_slug', 'title', 'description', 'created_at')
            ->latest()
            ->paginate(12);

        // For each group, attach the cover image
        foreach($galleries as $gallery) {
            $gallery->cover_image = Gallery::where('gallery_slug', $gallery->gallery_slug)
                ->where('status', true)
                ->with('media')
                ->first()?->media?->file_url;
            
            $gallery->photo_count = Gallery::where('gallery_slug', $gallery->gallery_slug)
                ->where('status', true)
                ->count();
        }

        return view('frontend.gallery.images', compact('galleries'));
    }

    /**
     * Display UNB Video Gallery for subscribers
     */
    public function videos(Request $request)
    {
        $user = Auth::user();
        
        // Subscription & Permission check
        if (!$user->hasSubscriptionAccess('videos')) {
            return redirect()
                ->route('subscription.plans')
                ->with('error', 'Your current plan does not include access to the Video Gallery.');
        }

        $videos = Gallery::videos()
            ->where('category', 'UNB')
            ->where('status', true)
            ->with(['media'])
            ->latest()
            ->paginate(12);

        return view('frontend.gallery.videos', compact('videos'));
    }
}
