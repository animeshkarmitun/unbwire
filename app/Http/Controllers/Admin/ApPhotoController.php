<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApPhoto;
use App\Models\ApPhotoCategory;
use App\Models\ApPhotoItem;
use App\Models\ApPhotoTag;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApPhotoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:ap photo index,admin'])->only(['index']);
        $this->middleware(['permission:ap photo create,admin'])->only(['create', 'store']);
        $this->middleware(['permission:ap photo update,admin'])->only(['edit', 'update']);
        $this->middleware(['permission:ap photo delete,admin'])->only(['destroy']);
    }

    public function index(Request $request)
    {
        $photos = ApPhoto::with(['category', 'items'])
            ->orderBy('id', 'DESC')
            ->paginate(20);

        return view('admin.ap-photo.index', compact('photos'));
    }

    public function create()
    {
        $categories = ApPhotoCategory::whereNull('parent_id')->orderBy('order')->get();
        $tags = ApPhotoTag::all();
        return view('admin.ap-photo.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:ap_photo_categories,id'],
            'sub_category_id' => ['nullable', 'exists:ap_photo_categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:ap_photo_tags,id'],
            'photos' => ['required', 'array'],
            'photos.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:5120'], // 5MB max
        ]);

        $apPhoto = new ApPhoto();
        $apPhoto->description = $request->description;
        $apPhoto->category_id = $request->category_id;
        $apPhoto->sub_category_id = $request->sub_category_id;
        $apPhoto->user_id = auth()->guard('admin')->user()->id;
        $apPhoto->status = true;
        $apPhoto->save();

        // Handle Tags
        if ($request->has('tags')) {
            $apPhoto->tags()->attach($request->tags);
        }

        // Handle Multiple Photo Uploads
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('ap-photos', $filename, 'public');
                $url = asset('storage/' . $path);

                ApPhotoItem::create([
                    'ap_photo_id' => $apPhoto->id,
                    'filename' => $filename,
                    'file_path' => $path,
                    'file_url' => $url,
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        toast(__('admin.Created Successfully'), 'success')->width('350');

        return redirect()->route('admin.ap-photo.index');
    }

    public function edit(string $id)
    {
        $photo = ApPhoto::with(['items', 'tags'])->findOrFail($id);
        $categories = ApPhotoCategory::whereNull('parent_id')
            ->get();
            
        $subCategories = ApPhotoCategory::where('parent_id', $photo->category_id)->get();

        return view('admin.ap-photo.edit', compact('photo', 'tags', 'categories', 'subCategories'));
    }

    public function update(Request $request, string $id)
    {
        $apPhoto = ApPhoto::findOrFail($id);
        
        $request->validate([
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:ap_photo_categories,id'],
            'sub_category_id' => ['nullable', 'exists:ap_photo_categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:ap_photo_tags,id'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
        ]);

        $apPhoto->description = $request->description;
        $apPhoto->category_id = $request->category_id;
        $apPhoto->sub_category_id = $request->sub_category_id;
        $apPhoto->save();

        // Update Tags
        $apPhoto->tags()->sync($request->tags ?? []);

        // Handle New Photo Uploads
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('ap-photos', $filename, 'public');
                $url = asset('storage/' . $path);

                ApPhotoItem::create([
                    'ap_photo_id' => $apPhoto->id,
                    'filename' => $filename,
                    'file_path' => $path,
                    'file_url' => $url,
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        toast(__('admin.Updated Successfully'), 'success')->width('350');

        return redirect()->route('admin.ap-photo.index');
    }

    public function destroy(string $id)
    {
        try {
            $apPhoto = ApPhoto::with('items')->findOrFail($id);
            
            // Delete physical files
            foreach ($apPhoto->items as $item) {
                if (Storage::disk('public')->exists($item->file_path)) {
                    Storage::disk('public')->delete($item->file_path);
                }
            }
            
            $apPhoto->delete();
            return response(['status' => 'success', 'message' => __('admin.Deleted Successfully!')]);
        } catch (\Throwable $th) {
            return response(['status' => 'error', 'message' => __('admin.something went wrong!')]);
        }
    }

    public function deleteItem(string $id)
    {
        try {
            $item = ApPhotoItem::findOrFail($id);
            if (Storage::disk('public')->exists($item->file_path)) {
                Storage::disk('public')->delete($item->file_path);
            }
            $item->delete();
            return response(['status' => 'success', 'message' => __('admin.Photo Deleted Successfully!')]);
        } catch (\Throwable $th) {
            return response(['status' => 'error', 'message' => __('admin.something went wrong!')]);
        }
    }

    public function fetchCategories(Request $request)
    {
        $categories = ApPhotoCategory::whereNull('parent_id')
            ->orderBy('order', 'asc')
            ->get();
        return response()->json($categories);
    }

    public function fetchSubCategories(Request $request)
    {
        $subCategories = ApPhotoCategory::where('parent_id', $request->category_id)
            ->orderBy('order', 'asc')
            ->get();
        return response()->json($subCategories);
    }
}
