<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WatermarkSetting;
use App\Traits\FileUploadTrait;
use Illuminate\Http\Request;

class WatermarkSettingController extends Controller
{
    use FileUploadTrait;

    public function __construct()
    {
        $this->middleware(['permission:watermark settings index,admin'])->only(['index']);
        $this->middleware(['permission:watermark settings update,admin'])->only(['update']);
    }

    /**
     * Display watermark settings
     */
    public function index()
    {
        $setting = WatermarkSetting::first();
        if (!$setting) {
            $setting = WatermarkSetting::create([
                'enabled' => false,
                'watermark_size' => 20,
                'opacity' => 100,
                'offset' => 10,
                'position' => 'center',
            ]);
        }
        return view('admin.watermark-settings.index', compact('setting'));
    }

    /**
     * Update watermark settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'enabled' => ['nullable', 'boolean'],
            'watermark_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'watermark_size' => ['nullable', 'integer', 'min:1', 'max:100'],
            'opacity' => ['nullable', 'integer', 'min:1', 'max:100'],
            'offset' => ['nullable', 'integer', 'min:0'],
            'position' => ['nullable', 'string', 'in:center,top-left,top-center,top-right,middle-left,middle-right,bottom-left,bottom-center,bottom-right'],
        ]);

        $setting = WatermarkSetting::first();
        if (!$setting) {
            $setting = new WatermarkSetting();
        }

        $setting->enabled = $request->boolean('enabled', false);
        $setting->watermark_size = $request->input('watermark_size', 20);
        $setting->opacity = $request->input('opacity', 100);
        $setting->offset = $request->input('offset', 10);
        $setting->position = $request->input('position', 'center');

        // Handle watermark image upload
        if ($request->hasFile('watermark_image')) {
            $oldImage = $setting->watermark_image;
            $imagePath = $this->handleFileUpload($request, 'watermark_image', $oldImage);
            $setting->watermark_image = $imagePath;
        }

        $setting->save();

        return redirect()->route('admin.watermark-settings.index')
            ->with('success', 'Watermark settings updated successfully');
    }
}
