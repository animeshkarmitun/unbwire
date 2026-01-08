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
        // Allow access to create form - permission check happens in create() method
        // This allows users with language-specific permissions to access the form
        $this->middleware(['permission:category create,admin'])->only(['store']);
        $this->middleware(['permission:category update,admin'])->only(['edit', 'update']);
        $this->middleware(['permission:category delete,admin'])->only(['destroy']);
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
    public function create($lang = null)
    {
        // Validate language if provided
        if ($lang && !in_array($lang, ['en', 'bn'])) {
            abort(404);
        }
        
        // Check permission for the specific language
        if ($lang) {
            $hasGeneralPermission = canAccess(['category create', 'news all-access']);
            $hasLanguagePermission = $lang === 'en' 
                ? canAccess(['category create en']) 
                : canAccess(['category create bn']);
            
            if (!$hasGeneralPermission && !$hasLanguagePermission) {
                $langName = $lang === 'en' ? 'English' : 'Bangla';
                abort(403, "You do not have permission to create {$langName} categories.");
            }
        } else {
            // If no language specified, check if user has any create permission
            $hasAnyPermission = canAccess(['category create', 'category create en', 'category create bn', 'news all-access']);
            if (!$hasAnyPermission) {
                abort(403, 'You do not have permission to create categories.');
            }
        }
        
        $languages = Language::all();
        
        // Fetch parent categories - filter by language if provided
        $parentCategoriesQuery = Category::whereNull('parent_id');
        
        if ($lang) {
            // Only show categories of the same language
            $parentCategoriesQuery->where('language', $lang);
        }
        
        $parentCategories = $parentCategoriesQuery
            ->orderBy('order')
            ->orderBy('name')
            ->get();
        
        // If language is provided, pre-select it
        $selectedLanguage = $lang ? Language::where('lang', $lang)->first() : null;
        
        return view('admin.category.create', compact('languages', 'parentCategories', 'selectedLanguage'));
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
        
        // Check language-specific permission
        $language = $request->language;
        if ($language === 'en' && !canAccess(['category create en', 'category create', 'news all-access'])) {
            abort(403, 'You do not have permission to create English categories.');
        }
        if ($language === 'bn' && !canAccess(['category create bn', 'category create', 'news all-access'])) {
            abort(403, 'You do not have permission to create Bangla categories.');
        }

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
        $category = Category::findOrFail($id);
        
        // Check language-specific permission
        $language = $request->language ?? $category->language;
        if ($language === 'en' && !canAccess(['category update en', 'category update', 'news all-access'])) {
            abort(403, 'You do not have permission to update English categories.');
        }
        if ($language === 'bn' && !canAccess(['category update bn', 'category update', 'news all-access'])) {
            abort(403, 'You do not have permission to update Bangla categories.');
        }
        
        $request->validate([
            'language' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'show_at_nav' => ['required', 'in:0,1'],
            'status' => ['required', 'in:0,1'],
            'order' => ['nullable', 'integer', 'min:0'],
            'parent_id' => ['nullable', 'exists:categories,id'],
        ]);

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

