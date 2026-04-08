<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const LOCALE = 'ru';

    public function up(): void
    {
        $this->createNewsTranslations();
        $this->createVacancyTranslations();
        $this->createProjectTranslations();
        $this->createServiceTranslations();
        $this->createContentPageTranslations();
        $this->createSiteSeoPageTranslations();
        $this->createGalleryItemTranslations();

        $this->migrateNews();
        $this->migrateVacancies();
        $this->migrateProjects();
        $this->migrateServices();
        $this->migrateContentPages();
        $this->migrateSiteSeoPages();
        $this->migrateGalleryItems();

        $this->dropOldNewsColumns();
        $this->dropOldVacancyColumns();
        $this->dropOldProjectColumns();
        $this->dropOldServiceColumns();
        $this->dropOldContentPageColumns();
        $this->dropOldSiteSeoPageColumns();
        $this->dropOldGalleryItemColumns();
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table): void {
            $table->string('title')->default('');
            $table->text('excerpt')->default('');
            $table->longText('content')->nullable();
            $table->string('category')->default('');
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_keywords')->nullable();
        });

        Schema::table('vacancies', function (Blueprint $table): void {
            $table->string('title')->default('');
            $table->text('excerpt')->default('');
            $table->longText('content')->nullable();
            $table->json('requirements')->nullable();
            $table->string('location')->nullable();
            $table->string('employment_type')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_keywords')->nullable();
        });

        Schema::table('projects', function (Blueprint $table): void {
            $table->string('title')->default('');
            $table->string('type_label')->default('');
            $table->string('location')->default('');
            $table->text('description')->default('');
            $table->json('stats')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_keywords')->nullable();
        });

        Schema::table('services', function (Blueprint $table): void {
            $table->string('title')->default('');
            $table->text('description')->default('');
            $table->json('features')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_keywords')->nullable();
        });

        Schema::table('content_pages', function (Blueprint $table): void {
            $table->string('title')->default('');
            $table->text('excerpt')->nullable();
            $table->longText('body')->default('');
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_keywords')->nullable();
        });

        Schema::table('site_seo_pages', function (Blueprint $table): void {
            $table->string('label')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_keywords')->nullable();
        });

        Schema::table('gallery_items', function (Blueprint $table): void {
            $table->string('alt')->nullable();
        });

        $this->restoreFromTranslations();

        Schema::dropIfExists('gallery_item_translations');
        Schema::dropIfExists('site_seo_page_translations');
        Schema::dropIfExists('content_page_translations');
        Schema::dropIfExists('service_translations');
        Schema::dropIfExists('project_translations');
        Schema::dropIfExists('vacancy_translations');
        Schema::dropIfExists('news_translations');
    }

    private function createNewsTranslations(): void
    {
        Schema::create('news_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('news_id')->constrained('news')->cascadeOnDelete();
            $table->string('locale', 8);
            $table->string('title');
            $table->text('excerpt');
            $table->longText('content')->nullable();
            $table->string('category');
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_keywords')->nullable();
            $table->timestamps();
            $table->unique(['news_id', 'locale']);
        });
    }

    private function createVacancyTranslations(): void
    {
        Schema::create('vacancy_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('vacancy_id')->constrained('vacancies')->cascadeOnDelete();
            $table->string('locale', 8);
            $table->string('title');
            $table->text('excerpt');
            $table->longText('content')->nullable();
            $table->json('requirements')->nullable();
            $table->string('location')->nullable();
            $table->string('employment_type')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_keywords')->nullable();
            $table->timestamps();
            $table->unique(['vacancy_id', 'locale']);
        });
    }

    private function createProjectTranslations(): void
    {
        Schema::create('project_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('locale', 8);
            $table->string('title');
            $table->string('type_label');
            $table->string('location');
            $table->text('description');
            $table->json('stats');
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_keywords')->nullable();
            $table->timestamps();
            $table->unique(['project_id', 'locale']);
        });
    }

    private function createServiceTranslations(): void
    {
        Schema::create('service_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->string('locale', 8);
            $table->string('title');
            $table->text('description');
            $table->json('features');
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_keywords')->nullable();
            $table->timestamps();
            $table->unique(['service_id', 'locale']);
        });
    }

    private function createContentPageTranslations(): void
    {
        Schema::create('content_page_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('content_page_id')->constrained('content_pages')->cascadeOnDelete();
            $table->string('locale', 8);
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->longText('body');
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_keywords')->nullable();
            $table->timestamps();
            $table->unique(['content_page_id', 'locale']);
        });
    }

    private function createSiteSeoPageTranslations(): void
    {
        Schema::create('site_seo_page_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('site_seo_page_id')->constrained('site_seo_pages')->cascadeOnDelete();
            $table->string('locale', 8);
            $table->string('label')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('seo_keywords')->nullable();
            $table->timestamps();
            $table->unique(['site_seo_page_id', 'locale']);
        });
    }

    private function createGalleryItemTranslations(): void
    {
        Schema::create('gallery_item_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('gallery_item_id')->constrained('gallery_items')->cascadeOnDelete();
            $table->string('locale', 8);
            $table->string('alt')->default('');
            $table->timestamps();
            $table->unique(['gallery_item_id', 'locale']);
        });
    }

    private function migrateNews(): void
    {
        $rows = DB::table('news')->select(
            'id',
            'title',
            'excerpt',
            'content',
            'category',
            'seo_title',
            'seo_description',
            'seo_keywords',
            'created_at',
            'updated_at'
        )->get();

        foreach ($rows as $row) {
            DB::table('news_translations')->insert([
                'news_id' => $row->id,
                'locale' => self::LOCALE,
                'title' => $row->title,
                'excerpt' => $row->excerpt,
                'content' => $row->content,
                'category' => $row->category,
                'seo_title' => $row->seo_title,
                'seo_description' => $row->seo_description,
                'seo_keywords' => $row->seo_keywords,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);
        }
    }

    private function migrateVacancies(): void
    {
        $rows = DB::table('vacancies')->select(
            'id',
            'title',
            'excerpt',
            'content',
            'requirements',
            'location',
            'employment_type',
            'seo_title',
            'seo_description',
            'seo_keywords',
            'created_at',
            'updated_at'
        )->get();

        foreach ($rows as $row) {
            DB::table('vacancy_translations')->insert([
                'vacancy_id' => $row->id,
                'locale' => self::LOCALE,
                'title' => $row->title,
                'excerpt' => $row->excerpt,
                'content' => $row->content,
                'requirements' => $row->requirements,
                'location' => $row->location,
                'employment_type' => $row->employment_type,
                'seo_title' => $row->seo_title,
                'seo_description' => $row->seo_description,
                'seo_keywords' => $row->seo_keywords,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);
        }
    }

    private function migrateProjects(): void
    {
        $rows = DB::table('projects')->select(
            'id',
            'title',
            'type_label',
            'location',
            'description',
            'stats',
            'seo_title',
            'seo_description',
            'seo_keywords',
            'created_at',
            'updated_at'
        )->get();

        foreach ($rows as $row) {
            DB::table('project_translations')->insert([
                'project_id' => $row->id,
                'locale' => self::LOCALE,
                'title' => $row->title,
                'type_label' => $row->type_label,
                'location' => $row->location,
                'description' => $row->description,
                'stats' => $row->stats,
                'seo_title' => $row->seo_title,
                'seo_description' => $row->seo_description,
                'seo_keywords' => $row->seo_keywords,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);
        }
    }

    private function migrateServices(): void
    {
        $rows = DB::table('services')->select(
            'id',
            'title',
            'description',
            'features',
            'seo_title',
            'seo_description',
            'seo_keywords',
            'created_at',
            'updated_at'
        )->get();

        foreach ($rows as $row) {
            DB::table('service_translations')->insert([
                'service_id' => $row->id,
                'locale' => self::LOCALE,
                'title' => $row->title,
                'description' => $row->description,
                'features' => $row->features,
                'seo_title' => $row->seo_title,
                'seo_description' => $row->seo_description,
                'seo_keywords' => $row->seo_keywords,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);
        }
    }

    private function migrateContentPages(): void
    {
        $rows = DB::table('content_pages')->select(
            'id',
            'title',
            'excerpt',
            'body',
            'seo_title',
            'seo_description',
            'seo_keywords',
            'created_at',
            'updated_at'
        )->get();

        foreach ($rows as $row) {
            DB::table('content_page_translations')->insert([
                'content_page_id' => $row->id,
                'locale' => self::LOCALE,
                'title' => $row->title,
                'excerpt' => $row->excerpt,
                'body' => $row->body,
                'seo_title' => $row->seo_title,
                'seo_description' => $row->seo_description,
                'seo_keywords' => $row->seo_keywords,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);
        }
    }

    private function migrateSiteSeoPages(): void
    {
        $rows = DB::table('site_seo_pages')->select(
            'id',
            'label',
            'seo_title',
            'seo_description',
            'seo_keywords',
            'created_at',
            'updated_at'
        )->get();

        foreach ($rows as $row) {
            DB::table('site_seo_page_translations')->insert([
                'site_seo_page_id' => $row->id,
                'locale' => self::LOCALE,
                'label' => $row->label,
                'seo_title' => $row->seo_title,
                'seo_description' => $row->seo_description,
                'seo_keywords' => $row->seo_keywords,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);
        }
    }

    private function migrateGalleryItems(): void
    {
        $rows = DB::table('gallery_items')->select(
            'id',
            'alt',
            'created_at',
            'updated_at'
        )->get();

        foreach ($rows as $row) {
            DB::table('gallery_item_translations')->insert([
                'gallery_item_id' => $row->id,
                'locale' => self::LOCALE,
                'alt' => $row->alt ?? '',
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);
        }
    }

    private function dropOldNewsColumns(): void
    {
        Schema::table('news', function (Blueprint $table): void {
            $table->dropColumn([
                'title',
                'excerpt',
                'content',
                'category',
                'seo_title',
                'seo_description',
                'seo_keywords',
            ]);
        });
    }

    private function dropOldVacancyColumns(): void
    {
        Schema::table('vacancies', function (Blueprint $table): void {
            $table->dropColumn([
                'title',
                'excerpt',
                'content',
                'requirements',
                'location',
                'employment_type',
                'seo_title',
                'seo_description',
                'seo_keywords',
            ]);
        });
    }

    private function dropOldProjectColumns(): void
    {
        Schema::table('projects', function (Blueprint $table): void {
            $table->dropColumn([
                'title',
                'type_label',
                'location',
                'description',
                'stats',
                'seo_title',
                'seo_description',
                'seo_keywords',
            ]);
        });
    }

    private function dropOldServiceColumns(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            $table->dropColumn([
                'title',
                'description',
                'features',
                'seo_title',
                'seo_description',
                'seo_keywords',
            ]);
        });
    }

    private function dropOldContentPageColumns(): void
    {
        Schema::table('content_pages', function (Blueprint $table): void {
            $table->dropColumn([
                'title',
                'excerpt',
                'body',
                'seo_title',
                'seo_description',
                'seo_keywords',
            ]);
        });
    }

    private function dropOldSiteSeoPageColumns(): void
    {
        Schema::table('site_seo_pages', function (Blueprint $table): void {
            $table->dropColumn([
                'label',
                'seo_title',
                'seo_description',
                'seo_keywords',
            ]);
        });
    }

    private function dropOldGalleryItemColumns(): void
    {
        Schema::table('gallery_items', function (Blueprint $table): void {
            $table->dropColumn(['alt']);
        });
    }

    private function restoreFromTranslations(): void
    {
        foreach (DB::table('news_translations')->where('locale', self::LOCALE)->get() as $t) {
            DB::table('news')->where('id', $t->news_id)->update([
                'title' => $t->title,
                'excerpt' => $t->excerpt,
                'content' => $t->content,
                'category' => $t->category,
                'seo_title' => $t->seo_title,
                'seo_description' => $t->seo_description,
                'seo_keywords' => $t->seo_keywords,
            ]);
        }

        foreach (DB::table('vacancy_translations')->where('locale', self::LOCALE)->get() as $t) {
            DB::table('vacancies')->where('id', $t->vacancy_id)->update([
                'title' => $t->title,
                'excerpt' => $t->excerpt,
                'content' => $t->content,
                'requirements' => $t->requirements,
                'location' => $t->location,
                'employment_type' => $t->employment_type,
                'seo_title' => $t->seo_title,
                'seo_description' => $t->seo_description,
                'seo_keywords' => $t->seo_keywords,
            ]);
        }

        foreach (DB::table('project_translations')->where('locale', self::LOCALE)->get() as $t) {
            DB::table('projects')->where('id', $t->project_id)->update([
                'title' => $t->title,
                'type_label' => $t->type_label,
                'location' => $t->location,
                'description' => $t->description,
                'stats' => $t->stats,
                'seo_title' => $t->seo_title,
                'seo_description' => $t->seo_description,
                'seo_keywords' => $t->seo_keywords,
            ]);
        }

        foreach (DB::table('service_translations')->where('locale', self::LOCALE)->get() as $t) {
            DB::table('services')->where('id', $t->service_id)->update([
                'title' => $t->title,
                'description' => $t->description,
                'features' => $t->features,
                'seo_title' => $t->seo_title,
                'seo_description' => $t->seo_description,
                'seo_keywords' => $t->seo_keywords,
            ]);
        }

        foreach (DB::table('content_page_translations')->where('locale', self::LOCALE)->get() as $t) {
            DB::table('content_pages')->where('id', $t->content_page_id)->update([
                'title' => $t->title,
                'excerpt' => $t->excerpt,
                'body' => $t->body,
                'seo_title' => $t->seo_title,
                'seo_description' => $t->seo_description,
                'seo_keywords' => $t->seo_keywords,
            ]);
        }

        foreach (DB::table('site_seo_page_translations')->where('locale', self::LOCALE)->get() as $t) {
            DB::table('site_seo_pages')->where('id', $t->site_seo_page_id)->update([
                'label' => $t->label,
                'seo_title' => $t->seo_title,
                'seo_description' => $t->seo_description,
                'seo_keywords' => $t->seo_keywords,
            ]);
        }

        foreach (DB::table('gallery_item_translations')->where('locale', self::LOCALE)->get() as $t) {
            DB::table('gallery_items')->where('id', $t->gallery_item_id)->update([
                'alt' => $t->alt,
            ]);
        }
    }
};
