<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @var array<int, string>
     */
    private array $tables = [
        'news_translations',
        'vacancy_translations',
        'project_translations',
        'service_translations',
        'content_page_translations',
        'site_seo_page_translations',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                if (! Schema::hasColumn($tableName, 'seo_image')) {
                    $table->string('seo_image')->nullable()->after('seo_keywords');
                }
            });
        }
    }

    public function down(): void
    {
        foreach (array_reverse($this->tables) as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                if (Schema::hasColumn($tableName, 'seo_image')) {
                    $table->dropColumn('seo_image');
                }
            });
        }
    }
};
