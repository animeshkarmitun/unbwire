<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Language;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:contact index,admin'])->only(['index']);
        $this->middleware(['permission:contact update,admin'])->only(['update']);
    }

    public function index()
    {
        $languages = Language::all();
        return view('admin.contact-page.index', compact('languages'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'address' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'email' => ['required', 'email'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ]);

       Contact::updateOrCreate(
            ['language' => $request->language],
            [
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
            ]
        );

        toast(__('admin.Updated Successfully'), 'success');

        return redirect()->back();
    }
}
