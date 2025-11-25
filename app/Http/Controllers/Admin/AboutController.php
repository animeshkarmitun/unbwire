<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\About;
use App\Models\Language;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:about index,admin'])->only(['index']);
        $this->middleware(['permission:about update,admin'])->only(['update']);
    }

    public function index()
    {
        $languages = Language::all();

        return view('admin.about-page.index', compact('languages'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'content' => ['required'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ]);

        About::updateOrCreate(
            ['language' => $request->language],
            [
                'content' => $request->content,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
            ]
        );

        toast(__('admin.Updated Successfully!'), 'success');

        return redirect()->back();
    }
}
