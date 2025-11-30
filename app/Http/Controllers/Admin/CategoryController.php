<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:news index,admin'])->only(['index']);
        $this->middleware(['permission:news create,admin'])->only(['create', 'store']);
        $this->middleware(['permission:news update,admin'])->only(['edit', 'update']);
        $this->middleware(['permission:news delete,admin'])->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $languages = Language::all();
        return view('admin.category.index', compact('languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::all();
        $parentCategories = Category::whereNull('parent_id')
            ->orderBy('order')
            ->orderBy('name')
            ->get();
        return view('admin.category.create', compact('languages', 'parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'language' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'show_at_nav' => ['required', 'in:0,1'],
            'status' => ['required', 'in:0,1'],
            'order' => ['nullable', 'integer', 'min:0'],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ]);

        // Prevent category from being its own parent
        if ($request->parent_id) {
            $parent = Category::findOrFail($request->parent_id);
            if ($parent->parent_id) {
                return redirect()->back()->withErrors(['parent_id' => 'Subcategories cannot have their own subcategories.'])->withInput();
            }
        }

        $category = new Category();
        $category->language = $request->language;
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->show_at_nav = (bool) $request->show_at_nav;
        $category->status = (bool) $request->status;
        $category->order = $request->order ?? 0;
        $category->parent_id = $request->parent_id;
        $category->save();

        toast(__('admin.Created Successfully'), 'success')->width('350');

        return redirect()->route('admin.category.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        $languages = Language::all();
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $id)
            ->where('language', $category->language)
            ->orderBy('order')
            ->orderBy('name')
            ->get();
        return view('admin.category.edit', compact('category', 'languages', 'parentCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'language' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'show_at_nav' => ['required', 'in:0,1'],
            'status' => ['required', 'in:0,1'],
            'order' => ['nullable', 'integer', 'min:0'],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ]);

        $category = Category::findOrFail($id);

        // Prevent category from being its own parent or parent of its parent
        if ($request->parent_id) {
            if ($request->parent_id == $id) {
                return redirect()->back()->withErrors(['parent_id' => 'Category cannot be its own parent.'])->withInput();
            }
            $parent = Category::findOrFail($request->parent_id);
            if ($parent->parent_id) {
                return redirect()->back()->withErrors(['parent_id' => 'Subcategories cannot have their own subcategories.'])->withInput();
            }
            // Prevent circular reference
            if ($category->hasChildren() && $request->parent_id) {
                return redirect()->back()->withErrors(['parent_id' => 'Cannot move category with subcategories to be a subcategory.'])->withInput();
            }
        }

        $category->language = $request->language;
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->show_at_nav = (bool) $request->show_at_nav;
        $category->status = (bool) $request->status;
        $category->order = $request->order ?? 0;
        $category->parent_id = $request->parent_id;
        $category->save();

        toast(__('admin.Updated Successfully'), 'success')->width('350');

        return redirect()->route('admin.category.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();
            return response(['status' => 'success', 'message' => __('admin.Deleted Successfully!')]);
        } catch (\Throwable $th) {
            return response(['status' => 'error', 'message' => __('admin.something went wrong!')]);
        }
    }
}

