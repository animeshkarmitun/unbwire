<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApPhotoTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApPhotoTagController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:ap photo tag index,admin'])->only(['index']);
        $this->middleware(['permission:ap photo tag create,admin'])->only(['create', 'store', 'edit', 'update']);
        $this->middleware(['permission:ap photo tag delete,admin'])->only(['destroy']);
    }

    public function index()
    {
        $tags = ApPhotoTag::all();
        return view('admin.ap-photo-tag.index', compact('tags'));
    }

    public function create()
    {
        return view('admin.ap-photo-tag.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:ap_photo_tags,name'],
        ]);

        $tag = new ApPhotoTag();
        $tag->name = $request->name;
        $tag->slug = Str::slug($request->name);
        $tag->save();

        toast(__('admin.Created Successfully'), 'success')->width('350');

        return redirect()->route('admin.ap-photo-tag.index');
    }

    public function edit(string $id)
    {
        $tag = ApPhotoTag::findOrFail($id);
        return view('admin.ap-photo-tag.edit', compact('tag'));
    }

    public function update(Request $request, string $id)
    {
        $tag = ApPhotoTag::findOrFail($id);
        
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:ap_photo_tags,name,' . $id],
        ]);

        $tag->name = $request->name;
        $tag->slug = Str::slug($request->name);
        $tag->save();

        toast(__('admin.Updated Successfully'), 'success')->width('350');

        return redirect()->route('admin.ap-photo-tag.index');
    }

    public function destroy(string $id)
    {
        try {
            $tag = ApPhotoTag::findOrFail($id);
            $tag->delete();
            return response(['status' => 'success', 'message' => __('admin.Deleted Successfully!')]);
        } catch (\Throwable $th) {
            return response(['status' => 'error', 'message' => __('admin.something went wrong!')]);
        }
    }
}
