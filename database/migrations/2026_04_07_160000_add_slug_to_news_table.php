<?php

use App\Models\News;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('id');
        });

        foreach (News::query()->orderBy('id')->get() as $news) {
            if ($news->slug !== null && $news->slug !== '') {
                continue;
            }
            $news->slug = News::ensureUniqueSlug(News::slugFromTitle($news->title));
            $news->saveQuietly();
        }
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
