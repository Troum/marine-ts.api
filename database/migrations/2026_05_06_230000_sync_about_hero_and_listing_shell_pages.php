<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Публичный контент страниц — в БД, не в коде фронтенда.
 *
 * - about: hero v2 (label / title / subtitle / body) в духе макета.
 * - news-page, gallery-page: создаются при отсутствии (пустой hero, контент из админки).
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->updateAboutHero();
        $this->ensureListingShellPage('news-page', ['ru' => 'Новости', 'en' => 'News']);
        $this->ensureListingShellPage('gallery-page', ['ru' => 'Галерея', 'en' => 'Gallery']);
    }

    public function down(): void
    {
        // намеренно без отката: данные могли редактироваться в CMS
    }

    private function updateAboutHero(): void
    {
        $pageId = DB::table('content_pages')->where('slug', 'about')->value('id');
        if (! $pageId) {
            return;
        }

        $heroes = [
            'ru' => [
                'label' => 'Профиль компании',
                'title' => 'Marine Technical Solutions',
                'subtitle' => 'Глобальный стандарт управления флотом с 2010 года',
                'body' => '<p>В современной морской индустрии, где границы между регионами стираются, а требования к безопасности и технологичности растут по экспоненте, успех судовладельца зависит от надёжности его операционного партнёра.</p>'
                    . '<p>Marine Technical Solutions (MTS) — это частная интегрированная судоходная компания, которая с 2010 года стоит на страже интересов международного морского бизнеса, обеспечивая безупречное управление активами в любой точке мирового океана.</p>',
            ],
            'en' => [
                'label' => 'Company profile',
                'title' => 'Marine Technical Solutions',
                'subtitle' => 'A global standard of fleet management since 2010',
                'body' => '<p>In today’s maritime industry, where regional boundaries blur and demands for safety and technology grow exponentially, an owner’s success depends on the reliability of the operational partner.</p>'
                    . '<p>Marine Technical Solutions (MTS) is a private integrated shipping company that, since 2010, has safeguarded the interests of international maritime business—delivering impeccable asset management anywhere in the world ocean.</p>',
            ],
        ];

        foreach ($heroes as $locale => $sec1) {
            $row = DB::table('content_page_translations')
                ->where('content_page_id', $pageId)
                ->where('locale', $locale)
                ->first();
            if (! $row) {
                continue;
            }
            $data = json_decode($row->body, true);
            if (! is_array($data)) {
                continue;
            }
            $data['sec1Hero'] = [
                'label' => $sec1['label'],
                'title' => $sec1['title'],
                'subtitle' => $sec1['subtitle'],
                'body' => $sec1['body'],
            ];
            $data['hideHeroPrimaryButton'] = true;
            $data['hideHeroSecondaryButton'] = true;

            DB::table('content_page_translations')
                ->where('content_page_id', $pageId)
                ->where('locale', $locale)
                ->update([
                    'body' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                    'updated_at' => now(),
                ]);
        }
    }

    private function ensureListingShellPage(string $slug, array $titlesByLocale): void
    {
        $existingId = DB::table('content_pages')->where('slug', $slug)->value('id');
        if ($existingId) {
            return;
        }

        $now = now();
        $pageId = DB::table('content_pages')->insertGetId([
            'slug' => $slug,
            'is_published' => true,
            'sort_order' => 0,
            'show_inquiry_form' => false,
            'show_public_title' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $emptyHero = [
            'titleFormatted' => [
                'spans' => [['text' => '', 'tone' => 'text']],
            ],
            'lead' => '',
        ];

        foreach ($titlesByLocale as $locale => $title) {
            $body = [
                'hero' => $emptyHero,
                'showInquiryForm' => false,
                'hideInquiryFormIntro' => false,
                'hideInquiryFormCardHeading' => false,
                'heroImage' => '',
                'sectionOrder' => ['listing'],
                'sectionVisibility' => ['listing' => true],
                'customSections' => [],
            ];

            DB::table('content_page_translations')->insert([
                'content_page_id' => $pageId,
                'locale' => $locale,
                'title' => $title,
                'excerpt' => null,
                'body' => json_encode($body, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                'seo_title' => null,
                'seo_description' => null,
                'seo_keywords' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
};
