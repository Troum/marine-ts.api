<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $manageNews = Permission::firstOrCreate(['name' => 'manage news']);
        $manageProjects = Permission::firstOrCreate(['name' => 'manage projects']);
        $manageServices = Permission::firstOrCreate(['name' => 'manage services']);
        $manageSeo = Permission::firstOrCreate(['name' => 'manage seo']);
        $manageVacancies = Permission::firstOrCreate(['name' => 'manage vacancies']);
        $manageFeedback = Permission::firstOrCreate(['name' => 'manage feedback']);
        $manageUsers = Permission::firstOrCreate(['name' => 'manage users']);
        $manageContentPages = Permission::firstOrCreate(['name' => 'manage content pages']);
        $manageGallery = Permission::firstOrCreate(['name' => 'manage gallery']);

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions([$manageNews, $manageProjects, $manageServices, $manageSeo, $manageVacancies, $manageFeedback, $manageUsers, $manageContentPages, $manageGallery]);

        $contentManager = Role::firstOrCreate(['name' => 'content_manager']);
        $contentManager->syncPermissions([$manageNews, $manageProjects, $manageServices, $manageSeo, $manageVacancies, $manageFeedback, $manageContentPages, $manageGallery]);

        $hrManager = Role::firstOrCreate(['name' => 'hr_manager']);
        $hrManager->syncPermissions([$manageVacancies]);
    }
}
