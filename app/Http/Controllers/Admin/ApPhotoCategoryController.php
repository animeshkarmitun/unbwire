<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApPhotoCategory;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApPhotoCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:ap photo category index,admin'])->only(['index']);
        $this->middleware(['permission:ap photo category create,admin'])->only(['create', 'store']);
        $this->middleware(['permission:ap photo category update,admin'])->only(['edit', 'update']);
        $this->middleware(['permission:ap photo category delete,admin'])->only(['destroy']);
    }

    public function index()
    {
        $categories = ApPhotoCategory::with('parent')->orderBy('order')->get();
        return view('admin.ap-photo-category.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = ApPhotoCategory::whereNull('parent_id')
            ->orderBy('order')
            ->get();
        
        return view('admin.ap-photo-category.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:0,1'],
            'order' => ['nullable', 'integer', 'min:0'],
            'parent_id' => ['nullable', 'exists:ap_photo_categories,id'],
        ]);

        if ($request->parent_id) {
            $parent = ApPhotoCategory::findOrFail($request->parent_id);
            if ($parent->parent_id) {
                return redirect()->back()->withErrors(['parent_id' => 'Subcategories cannot have their own subcategories.'])->withInput();
            }
        }

        $category = new ApPhotoCategory();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->status = (bool) $request->status;
        $category->order = $request->order ?? 0;
        $category->parent_id = $request->parent_id;
        $category->save();

        toast(__('admin.Created Successfully'), 'success')->width('350');

        return redirect()->route('admin.ap-photo-category.index');
    }

    public function edit(string $id)
    {
        $category = ApPhotoCategory::findOrFail($id);
        $parentCategories = ApPhotoCategory::whereNull('parent_id')
            ->where('id', '!=', $id)
            ->get();
        return view('admin.ap-photo-category.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, string $id)
    {
        $category = ApPhotoCategory::findOrFail($id);
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:0,1'],
            'order' => ['nullable', 'integer', 'min:0'],
            'parent_id' => ['nullable', 'exists:ap_photo_categories,id'],
        ]);

        if ($request->parent_id) {
            if ($request->parent_id == $id) {
                return redirect()->back()->withErrors(['parent_id' => 'Category cannot be its own parent.'])->withInput();
            }
            $parent = ApPhotoCategory::findOrFail($request->parent_id);
            if ($parent->parent_id) {
                return redirect()->back()->withErrors(['parent_id' => 'Subcategories cannot have their own subcategories.'])->withInput();
            }
        }

        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->status = (bool) $request->status;
        $category->order = $request->order ?? 0;
        $category->parent_id = $request->parent_id;
        $category->save();

        toast(__('admin.Updated Successfully'), 'success')->width('350');

        return redirect()->route('admin.ap-photo-category.index');
    }

    public function destroy(string $id)
    {
        try {
            $category = ApPhotoCategory::findOrFail($id);
            $category->delete();
            return response(['status' => 'success', 'message' => __('admin.Deleted Successfully!')]);
        } catch (\Throwable $th) {
            return response(['status' => 'error', 'message' => __('admin.something went wrong!')]);
        }
    }
}
