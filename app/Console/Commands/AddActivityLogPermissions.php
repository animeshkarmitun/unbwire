<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AddActivityLogPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity-log:add-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add activity log permissions to the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Clear permission cache
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'activity log index',
            'activity log view',
            'activity log restore',
            'activity log export',
        ];

        $this->info('Creating activity log permissions...');

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

        $this->info('Done! Activity log permissions have been added.');
        
        return Command::SUCCESS;
    }
}
