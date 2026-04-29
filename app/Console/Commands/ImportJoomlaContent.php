<?php

namespace App\Console\Commands;

use App\Models\ContentPage;
use App\Models\Project;
use App\Models\ProjectTranslation;
use App\Models\Service;
use App\Models\ServiceTranslation;
use App\Models\SiteSeoPage;
use App\Models\Vacancy;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use JsonException;

class ImportJoomlaContent extends Command
{
    protected $signature = 'mts:import-joomla-content
        {--manifest=../joomla-export/import-manifest.json : Path to import manifest}
        {--report=../joomla-export/import-report.json : Path to write import report}
        {--apply : Actually write changes. Without this option the command only dry-runs.}';

    protected $description = 'Safely imports cleaned Joomla/Gridbox content into the current Marine CMS.';

    /** @var array<int, array<string, mixed>> */
    private array $events = [];

    private bool $apply = false;

    public function handle(): int
    {
        $this->apply = (bool) $this->option('apply');

        $manifestPath = $this->absolutePath((string) $this->option('manifest'));
        if (! is_file($manifestPath)) {
            $this->error("Manifest not found: {$manifestPath}");

            return self::FAILURE;
        }

        try {
            $manifest = json_decode((string) file_get_contents($manifestPath), true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->error("Invalid manifest JSON: {$e->getMessage()}");

            return self::FAILURE;
        }

        $this->info($this->apply ? 'Applying Joomla content import...' : 'Dry-running Joomla content import...');

        if ($this->apply) {
            DB::transaction(fn () => $this->import($manifest));
        } else {
            $this->import($manifest);
        }

        $report = [
            'mode' => $this->apply ? 'apply' : 'dry-run',
            'generated_at' => now()->toIso8601String(),
            'manifest' => $manifestPath,
            'summary' => $this->summary(),
            'events' => $this->events,
        ];

        $reportPath = $this->absolutePath((string) $this->option('report'));
        $this->writeJson($reportPath, $report);
        $this->info("Report written: {$reportPath}");
        $this->table(['Action', 'Count'], collect($report['summary'])->map(fn ($count, $action) => [$action, $count])->all());

        return self::SUCCESS;
    }

    /**
     * @param  array<string, mixed>  $manifest
     */
    private function import(array $manifest): void
    {
        foreach ($manifest['structured_pages'] ?? [] as $item) {
            $this->upsertStructuredPage($item);
        }

        foreach ($manifest['content_pages'] ?? [] as $item) {
            $this->upsertNarrativeContentPage($item);
        }

        foreach ($manifest['services'] ?? [] as $item) {
            $this->upsertService($item);
        }

        foreach ($manifest['projects'] ?? [] as $item) {
            $this->upsertProject($item);
        }

        foreach ($manifest['vacancies'] ?? [] as $item) {
            $this->upsertVacancy($item);
        }

        foreach ($manifest['seo_pages'] ?? [] as $item) {
            $this->upsertSeoPage($item);
        }
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function upsertStructuredPage(array $item): void
    {
        $slug = $this->slug((string) $item['slug']);
        $page = $this->findContentPage($slug);
        $exists = $page->exists;

        $this->record($exists ? 'update' : 'create', 'structured_page', $slug, [
            'sections' => count($item['sections'] ?? []),
        ]);

        if (! $this->apply) {
            return;
        }

        $page->fill([
            'slug' => $slug,
            'is_published' => true,
            'sort_order' => (int) ($item['sort_order'] ?? 0),
            'show_inquiry_form' => (bool) ($item['show_inquiry_form'] ?? false),
        ]);
        $page->save();

        if (method_exists($page, 'restore') && $page->trashed()) {
            $page->restore();
        }

        $translation = $page->translations()->where('locale', 'ru')->first();
        $base = is_array($item['base'] ?? null) ? $item['base'] : [];
        $body = $this->mergeStructuredBody($translation?->body, $base, $item['sections'] ?? []);

        $this->upsertContentPageTranslation($page, [
            'locale' => 'ru',
            'title' => (string) ($item['title'] ?? Str::headline($slug)),
            'excerpt' => $this->firstSectionExcerpt($item['sections'] ?? []),
            'body' => $body,
        ]);
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function upsertNarrativeContentPage(array $item, ?string $contentableType = null, ?int $contentableId = null): ContentPage
    {
        $slug = $this->slug((string) $item['slug']);
        $page = $this->findContentPage($slug);
        $exists = $page->exists;

        $attrs = [
            'slug' => $slug,
            'is_published' => true,
            'sort_order' => (int) ($item['sort_order'] ?? 100),
            'show_inquiry_form' => (bool) ($item['show_inquiry_form'] ?? true),
            'contentable_type' => $contentableType,
            'contentable_id' => $contentableId,
        ];

        $this->record($exists ? 'update' : 'create', 'content_page', $slug, [
            'translations' => count($item['translations'] ?? []),
            'contentable' => $contentableType ? class_basename($contentableType).':'.$contentableId : null,
        ]);

        if (! $this->apply) {
            return $page;
        }

        $page->fill($attrs);
        $page->save();

        if (method_exists($page, 'restore') && $page->trashed()) {
            $page->restore();
        }

        foreach ($item['translations'] ?? [] as $translation) {
            $this->upsertContentPageTranslation($page, $translation);
        }

        return $page;
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function upsertService(array $item): void
    {
        $slug = $this->slug((string) $item['slug']);
        $service = $this->findService($item);
        $exists = $service->exists;

        $this->record($exists ? 'update' : 'create', 'service', $slug, [
            'translations' => count($item['translations'] ?? []),
        ]);

        if (! $this->apply) {
            return;
        }

        $service->fill([
            'icon_key' => (string) ($item['icon_key'] ?? 'Ship'),
            'sort_order' => (int) ($item['sort_order'] ?? 100),
            'image_path' => $item['image_path'] ?? $service->image_path,
        ]);
        $service->save();

        if (method_exists($service, 'restore') && $service->trashed()) {
            $service->restore();
        }

        foreach ($item['translations'] ?? [] as $translation) {
            $this->upsertServiceTranslation($service, $translation);
        }

        $contentPageTranslations = [];
        foreach ($item['translations'] ?? [] as $translation) {
            if (isset($translation['content_page']) && is_array($translation['content_page'])) {
                $contentPageTranslations[] = $translation['content_page'];
            }
        }

        if ($contentPageTranslations !== []) {
            $this->upsertNarrativeContentPage([
                'slug' => $slug,
                'sort_order' => (int) ($item['sort_order'] ?? 100),
                'show_inquiry_form' => true,
                'translations' => $contentPageTranslations,
            ], Service::class, $service->id);
        }
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function upsertProject(array $item): void
    {
        $slug = $this->slug((string) $item['slug']);
        $project = $this->findProject($item);
        $exists = $project->exists;

        $this->record($exists ? 'update' : 'create', 'project', $slug, [
            'translations' => count($item['translations'] ?? []),
        ]);

        if (! $this->apply) {
            return;
        }

        $project->fill([
            'type' => in_array($item['type'] ?? '', ['hull', 'engine', 'electrical'], true) ? $item['type'] : 'hull',
            'date' => (string) ($item['date'] ?? '2010+'),
            'image' => $item['image'] ?? $project->image,
        ]);
        $project->save();

        if (method_exists($project, 'restore') && $project->trashed()) {
            $project->restore();
        }

        foreach ($item['translations'] ?? [] as $translation) {
            $this->upsertProjectTranslation($project, $translation);
        }

        $contentPageTranslations = [];
        foreach ($item['translations'] ?? [] as $translation) {
            if (isset($translation['content_page']) && is_array($translation['content_page'])) {
                $contentPageTranslations[] = $translation['content_page'];
            }
        }

        if ($contentPageTranslations !== []) {
            $this->upsertNarrativeContentPage([
                'slug' => $slug,
                'sort_order' => 300,
                'show_inquiry_form' => true,
                'translations' => $contentPageTranslations,
            ], Project::class, $project->id);
        }
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function upsertVacancy(array $item): void
    {
        $slug = $this->slug((string) $item['slug']);
        $vacancy = Vacancy::withTrashed()->where('slug', $slug)->first() ?? new Vacancy(['slug' => $slug]);
        $exists = $vacancy->exists;

        $this->record($exists ? 'update' : 'create', 'vacancy', $slug, [
            'translations' => count($item['translations'] ?? []),
        ]);

        if (! $this->apply) {
            return;
        }

        $vacancy->fill([
            'slug' => $slug,
            'sort_order' => (int) ($item['sort_order'] ?? 100),
            'is_published' => true,
        ]);
        $vacancy->save();

        if (method_exists($vacancy, 'restore') && $vacancy->trashed()) {
            $vacancy->restore();
        }

        foreach ($item['translations'] ?? [] as $translation) {
            $this->upsertVacancyTranslation($vacancy, $translation);
        }
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function upsertSeoPage(array $item): void
    {
        $slug = $this->slug((string) $item['slug']);
        $seoPage = SiteSeoPage::query()->firstOrNew(['slug' => $slug]);
        $exists = $seoPage->exists;

        $this->record($exists ? 'update' : 'create', 'seo_page', $slug, [
            'translations' => count($item['translations'] ?? []),
        ]);

        if (! $this->apply) {
            return;
        }

        $seoPage->fill(['slug' => $slug]);
        $seoPage->save();

        foreach ($item['translations'] ?? [] as $translation) {
            $seoPage->translations()->updateOrCreate(
                ['locale' => $translation['locale'] ?? 'ru'],
                [
                    'label' => $translation['label'] ?? $translation['seo_title'] ?? Str::headline($slug),
                    'seo_title' => $translation['seo_title'] ?? null,
                    'seo_description' => $translation['seo_description'] ?? null,
                    'seo_keywords' => $translation['seo_keywords'] ?? null,
                ],
            );
        }
    }

    /**
     * @param  array<string, mixed>  $translation
     */
    private function upsertContentPageTranslation(ContentPage $page, array $translation): void
    {
        $page->translations()->updateOrCreate(
            ['locale' => $translation['locale'] ?? 'ru'],
            [
                'title' => $translation['title'] ?? Str::headline($page->slug),
                'excerpt' => $translation['excerpt'] ?? null,
                'body' => $translation['body'] ?? '',
                'seo_title' => $translation['seo_title'] ?? null,
                'seo_description' => $translation['seo_description'] ?? null,
                'seo_keywords' => $translation['seo_keywords'] ?? null,
            ],
        );
    }

    /**
     * @param  array<string, mixed>  $translation
     */
    private function upsertServiceTranslation(Service $service, array $translation): void
    {
        $service->translations()->updateOrCreate(
            ['locale' => $translation['locale'] ?? 'ru'],
            [
                'title' => $translation['title'] ?? 'Service',
                'description' => $translation['description'] ?? '',
                'features' => array_values(array_filter((array) ($translation['features'] ?? ['Service']))),
                'seo_title' => $translation['seo_title'] ?? null,
                'seo_description' => $translation['seo_description'] ?? null,
                'seo_keywords' => $translation['seo_keywords'] ?? null,
            ],
        );
    }

    /**
     * @param  array<string, mixed>  $translation
     */
    private function upsertProjectTranslation(Project $project, array $translation): void
    {
        $project->translations()->updateOrCreate(
            ['locale' => $translation['locale'] ?? 'ru'],
            [
                'title' => $translation['title'] ?? 'Project',
                'type_label' => $translation['type_label'] ?? 'Проект',
                'location' => $translation['location'] ?? 'Международные проекты',
                'description' => $translation['description'] ?? '',
                'stats' => $translation['stats'] ?? [],
                'seo_title' => $translation['seo_title'] ?? null,
                'seo_description' => $translation['seo_description'] ?? null,
                'seo_keywords' => $translation['seo_keywords'] ?? null,
            ],
        );
    }

    /**
     * @param  array<string, mixed>  $translation
     */
    private function upsertVacancyTranslation(Vacancy $vacancy, array $translation): void
    {
        $vacancy->translations()->updateOrCreate(
            ['locale' => $translation['locale'] ?? 'ru'],
            [
                'title' => $translation['title'] ?? 'Vacancy',
                'excerpt' => $translation['excerpt'] ?? '',
                'content' => $translation['content'] ?? null,
                'requirements' => array_values(array_filter((array) ($translation['requirements'] ?? []))),
                'location' => $translation['location'] ?? null,
                'employment_type' => $translation['employment_type'] ?? null,
                'seo_title' => $translation['seo_title'] ?? null,
                'seo_description' => $translation['seo_description'] ?? null,
                'seo_keywords' => $translation['seo_keywords'] ?? null,
            ],
        );
    }

    private function findContentPage(string $slug): ContentPage
    {
        return ContentPage::withTrashed()->where('slug', $slug)->first() ?? new ContentPage(['slug' => $slug]);
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function findService(array $item): Service
    {
        $slug = $this->slug((string) $item['slug']);
        $existingPage = ContentPage::query()->where('slug', $slug)->where('contentable_type', Service::class)->first();
        if ($existingPage?->contentable instanceof Service) {
            return $existingPage->contentable;
        }

        $ruTitle = collect($item['translations'] ?? [])->firstWhere('locale', 'ru')['title'] ?? null;
        if (is_string($ruTitle)) {
            $translation = ServiceTranslation::query()->where('locale', 'ru')->where('title', $ruTitle)->first();
            if ($translation?->service) {
                return $translation->service;
            }
        }

        foreach ((array) ($item['match_keywords'] ?? []) as $keyword) {
            $translation = ServiceTranslation::query()
                ->where('locale', 'ru')
                ->where('title', 'like', '%'.$keyword.'%')
                ->first();
            if ($translation?->service) {
                return $translation->service;
            }
        }

        return new Service;
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function findProject(array $item): Project
    {
        $slug = $this->slug((string) $item['slug']);
        $existingPage = ContentPage::query()->where('slug', $slug)->where('contentable_type', Project::class)->first();
        if ($existingPage?->contentable instanceof Project) {
            return $existingPage->contentable;
        }

        $ruTitle = collect($item['translations'] ?? [])->firstWhere('locale', 'ru')['title'] ?? null;
        if (is_string($ruTitle)) {
            $translation = ProjectTranslation::query()->where('locale', 'ru')->where('title', $ruTitle)->first();
            if ($translation?->project) {
                return $translation->project;
            }
        }

        return new Project;
    }

    /**
     * @param  array<int, mixed>  $sections
     */
    private function mergeStructuredBody(?string $existingBody, array $base, array $sections): string
    {
        $data = $base;
        if (is_string($existingBody) && trim($existingBody) !== '') {
            $decoded = json_decode($existingBody, true);
            if (is_array($decoded)) {
                $data = $decoded;
            }
        }

        $data['customSections'] = array_values(array_filter(
            (array) ($data['customSections'] ?? []),
            fn ($section) => is_array($section) && ! in_array($section['id'] ?? null, collect($sections)->pluck('id')->all(), true),
        ));

        foreach ($sections as $section) {
            if (is_array($section)) {
                unset($section['source']);
                $data['customSections'][] = $section;
                $key = 'custom:'.$section['id'];
                $data['sectionOrder'] = array_values(array_unique([...(array) ($data['sectionOrder'] ?? []), $key]));
                $data['sectionVisibility'] = array_merge((array) ($data['sectionVisibility'] ?? []), [$key => true]);
            }
        }

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    /**
     * @param  array<int, mixed>  $sections
     */
    private function firstSectionExcerpt(array $sections): ?string
    {
        foreach ($sections as $section) {
            if (! is_array($section)) {
                continue;
            }
            foreach ($section['blocks'] ?? [] as $block) {
                if (! is_array($block) || ($block['content'] ?? '') === '') {
                    continue;
                }
                $plain = trim((string) preg_replace('/\s+/', ' ', strip_tags((string) $block['content'])));

                return Str::limit($plain, 220);
            }
        }

        return null;
    }

    private function slug(string $value): string
    {
        $slug = Str::slug($value);

        return $slug !== '' ? $slug : 'page';
    }

    /**
     * @param  array<string, mixed>  $details
     */
    private function record(string $action, string $type, string $key, array $details = []): void
    {
        $this->events[] = compact('action', 'type', 'key', 'details');
    }

    /**
     * @return array<string, int>
     */
    private function summary(): array
    {
        $summary = [];
        foreach ($this->events as $event) {
            $key = $event['action'].'.'.$event['type'];
            $summary[$key] = ($summary[$key] ?? 0) + 1;
        }
        ksort($summary);

        return $summary;
    }

    private function absolutePath(string $path): string
    {
        if (str_starts_with($path, '/')) {
            return $path;
        }

        return base_path($path);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function writeJson(string $path, array $payload): void
    {
        $dir = dirname($path);
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        file_put_contents($path, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
}
