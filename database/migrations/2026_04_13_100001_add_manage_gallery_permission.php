<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $p = Permission::firstOrCreate(
            ['name' => 'manage gallery', 'guard_name' => 'web'],
        );

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        if (! $admin->hasPermissionTo($p)) {
            $admin->givePermissionTo($p);
        }

        $contentManager = Role::firstOrCreate(['name' => 'content_manager', 'guard_name' => 'web']);
        if (! $contentManager->hasPermissionTo($p)) {
            $contentManager->givePermissionTo($p);
        }
    }

    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::query()->where('name', 'manage gallery')->where('guard_name', 'web')->delete();
    }
};
