<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionPackageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:subscription package index,admin'])->only(['index']);
        $this->middleware(['permission:subscription package create,admin'])->only(['create', 'store']);
        $this->middleware(['permission:subscription package update,admin'])->only(['edit', 'update']);
        $this->middleware(['permission:subscription package delete,admin'])->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $packages = SubscriptionPackage::orderBy('sort_order')->get();
        return view('admin.subscription-package.index', compact('packages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.subscription-package.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:3'],
            'billing_period' => ['required', 'in:monthly,yearly'],
            'access_news' => ['boolean'],
            'access_images' => ['boolean'],
            'access_videos' => ['boolean'],
            'access_exclusive' => ['boolean'],
            'access_bangla' => ['boolean'],
            'access_english' => ['boolean'],
            'max_articles_per_day' => ['nullable', 'integer', 'min:1'],
            'ad_free' => ['boolean'],
            'priority_support' => ['boolean'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $package = new SubscriptionPackage();
        $package->name = $request->name;
        $package->slug = Str::slug($request->name);
        $package->description = $request->description;
        $package->price = $request->price;
        $package->currency = $request->currency;
        $package->billing_period = $request->billing_period;
        $package->access_news = $request->boolean('access_news');
        $package->access_images = $request->boolean('access_images');
        $package->access_videos = $request->boolean('access_videos');
        $package->access_exclusive = $request->boolean('access_exclusive');
        $package->access_bangla = $request->boolean('access_bangla');
        $package->access_english = $request->boolean('access_english');
        $package->max_articles_per_day = $request->max_articles_per_day;
        $package->ad_free = $request->boolean('ad_free');
        $package->priority_support = $request->boolean('priority_support');
        $package->is_active = $request->boolean('is_active');
        $package->sort_order = $request->sort_order ?? 0;
        $package->save();

        toast(__('admin.Created Successfully!'), 'success')->width('350');

        return redirect()->route('admin.subscription-package.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $package = SubscriptionPackage::findOrFail($id);
        return view('admin.subscription-package.edit', compact('package'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:3'],
            'billing_period' => ['required', 'in:monthly,yearly'],
            'access_news' => ['boolean'],
            'access_images' => ['boolean'],
            'access_videos' => ['boolean'],
            'access_exclusive' => ['boolean'],
            'access_bangla' => ['boolean'],
            'access_english' => ['boolean'],
            'max_articles_per_day' => ['nullable', 'integer', 'min:1'],
            'ad_free' => ['boolean'],
            'priority_support' => ['boolean'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $package = SubscriptionPackage::findOrFail($id);
        $package->name = $request->name;
        $package->slug = Str::slug($request->name);
        $package->description = $request->description;
        $package->price = $request->price;
        $package->currency = $request->currency;
        $package->billing_period = $request->billing_period;
        $package->access_news = $request->boolean('access_news');
        $package->access_images = $request->boolean('access_images');
        $package->access_videos = $request->boolean('access_videos');
        $package->access_exclusive = $request->boolean('access_exclusive');
        $package->access_bangla = $request->boolean('access_bangla');
        $package->access_english = $request->boolean('access_english');
        $package->max_articles_per_day = $request->max_articles_per_day;
        $package->ad_free = $request->boolean('ad_free');
        $package->priority_support = $request->boolean('priority_support');
        $package->is_active = $request->boolean('is_active');
        $package->sort_order = $request->sort_order ?? 0;
        $package->save();

        toast(__('admin.Updated Successfully!'), 'success')->width('350');

        return redirect()->route('admin.subscription-package.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $package = SubscriptionPackage::findOrFail($id);
            
            // Check if package has active subscriptions
            if ($package->activeSubscriptions()->count() > 0) {
                return response([
                    'status' => 'error', 
                    'message' => __('admin.Cannot delete package with active subscriptions!')
                ]);
            }

            $package->delete();
            return response(['status' => 'success', 'message' => __('admin.Deleted Successfully!')]);
        } catch (\Throwable $th) {
            return response(['status' => 'error', 'message' => __('admin.something went wrong!')]);
        }
    }
}

