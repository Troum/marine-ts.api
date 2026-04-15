<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const SLUGS = ['ship-repair', 'spare-parts'];

    public function up(): void
    {
        $schema = DB::getSchemaBuilder();

        if ($schema->hasTable('content_pages')) {
            $pageIds = DB::table('content_pages')->whereIn('slug', self::SLUGS)->pluck('id');
            if ($pageIds->isNotEmpty() && $schema->hasTable('content_page_translations')) {
                DB::table('content_page_translations')->whereIn('content_page_id', $pageIds->all())->delete();
            }
            DB::table('content_pages')->whereIn('slug', self::SLUGS)->delete();
        }

        if ($schema->hasTable('site_seo_pages')) {
            $ids = DB::table('site_seo_pages')->whereIn('slug', self::SLUGS)->pluck('id');
            if ($ids->isNotEmpty() && $schema->hasTable('site_seo_page_translations')) {
                DB::table('site_seo_page_translations')->whereIn('site_seo_page_id', $ids->all())->delete();
            }
            DB::table('site_seo_pages')->whereIn('slug', self::SLUGS)->delete();
        }
    }

    public function down(): void
    {
        // Восстановление контента не выполняется — данные удалены намеренно.
    }
};
