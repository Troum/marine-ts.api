<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            ServicesSeeder::class,
            SiteSeoPagesSeeder::class,
            VacanciesSeeder::class,
            MarineDataSeeder::class,
            SiteSettingsContactSeeder::class,
            // Точечно перезаписывает geography.locations на странице "О компании",
            // не трогая остальной контент. Если страница ещё не создана — пропускает.
            AboutPageLocationsSeeder::class,
        ]);
    }
}
