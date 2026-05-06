<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('site_seo_pages') || ! Schema::hasTable('site_seo_page_translations')) {
            return;
        }

        if (DB::table('site_seo_pages')->where('slug', 'lnk')->exists()) {
            return;
        }

        $now = now();
        $locale = (string) config('marine.default_locale', 'ru');

        $pageId = DB::table('site_seo_pages')->insertGetId([
            'slug' => 'lnk',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $translation = [
            'site_seo_page_id' => $pageId,
            'locale' => $locale,
            'label' => 'ЛНК — лаборатория неразрушающего контроля',
            'seo_title' => 'Лаборатория неразрушающего контроля (ЛНК) — Marine Technical Solutions',
            'seo_description' => 'Ультразвуковая толщинометрия, диагностика судовых систем и подготовка к освидетельствованиям класса для морского флота.',
            'seo_keywords' => 'ЛНК, неразрушающий контроль, UTM, дефектоскопия, Marine Technical Solutions',
            'created_at' => $now,
            'updated_at' => $now,
        ];

        if (Schema::hasColumn('site_seo_page_translations', 'seo_image')) {
            $translation['seo_image'] = null;
        }

        DB::table('site_seo_page_translations')->insert($translation);
    }

    public function down(): void
    {
        if (! Schema::hasTable('site_seo_pages')) {
            return;
        }

        DB::table('site_seo_pages')->where('slug', 'lnk')->delete();
    }
};
