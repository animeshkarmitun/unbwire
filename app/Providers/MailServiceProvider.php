<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use App\Models\Setting;

class MailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Use a closure to defer loading until after database is ready
        $this->app->booted(function () {
            try {
                // Only configure if settings table exists (to avoid errors during migrations)
                if (!\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                    return;
                }

                $settings = Setting::pluck('value', 'key')->toArray();

                // Configure mailer type
                if (isset($settings['mail_mailer'])) {
                    Config::set('mail.default', $settings['mail_mailer']);
                }

                // Configure SMTP settings if SMTP is selected
                if (($settings['mail_mailer'] ?? 'smtp') === 'smtp') {
                    if (isset($settings['mail_host'])) {
                        Config::set('mail.mailers.smtp.host', $settings['mail_host']);
                    }
                    if (isset($settings['mail_port'])) {
                        Config::set('mail.mailers.smtp.port', $settings['mail_port']);
                    }
                    if (isset($settings['mail_encryption'])) {
                        Config::set('mail.mailers.smtp.encryption', $settings['mail_encryption']);
                    }
                    if (isset($settings['mail_username'])) {
                        Config::set('mail.mailers.smtp.username', $settings['mail_username']);
                    }
                    if (isset($settings['mail_password'])) {
                        Config::set('mail.mailers.smtp.password', $settings['mail_password']);
                    }
                }

                // Configure from address
                if (isset($settings['mail_from_address'])) {
                    Config::set('mail.from.address', $settings['mail_from_address']);
                }
                if (isset($settings['mail_from_name'])) {
                    Config::set('mail.from.name', $settings['mail_from_name']);
                }
            } catch (\Exception $e) {
                // Silently fail if settings table doesn't exist or there's an error
                // This prevents errors during migrations or when settings are not yet configured
            }
        });
    }
}
