<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:reset-password {email?} {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset admin password. Usage: php artisan admin:reset-password [email] [--password=newpassword]';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?: 'admin@unb.com.bd';
        $password = $this->option('password') ?: 'password';

        $admin = Admin::where('email', $email)->first();

        if (!$admin) {
            // Create admin if doesn't exist
            $admin = Admin::create([
                'email' => $email,
                'name' => 'Super Admin',
                'password' => Hash::make($password),
                'status' => true,
                'image' => 'frontend/assets/images/avatar.png',
            ]);
            $this->info("Admin user created successfully!");
        } else {
            // Update password
            $admin->password = Hash::make($password);
            $admin->status = true; // Ensure status is active
            $admin->save();
            $this->info("Admin password reset successfully!");
        }

        $this->info("Email: {$email}");
        $this->info("Password: {$password}");
        $this->warn("Please change this password after logging in!");

        return Command::SUCCESS;
    }
}
