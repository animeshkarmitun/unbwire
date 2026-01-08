<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\WatermarkSetting;
use App\Traits\FileUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SettingController extends Controller
{
    use FileUploadTrait;

    public function __construct()
    {
        $this->middleware(['permission:setting index,admin'])->only(['index']);
        $this->middleware(['permission:setting update,admin'])->only(['updateGeneralSetting', 'updateSeoSetting', 'updateAppearanceSetting', 'updateMicrosoftApiSetting', 'updateWatermarkSetting', 'updateEmailSetting', 'testEmailSetting']);
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

    /**
     * Update email settings
     */
    public function updateEmailSetting(Request $request)
    {
        $request->validate([
            'mail_mailer' => ['required', 'string', 'in:smtp,sendmail,mailgun,ses,postmark,log'],
            'mail_host' => ['required_if:mail_mailer,smtp', 'nullable', 'string', 'max:255'],
            'mail_port' => ['required_if:mail_mailer,smtp', 'nullable', 'integer', 'min:1', 'max:65535'],
            'mail_encryption' => ['nullable', 'string', 'in:tls,ssl,'],
            'mail_username' => ['required_if:mail_mailer,smtp', 'nullable', 'string', 'max:255'],
            'mail_password' => ['nullable', 'string', 'max:255'],
            'mail_from_address' => ['required', 'email', 'max:255'],
            'mail_from_name' => ['required', 'string', 'max:255'],
        ]);

        $settings = [
            'mail_mailer' => $request->mail_mailer,
            'mail_from_address' => $request->mail_from_address,
            'mail_from_name' => $request->mail_from_name,
        ];

        // Only save SMTP settings if SMTP is selected
        if ($request->mail_mailer === 'smtp') {
            $settings['mail_host'] = $request->mail_host;
            $settings['mail_port'] = $request->mail_port;
            $settings['mail_encryption'] = $request->mail_encryption ?? '';
            $settings['mail_username'] = $request->mail_username;
            
            // Only update password if provided (don't overwrite with empty)
            if ($request->filled('mail_password')) {
                $settings['mail_password'] = $request->mail_password;
            }
        }

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        toast(__('admin.Updated Successfully'), 'success')->width('350');

        return redirect()->route('admin.setting.index');
    }

    /**
     * Test email configuration
     */
    public function testEmailSetting(Request $request)
    {
        $request->validate([
            'mail_mailer' => ['required', 'string', 'in:smtp,sendmail,mailgun,ses,postmark,log'],
            'mail_host' => ['required_if:mail_mailer,smtp', 'nullable', 'string', 'max:255'],
            'mail_port' => ['required_if:mail_mailer,smtp', 'nullable', 'integer', 'min:1', 'max:65535'],
            'mail_encryption' => ['nullable', 'string', 'in:tls,ssl,'],
            'mail_username' => ['required_if:mail_mailer,smtp', 'nullable', 'string', 'max:255'],
            'mail_password' => ['nullable', 'string', 'max:255'],
            'mail_from_address' => ['required', 'email', 'max:255'],
            'mail_from_name' => ['required', 'string', 'max:255'],
        ]);

        try {
            // Temporarily configure mail with test settings
            config([
                'mail.default' => $request->mail_mailer,
                'mail.mailers.smtp.host' => $request->mail_host ?? config('mail.mailers.smtp.host'),
                'mail.mailers.smtp.port' => $request->mail_port ?? config('mail.mailers.smtp.port'),
                'mail.mailers.smtp.encryption' => $request->mail_encryption ?? config('mail.mailers.smtp.encryption'),
                'mail.mailers.smtp.username' => $request->mail_username ?? config('mail.mailers.smtp.username'),
                'mail.mailers.smtp.password' => $request->mail_password ?? config('mail.mailers.smtp.password'),
                'mail.from.address' => $request->mail_from_address,
                'mail.from.name' => $request->mail_from_name,
            ]);

            // Get admin email for testing
            $adminEmail = auth('admin')->user()->email ?? config('mail.from.address');

            if (!$adminEmail) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Admin email not found. Please ensure you are logged in.'
                ], 400);
            }

            // Send test email
            Mail::raw('This is a test email from UNB News. Your email configuration is working correctly!', function ($message) use ($request, $adminEmail) {
                $message->to($adminEmail)
                        ->subject('Test Email - UNB News Email Configuration');
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Test email sent successfully to ' . $adminEmail . '. Please check your inbox (and spam folder).'
            ]);
        } catch (\Exception $e) {
            // Handle different types of email errors
            $errorMessage = 'Failed to send test email. ';
            
            $errorClass = get_class($e);
            $errorMsg = $e->getMessage();
            
            // Check for common SMTP errors
            if (str_contains($errorMsg, 'Connection could not be established') || 
                str_contains($errorMsg, 'Connection timed out')) {
                $errorMessage = 'Cannot connect to SMTP server. Please check: ';
                $errorMessage .= 'SMTP Host: ' . ($request->mail_host ?? 'Not set') . ', ';
                $errorMessage .= 'SMTP Port: ' . ($request->mail_port ?? 'Not set') . ', ';
                $errorMessage .= 'Encryption: ' . ($request->mail_encryption ?? 'Not set') . '. ';
                $errorMessage .= 'Also check firewall/network settings.';
            } elseif (str_contains($errorMsg, 'Authentication failed') || 
                      str_contains($errorMsg, 'Invalid login') ||
                      str_contains($errorMsg, 'Username and Password not accepted')) {
                $errorMessage = 'Authentication failed. Please check: ';
                $errorMessage .= 'SMTP Username (email): ' . ($request->mail_username ?? 'Not set') . ', ';
                $errorMessage .= 'SMTP Password (use App Password for Gmail).';
            } elseif (str_contains($errorMsg, 'Could not authenticate')) {
                $errorMessage = 'Could not authenticate with SMTP server. Please verify your credentials.';
            } else {
                $errorMessage .= $errorMsg;
            }
            
            Log::error('Email test failed', [
                'error' => $errorMsg,
                'class' => $errorClass,
                'mailer' => $request->mail_mailer,
                'host' => $request->mail_host,
                'port' => $request->mail_port,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => $errorMessage
            ], 500);
        }
    }
}

