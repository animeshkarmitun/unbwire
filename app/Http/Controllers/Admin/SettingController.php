<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\WatermarkSetting;
use App\Traits\FileUploadTrait;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    use FileUploadTrait;

    public function __construct()
    {
        $this->middleware(['permission:setting index,admin'])->only(['index']);
        $this->middleware(['permission:setting update,admin'])->only(['updateGeneralSetting', 'updateSeoSetting', 'updateAppearanceSetting', 'updateMicrosoftApiSetting', 'updateWatermarkSetting']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $watermarkSetting = WatermarkSetting::first();
        
        // Create default watermark setting if it doesn't exist
        if (!$watermarkSetting) {
            $watermarkSetting = WatermarkSetting::create([
                'enabled' => false,
                'watermark_size' => 20,
                'opacity' => 100,
                'offset' => 10,
                'position' => 'center',
            ]);
        }
        
        return view('admin.setting.index', compact('settings', 'watermarkSetting'));
    }

    /**
     * Update general settings
     */
    public function updateGeneralSetting(Request $request)
    {
        $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'site_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'site_favicon' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,ico', 'max:1024'],
        ]);

        // Get current settings
        $currentLogo = Setting::where('key', 'site_logo')->first()?->value;
        $currentFavicon = Setting::where('key', 'site_favicon')->first()?->value;

        // Handle logo upload
        if ($request->hasFile('site_logo')) {
            $logoPath = $this->handleFileUpload($request, 'site_logo', $currentLogo);
            Setting::updateOrCreate(
                ['key' => 'site_logo'],
                ['value' => $logoPath]
            );
        }

        // Handle favicon upload
        if ($request->hasFile('site_favicon')) {
            $faviconPath = $this->handleFileUpload($request, 'site_favicon', $currentFavicon);
            Setting::updateOrCreate(
                ['key' => 'site_favicon'],
                ['value' => $faviconPath]
            );
        }

        // Update site name
        Setting::updateOrCreate(
            ['key' => 'site_name'],
            ['value' => $request->site_name]
        );

        toast(__('admin.Updated Successfully'), 'success')->width('350');

        return redirect()->route('admin.setting.index');
    }

    /**
     * Update SEO settings
     */
    public function updateSeoSetting(Request $request)
    {
        $request->validate([
            'site_seo_title' => ['required', 'string', 'max:255'],
            'site_seo_description' => ['required', 'string', 'max:500'],
            'site_seo_keywords' => ['required', 'string', 'max:255'],
        ]);

        Setting::updateOrCreate(
            ['key' => 'site_seo_title'],
            ['value' => $request->site_seo_title]
        );

        Setting::updateOrCreate(
            ['key' => 'site_seo_description'],
            ['value' => $request->site_seo_description]
        );

        Setting::updateOrCreate(
            ['key' => 'site_seo_keywords'],
            ['value' => $request->site_seo_keywords]
        );

        toast(__('admin.Updated Successfully'), 'success')->width('350');

        return redirect()->route('admin.setting.index');
    }

    /**
     * Update appearance settings
     */
    public function updateAppearanceSetting(Request $request)
    {
        $request->validate([
            'site_color' => ['required', 'string', 'regex:/^#[a-fA-F0-9]{6}$/'],
        ]);

        Setting::updateOrCreate(
            ['key' => 'site_color'],
            ['value' => $request->site_color]
        );

        toast(__('admin.Updated Successfully'), 'success')->width('350');

        return redirect()->route('admin.setting.index');
    }

    /**
     * Update Microsoft API settings
     */
    public function updateMicrosoftApiSetting(Request $request)
    {
        $request->validate([
            'site_microsoft_api_host' => ['required', 'string', 'url', 'max:255'],
            'site_microsoft_api_key' => ['required', 'string', 'max:255'],
        ]);

        Setting::updateOrCreate(
            ['key' => 'site_microsoft_api_host'],
            ['value' => $request->site_microsoft_api_host]
        );

        Setting::updateOrCreate(
            ['key' => 'site_microsoft_api_key'],
            ['value' => $request->site_microsoft_api_key]
        );

        toast(__('admin.Updated Successfully'), 'success')->width('350');

        return redirect()->route('admin.setting.index');
    }

    /**
     * Update watermark settings
     */
    public function updateWatermarkSetting(Request $request)
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

        toast(__('admin.Updated Successfully'), 'success')->width('350');

        return redirect()->route('admin.setting.index');
    }
}

