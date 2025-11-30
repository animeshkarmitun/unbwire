<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicketTag;
use Illuminate\Http\Request;

class SupportTicketTagController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:support tickets index,admin'])->only(['index']);
        $this->middleware(['permission:support tickets create,admin'])->only(['create', 'store']);
        $this->middleware(['permission:support tickets update,admin'])->only(['edit', 'update']);
        $this->middleware(['permission:support tickets delete,admin'])->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tags = SupportTicketTag::withCount('tickets')->orderBy('name')->get();
        return view('admin.support-ticket-tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.support-ticket-tags.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:support_ticket_tags,name'],
            'color' => ['nullable', 'string', 'regex:/^#[a-fA-F0-9]{6}$/'],
        ]);

        $color = $request->color_hex ?? $request->color ?? '#6c757d';
        
        SupportTicketTag::create([
            'name' => $request->name,
            'color' => $color,
        ]);

        toast(__('Tag created successfully'), 'success');

        return redirect()->route('admin.support-ticket-tags.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $tag = SupportTicketTag::findOrFail($id);
        return view('admin.support-ticket-tags.edit', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tag = SupportTicketTag::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:support_ticket_tags,name,' . $id],
            'color' => ['nullable', 'string', 'regex:/^#[a-fA-F0-9]{6}$/'],
        ]);

        $color = $request->color_hex ?? $request->color ?? $tag->color;
        
        $tag->update([
            'name' => $request->name,
            'color' => $color,
        ]);

        toast(__('Tag updated successfully'), 'success');

        return redirect()->route('admin.support-ticket-tags.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tag = SupportTicketTag::findOrFail($id);
        $tag->delete();

        toast(__('Tag deleted successfully'), 'success');

        return redirect()->route('admin.support-ticket-tags.index');
    }
}

