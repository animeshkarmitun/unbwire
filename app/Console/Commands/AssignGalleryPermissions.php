<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AssignGalleryPermissions extends Command
{
    protected $signature = 'permissions:assign-gallery';
    protected $description = 'Assign gallery permissions to Super Admin role';

    public function handle()
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $galleryPermissions = Permission::where('name', 'like', '%gallery%')
            ->where('guard_name', 'admin')
            ->get();

        if ($galleryPermissions->isEmpty()) {
            $this->warn('No gallery permissions found!');
            return Command::FAILURE;
        }

        $role = Role::where('name', 'Super Admin')
            ->where('guard_name', 'admin')
            ->first();

        if (!$role) {
            $this->warn('Super Admin role not found!');
            return Command::FAILURE;
        }

        $role->givePermissionTo($galleryPermissions);

        $this->info('Assigned gallery permissions to Super Admin:');
        foreach ($galleryPermissions as $perm) {
            $this->line("  ✓ {$perm->name}");
        }

        return Command::SUCCESS;
    }
}


