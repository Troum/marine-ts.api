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

        $contentManager = Role::query()->where('name', 'content_manager')->first();
        if ($contentManager) {
            foreach (['manage vacancies', 'manage feedback', 'manage page inquiries'] as $permissionName) {
                $permission = Permission::query()->where('name', $permissionName)->first();
                if ($permission && $contentManager->hasPermissionTo($permission)) {
                    $contentManager->revokePermissionTo($permission);
                }
            }
        }

        $hrManager = Role::query()->where('name', 'hr_manager')->first();
        if ($hrManager) {
            foreach (['manage news', 'manage projects', 'manage services', 'manage seo', 'manage content pages'] as $permissionName) {
                $permission = Permission::query()->where('name', $permissionName)->first();
                if ($permission && $hrManager->hasPermissionTo($permission)) {
                    $hrManager->revokePermissionTo($permission);
                }
            }
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $contentManager = Role::query()->where('name', 'content_manager')->first();
        if ($contentManager) {
            foreach (['manage vacancies', 'manage feedback', 'manage page inquiries'] as $permissionName) {
                $permission = Permission::query()->where('name', $permissionName)->first();
                if ($permission && ! $contentManager->hasPermissionTo($permission)) {
                    $contentManager->givePermissionTo($permission);
                }
            }
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
