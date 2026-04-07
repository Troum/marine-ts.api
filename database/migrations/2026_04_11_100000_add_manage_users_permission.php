<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        $p = Permission::firstOrCreate(
            ['name' => 'manage users', 'guard_name' => 'web'],
        );

        // Не использовать findByName: до первого seed роли может не быть — иначе исключение.
        $admin = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
        );

        if (! $admin->hasPermissionTo($p)) {
            $admin->givePermissionTo($p);
        }
    }

    public function down(): void
    {
        Permission::query()->where('name', 'manage users')->where('guard_name', 'web')->delete();
    }
};
