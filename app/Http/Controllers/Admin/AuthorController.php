<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Language;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:author index,admin'])->only(['index']);
        // Permission check for create is done in the create() method to support language-specific permissions
        $this->middleware(['permission:author create,admin'])->only(['store']);
        $this->middleware(['permission:author update,admin'])->only(['edit', 'update']);
        $this->middleware(['permission:author delete,admin'])->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $languages = Language::all();
        $selectedLang = $request->get('lang');
        
        $query = Author::orderBy('created_at', 'desc');
        
        // Filter by language if selected and user has permission
        if ($selectedLang) {
            $canViewLang = canAccess(['news all-access', 'author view', 'author view ' . $selectedLang]);
            if ($canViewLang) {
                $query->where('language', $selectedLang);
            } else {
                // If no permission, return empty or redirect
                $query->where('id', 0); // Return no results
            }
        } else {
            // If viewing all, filter by languages user can view
            $allowedLanguages = [];
            foreach ($languages as $lang) {
                $canViewLang = canAccess(['news all-access', 'author view', 'author view ' . $lang->lang]);
                if ($canViewLang) {
                    $allowedLanguages[] = $lang->lang;
                }
            }
            if (!empty($allowedLanguages)) {
                $query->whereIn('language', $allowedLanguages);
            } else {
                $query->where('id', 0); // Return no results if no permissions
            }
        }
        
        $authors = $query->get();
        
        return view('admin.author.index', compact('authors', 'languages', 'selectedLang'));
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
            $hasGeneralPermission = canAccess(['author create', 'news all-access']);
            $hasLanguagePermission = $lang === 'en' 
                ? canAccess(['author create en']) 
                : canAccess(['author create bn']);
            
            if (!$hasGeneralPermission && !$hasLanguagePermission) {
                $langName = $lang === 'en' ? 'English' : 'Bangla';
                abort(403, "You do not have permission to create {$langName} authors.");
            }
        } else {
            // If no language specified, check if user has any create permission
            $hasAnyPermission = canAccess(['author create', 'author create en', 'author create bn', 'news all-access']);
            if (!$hasAnyPermission) {
                abort(403, 'You do not have permission to create authors.');
            }
        }
        
        $languages = Language::all();
        
        // If language is provided, pre-select it
        $selectedLanguage = $lang ? Language::where('lang', $lang)->first() : null;
        
        return view('admin.author.create', compact('languages', 'selectedLanguage'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'language' => ['required', 'string', 'in:en,bn'],
            'designation' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:0,1'],
        ]);
        
        // Check language-specific permission
        $language = $request->language;
        if ($language === 'en' && !canAccess(['author create en', 'author create', 'news all-access'])) {
            abort(403, 'You do not have permission to create English authors.');
        }
        if ($language === 'bn' && !canAccess(['author create bn', 'author create', 'news all-access'])) {
            abort(403, 'You do not have permission to create Bangla authors.');
        }

        $author = new Author();
        $author->name = $request->name;
        $author->language = $request->language;
        $author->designation = $request->designation;
        $author->photo = $request->photo;
        $author->status = (bool) $request->status;
        $author->save();

        toast(__('admin.Created Successfully'), 'success')->width('350');

        return redirect()->route('admin.author.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $author = Author::findOrFail($id);
        return view('admin.author.edit', compact('author'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $author = Author::findOrFail($id);
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'language' => ['required', 'string', 'in:en,bn'],
            'designation' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:0,1'],
        ]);
        
        // Check language-specific permission
        $language = $request->language ?? $author->language;
        if ($language === 'en' && !canAccess(['author update en', 'author update', 'news all-access'])) {
            abort(403, 'You do not have permission to update English authors.');
        }
        if ($language === 'bn' && !canAccess(['author update bn', 'author update', 'news all-access'])) {
            abort(403, 'You do not have permission to update Bangla authors.');
        }

        $author->name = $request->name;
        $author->language = $request->language;
        $author->designation = $request->designation;
        $author->photo = $request->photo;
        $author->status = (bool) $request->status;
        $author->save();

        toast(__('admin.Updated Successfully'), 'success')->width('350');

        return redirect()->route('admin.author.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $author = Author::findOrFail($id);
            
            // Check language-specific permission
            $language = $author->language;
            if ($language === 'en' && !canAccess(['author delete en', 'author delete', 'news all-access'])) {
                abort(403, 'You do not have permission to delete English authors.');
            }
            if ($language === 'bn' && !canAccess(['author delete bn', 'author delete', 'news all-access'])) {
                abort(403, 'You do not have permission to delete Bangla authors.');
            }
            
            $author->delete();

            return response(['status' => 'success', 'message' => __('admin.Deleted Successfully!')]);
        } catch (\Throwable $th) {
            return response(['status' => 'error', 'message' => __('admin.something went wrong!')]);
        }
    }
}
