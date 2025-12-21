<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UpdateAllPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:update-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all permissions based on routes and controllers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Clear permission cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->info('Updating all permissions...');

        // Define all permissions grouped by module
        $permissions = [
            // Dashboard
            'dashboard index' => 'Dashboard',

            // News
            'news index' => 'News',
            'news create' => 'News',
            'news update' => 'News',
            'news delete' => 'News',
            'news all-access' => 'News',

            // Category
            'category index' => 'Category',
            'category create' => 'Category',
            'category update' => 'Category',
            'category delete' => 'Category',

            // Pages
            'about index' => 'Pages',
            'about update' => 'Pages',
            'contact index' => 'Pages',
            'contact update' => 'Pages',

            // Social Count
            'social count index' => 'Social Count',
            'social count create' => 'Social Count',
            'social count update' => 'Social Count',
            'social count delete' => 'Social Count',

            // Contact Messages
            'contact message index' => 'Contact Messages',
            'contact message update' => 'Contact Messages',

            // Support Tickets
            'support tickets index' => 'Support Tickets',
            'support tickets view' => 'Support Tickets',
            'support tickets create' => 'Support Tickets',
            'support tickets update' => 'Support Tickets',
            'support tickets assign' => 'Support Tickets',
            'support tickets delete' => 'Support Tickets',

            // Home Section Setting
            'home section index' => 'Home Section Setting',
            'home section update' => 'Home Section Setting',

            // Advertisement
            'advertisement index' => 'Advertisement',
            'advertisement update' => 'Advertisement',

            // Media Library
            'media library index' => 'Media Library',
            'media library create' => 'Media Library',
            'media library update' => 'Media Library',
            'media library delete' => 'Media Library',

            // Gallery
            'image gallery index' => 'Gallery',
            'image gallery create' => 'Gallery',
            'image gallery update' => 'Gallery',
            'image gallery delete' => 'Gallery',
            'video gallery index' => 'Gallery',
            'video gallery create' => 'Gallery',
            'video gallery update' => 'Gallery',
            'video gallery delete' => 'Gallery',

            // Subscription Package
            'subscription package index' => 'Subscription',
            'subscription package create' => 'Subscription',
            'subscription package update' => 'Subscription',
            'subscription package delete' => 'Subscription',

            // Footer
            'footer index' => 'Footer',
            'footer create' => 'Footer',
            'footer update' => 'Footer',
            'footer destroy' => 'Footer',

            // Access Management
            'access management index' => 'Access Management',
            'access management create' => 'Access Management',
            'access management update' => 'Access Management',
            'access management destroy' => 'Access Management',

            // Settings
            'setting index' => 'Settings',
            'setting update' => 'Settings',

            // Languages
            'languages index' => 'Languages',
            'languages create' => 'Languages',
            'languages update' => 'Languages',
            'languages delete' => 'Languages',

            // Localization
            'localization index' => 'Localization',
            'localization update' => 'Localization',

            // Analytics
            'analytics index' => 'Analytics',
            'analytics view' => 'Analytics',
            'analytics export' => 'Analytics',

            // Activity Log
            'activity log index' => 'Activity Log',
            'activity log view' => 'Activity Log',
            'activity log restore' => 'Activity Log',
            'activity log export' => 'Activity Log',

            // Watermark Settings
            'watermark settings index' => 'Watermark Settings',
            'watermark settings update' => 'Watermark Settings',

            // Subscribers (if exists)
            'subscribers index' => 'Subscribers',
            'subscribers delete' => 'Subscribers',
        ];

        $created = 0;
        $existing = 0;

        foreach ($permissions as $permission => $group) {
            $perm = Permission::firstOrCreate(
                [
                    'name' => $permission,
                    'guard_name' => 'admin',
                ]
            );

            // Check if group_name column exists
            $hasGroupName = \Schema::hasColumn('permissions', 'group_name');
            
            if ($hasGroupName) {
                // Update group_name if it's different or null
                if ($perm->group_name !== $group) {
                    $perm->group_name = $group;
                    $perm->save();
                }
            }

            if ($perm->wasRecentlyCreated) {
                $created++;
                $this->line("✓ Created permission: {$permission} ({$group})");
            } else {
                $existing++;
                if ($hasGroupName && $perm->group_name !== $group) {
                    $this->line("→ Updated group for: {$permission} ({$group})");
                } else {
                    $this->line("→ Permission already exists: {$permission}");
                }
            }
        }

        // Assign all permissions to Super Admin role
        $superAdminRole = Role::where('name', 'Super Admin')
            ->where('guard_name', 'admin')
            ->first();

        if ($superAdminRole) {
            $allPermissions = Permission::where('guard_name', 'admin')->get();
            $superAdminRole->syncPermissions($allPermissions);
            $this->info("✓ All {$allPermissions->count()} permissions assigned to Super Admin role");
        } else {
            $this->warn('⚠ Super Admin role not found. Please assign permissions manually.');
        }

        $this->info("\nDone! Created {$created} new permissions, {$existing} already existed.");
        $this->info("Total permissions: " . Permission::where('guard_name', 'admin')->count());
        
        return Command::SUCCESS;
    }
}

