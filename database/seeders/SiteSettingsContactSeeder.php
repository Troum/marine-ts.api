<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use App\Services\ContactSettingsService;
use Illuminate\Database\Seeder;

class SiteSettingsContactSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(ContactSettingsService::class);
        SiteSetting::query()->firstOrCreate(
            ['key' => ContactSettingsService::KEY],
            ['value' => $service->defaultContacts()],
        );
    }
}
