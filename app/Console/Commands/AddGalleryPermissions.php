<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AddGalleryPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gallery:add-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add gallery permissions (image gallery and video gallery)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'image gallery index',
            'image gallery create',
            'image gallery update',
            'image gallery delete',
            'video gallery index',
            'video gallery create',
            'video gallery update',
            'video gallery delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'admin',
            ]);
        }

        $this->info('Creating gallery permissions...');

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

        // Assign to Super Admin role
        $superAdminRole = Role::where('name', 'Super Admin')
            ->where('guard_name', 'admin')
            ->first();

        if ($superAdminRole) {
            $superAdminRole->syncPermissions(Permission::where('guard_name', 'admin')->get());
            $this->info('✓ All permissions assigned to Super Admin role');
        } else {
            $this->warn('⚠ Super Admin role not found. Please assign permissions manually.');
        }

        $this->info('Done! Gallery permissions have been added.');
        
        return Command::SUCCESS;
    }
}
