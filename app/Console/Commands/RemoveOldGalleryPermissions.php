<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RemoveOldGalleryPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:remove-old-gallery';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove old gallery permissions (gallery index, create, update, delete)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Clear permission cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $oldPermissions = [
            'gallery index',
            'gallery create',
            'gallery update',
            'gallery delete',
        ];

        $this->info('Removing old gallery permissions...');

        foreach ($oldPermissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)
                ->where('guard_name', 'admin')
                ->first();

            if ($permission) {
                // Find all roles that have this permission
                $roles = Role::whereHas('permissions', function($query) use ($permission) {
                    $query->where('permissions.id', $permission->id);
                })->get();

                // Revoke permission from all roles
                foreach ($roles as $role) {
                    $role->revokePermissionTo($permission);
                    $this->line("  → Revoked '{$permissionName}' from role: {$role->name}");
                }

                // Delete the permission
                $permission->delete();
                $this->info("  ✓ Deleted permission: {$permissionName}");
            } else {
                $this->line("  → Permission '{$permissionName}' not found");
            }
        }

        $this->info("\nDone! Old gallery permissions have been removed.");
        $this->info("Note: Make sure roles have the new 'image gallery' and 'video gallery' permissions instead.");
        
        return Command::SUCCESS;
    }
}


