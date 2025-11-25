<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AddSubscriptionPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:add-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add subscription package permissions to the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Clear permission cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'subscription package index',
            'subscription package create',
            'subscription package update',
            'subscription package delete',
        ];

        $this->info('Creating subscription package permissions...');

        foreach ($permissions as $permission) {
            $perm = Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'admin',
            ]);

            if ($perm->wasRecentlyCreated) {
                $this->line("✓ Created permission: {$permission}");
            } else {
                $this->line("→ Permission already exists: {$permission}");
            }
        }

        // Assign all permissions to Super Admin role
        $superAdminRole = Role::where('name', 'Super Admin')
            ->where('guard_name', 'admin')
            ->first();

        if ($superAdminRole) {
            $superAdminRole->syncPermissions(Permission::where('guard_name', 'admin')->get());
            $this->info('✓ All permissions assigned to Super Admin role');
        } else {
            $this->warn('⚠ Super Admin role not found. Please assign permissions manually.');
        }

        $this->info('Done! Subscription package permissions have been added.');
        
        return Command::SUCCESS;
    }
}

