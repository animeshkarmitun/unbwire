<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminNewsCreateRequest;
use App\Http\Requests\AdminNewsUpdateRequest;
use App\Models\Category;
use App\Models\Language;
use App\Models\News;
use App\Models\Tag;
use App\Traits\FileUploadTrait;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NewsController extends Controller
{
    use FileUploadTrait;

    public function __construct()
    {
        $this->middleware(['permission:news index,admin'])->only(['index', 'copyNews']);
        $this->middleware(['permission:news create,admin'])->only(['create', 'store']);
        $this->middleware(['permission:news update,admin'])->only(['edit', 'update']);
        $this->middleware(['permission:news delete,admin'])->only(['destroy']);
        $this->middleware(['permission:news all-access,admin'])->only(['toggleNewsStatus']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $languages = Language::all();
        return view('admin.news.index', compact('languages'));
    }

    public function pendingNews(): View
    {
        $languages = Language::all();
        return view('admin.pending-news.index', compact('languages'));
    }


    /**
     * Fetch category depending on language
     */
    public function fetchCategory(Request $request)
    {
        $categories = Category::where('language', $request->lang)->get();
        return $categories;
    }

    function approveNews(Request $request): Response
    {
        $news = News::findOrFail($request->id);
        $news->is_approved = $request->is_approve;
        $news->save();

        return response(['status' => 'success', 'message' => __('admin.Updated Successfully')]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::all();
        return view('admin.news.create', compact('languages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminNewsCreateRequest $request)
    {
        /** Handle image - can be file upload or path from media library */
        $imagePath = null;
        if ($request->hasFile('image')) {
            // File upload
            $imagePath = $this->handleFileUpload($request, 'image');
        } elseif ($request->filled('image')) {
            // Path from media library
            $imagePath = $request->input('image');
        }

        $news = new News();
        $news->language = $request->language;
        $news->category_id = $request->category;
        $news->auther_id = Auth::guard('admin')->user()->id;
        $news->image = $imagePath;
        $news->title = $request->title;
        $news->slug = \Str::slug($request->title);
        $news->content = $request->content;
        $news->meta_title = $request->meta_title;
        $news->meta_description = $request->meta_description;
        $news->is_breaking_news = $request->is_breaking_news == 1 ? 1 : 0;
        $news->show_at_slider = $request->show_at_slider == 1 ? 1 : 0;
        $news->show_at_popular = $request->show_at_popular == 1 ? 1 : 0;
        $news->status = $request->status == 1 ? 1 : 0;
        $news->is_approved = getRole() == 'Super Admin' || checkPermission('news all-access') ? 1 : 0;
        $news->save();

        $tags = explode(',', $request->tags);
        $tagIds = [];

        foreach ($tags as $tag) {
            $item = new Tag();
            $item->name = $tag;
            $item->language = $news->language;
            $item->save();

            $tagIds[] = $item->id;
        }

        $news->tags()->attach($tagIds);


        toast(__('admin.Created Successfully!'), 'success')->width('330');

        return redirect()->route('admin.news.index');
    }

    /**
     * Change toggle status of news
     */
    public function toggleNewsStatus(Request $request)
    {
        try {
            $news = News::findOrFail($request->id);
            
            // Validate the field name to prevent mass assignment issues
            $allowedFields = ['is_breaking_news', 'show_at_slider', 'show_at_popular', 'status'];
            if (!in_array($request->name, $allowedFields)) {
                return response(['status' => 'error', 'message' => 'Invalid field name'], 400);
            }
            
            // Convert status to proper integer value (0 or 1)
            $value = (int)($request->status == 1 || $request->status === '1' || $request->status === true);
            
            // Store old value for logging
            $oldValue = $news->{$request->name};
            
            // Use DB::table for direct update to bypass any model events that might interfere
            // This ensures the update happens even if model events fail
            $updated = DB::table('news')
                ->where('id', $request->id)
                ->update([
                    $request->name => $value,
                    'updated_at' => now()
                ]);
            
            if ($updated === 0) {
                return response(['status' => 'error', 'message' => 'No rows updated'], 500);
            }
            
            // Refresh the model instance to get updated values
            $news->refresh();
            
            // Manually log the activity since we bypassed model events
            try {
                $news->logActivity('updated', 
                    [$request->name => $oldValue], 
                    [$request->name => $value], 
                    [$request->name => $value]
                );
            } catch (\Exception $e) {
                // Log error but don't fail the request
                \Log::warning('Failed to log activity: ' . $e->getMessage());
            }

            return response(['status' => 'success', 'message' => __('admin.Updated successfully!')]);
        } catch (\Throwable $th) {
            \Log::error('Toggle news status error: ' . $th->getMessage(), [
                'request' => $request->all(),
                'trace' => $th->getTraceAsString()
            ]);
            return response(['status' => 'error', 'message' => 'Failed to update: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $languages = Language::all();
        $news = News::findOrFail($id);
        
        if(!canAccess(['news all-access'])){
            if($news->auther_id != auth()->guard('admin')->user()->id){
                return abort(404);
            }
        }

        $categories = Category::where('language', $news->language)->get();

        return view('admin.news.edit', compact('languages', 'news', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminNewsUpdateRequest $request, string $id)
    {

        $news = News::findOrFail($id);

        if(!canAccess(['news all-access'])){
            if($news->auther_id != auth()->guard('admin')->user()->id){
                return abort(404);
            }
        }

        /** Handle image - can be file upload or path from media library */
        $imagePath = null;
        if ($request->hasFile('image')) {
            // File upload
            $imagePath = $this->handleFileUpload($request, 'image', $news->image);
        } elseif ($request->filled('image')) {
            // Path from media library
            $imagePath = $request->input('image');
        }

        $news->language = $request->language;
        $news->category_id = $request->category;
        $news->image = !empty($imagePath) ? $imagePath : $news->image;
        $news->title = $request->title;
        $news->slug = \Str::slug($request->title);
        $news->content = $request->content;
        $news->meta_title = $request->meta_title;
        $news->meta_description = $request->meta_description;
        $news->is_breaking_news = $request->is_breaking_news == 1 ? 1 : 0;
        $news->show_at_slider = $request->show_at_slider == 1 ? 1 : 0;
        $news->show_at_popular = $request->show_at_popular == 1 ? 1 : 0;
        $news->status = $request->status == 1 ? 1 : 0;
        $news->save();

        $tags = explode(',', $request->tags);
        $tagIds = [];

        /** Delete previos tags */
        $news->tags()->delete();

        /** detach tags form pivot table */
        $news->tags()->detach($news->tags);

        foreach ($tags as $tag) {
            $item = new Tag();
            $item->name = $tag;
            $item->language = $news->language;
            $item->save();

            $tagIds[] = $item->id;
        }

        $news->tags()->attach($tagIds);


        toast(__('admin.Update Successfully!'), 'success')->width('330');

        return redirect()->route('admin.news.index');
    }

    /**
     * Remove the specified resource from storage.
     * Moves news to archive table and moves assets to archive folder.
     */
    public function destroy(string $id)
    {
        try {
            $news = News::with(['tags'])->findOrFail($id);
            
            // Move image to archive folder
            $archivedImagePath = $this->moveFileToArchive($news->image);
            
            // If image move failed, keep original path
            $finalImagePath = $archivedImagePath ?? $news->image;
            
            // Move video file if it exists and is a local file (not external URL)
            $finalVideoUrl = $news->video_url;
            if (!empty($news->video_url) && !filter_var($news->video_url, FILTER_VALIDATE_URL)) {
                $archivedVideoPath = $this->moveFileToArchive($news->video_url);
                $finalVideoUrl = $archivedVideoPath ?? $news->video_url;
            }
            
            // Create archived news record
            $archivedNews = \App\Models\ArchivedNews::create([
                'original_id' => $news->id,
                'language' => $news->language,
                'category_id' => $news->category_id,
                'auther_id' => $news->auther_id,
                'image' => $finalImagePath,
                'title' => $news->title,
                'slug' => $news->slug,
                'content' => $news->content,
                'meta_title' => $news->meta_title,
                'meta_description' => $news->meta_description,
                'is_breaking_news' => $news->is_breaking_news,
                'show_at_slider' => $news->show_at_slider,
                'show_at_popular' => $news->show_at_popular,
                'is_approved' => $news->is_approved,
                'status' => $news->status,
                'views' => $news->views,
                'is_exclusive' => $news->is_exclusive ?? false,
                'video_url' => $finalVideoUrl ?? null,
                'subscription_required' => $news->subscription_required ?? 'free',
                'deleted_by' => auth()->guard('admin')->id(),
                'deleted_at' => now(),
            ]);
            
            // Archive tags relationship (store as JSON or create archive_tags table if needed)
            // For now, we'll just detach from original news
            $news->tags()->detach();
            
            // Delete the original news article
            $news->delete();

            return response(['status' => 'success', 'message' => __('admin.News archived successfully!')]);
        } catch (\Exception $e) {
            \Log::error('Error archiving news: ' . $e->getMessage());
            return response(['status' => 'error', 'message' => 'Error archiving news: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Copy news
     */
    public function copyNews(string $id)
    {
        $news = News::findOrFail($id);
        $copyNews = $news->replicate();
        $copyNews->save();

        toast(__('admin.Copied Successfully!'), 'success');

        return redirect()->back();
    }
}
