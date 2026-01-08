<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SubscriberNotificationSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:setting index,admin'])->only(['index']);
        $this->middleware(['permission:setting update,admin'])->only(['update']);
    }

    /**
     * Display notification settings page
     */
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        
        // Default values
        $defaults = [
            'notification_email_enabled' => '1',
            'notification_send_to_all' => '1',
            'notification_email_rate_limit' => '100',
            'notification_batch_size' => '100',
            // Email template settings
            'email_template_send_full_content' => '1',
            'email_template_include_title' => '1',
            'email_template_include_image' => '1',
            'email_template_include_content' => '1',
            'email_template_include_author' => '1',
            'email_template_include_category' => '1',
            'email_template_include_publish_date' => '1',
            'email_template_include_video_link' => '1',
            'email_template_include_tags' => '0',
            'email_template_include_excerpt' => '1',
        ];

        foreach ($defaults as $key => $value) {
            if (!isset($settings[$key])) {
                $settings[$key] = $value;
            }
        }

        return view('admin.subscriber-notification-settings.index', compact('settings'));
    }

    /**
     * Update notification settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'notification_email_enabled' => 'nullable|in:0,1',
            'notification_send_to_all' => 'nullable|in:0,1',
            'notification_email_rate_limit' => 'nullable|integer|min:1|max:1000',
            'notification_batch_size' => 'nullable|integer|min:10|max:500',
            // Email template settings
            'email_template_send_full_content' => 'nullable|in:0,1',
            'email_template_include_title' => 'nullable|in:0,1',
            'email_template_include_image' => 'nullable|in:0,1',
            'email_template_include_content' => 'nullable|in:0,1',
            'email_template_include_author' => 'nullable|in:0,1',
            'email_template_include_category' => 'nullable|in:0,1',
            'email_template_include_publish_date' => 'nullable|in:0,1',
            'email_template_include_video_link' => 'nullable|in:0,1',
            'email_template_include_tags' => 'nullable|in:0,1',
            'email_template_include_excerpt' => 'nullable|in:0,1',
        ]);

        $settings = [
            'notification_email_enabled' => $request->notification_email_enabled ?? '1',
            'notification_send_to_all' => $request->notification_send_to_all ?? '1',
            'notification_email_rate_limit' => $request->notification_email_rate_limit ?? '100',
            'notification_batch_size' => $request->notification_batch_size ?? '100',
            // Email template settings
            'email_template_send_full_content' => $request->email_template_send_full_content ?? '1',
            'email_template_include_title' => $request->email_template_include_title ?? '1',
            'email_template_include_image' => $request->email_template_include_image ?? '1',
            'email_template_include_content' => $request->email_template_include_content ?? '1',
            'email_template_include_author' => $request->email_template_include_author ?? '1',
            'email_template_include_category' => $request->email_template_include_category ?? '1',
            'email_template_include_publish_date' => $request->email_template_include_publish_date ?? '1',
            'email_template_include_video_link' => $request->email_template_include_video_link ?? '1',
            'email_template_include_tags' => $request->email_template_include_tags ?? '0',
            'email_template_include_excerpt' => $request->email_template_include_excerpt ?? '1',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        toast(__('admin.Updated Successfully!'), 'success')->width('330');
        return redirect()->back();
    }
}
