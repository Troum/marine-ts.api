<?php

namespace App\Services;

use App\Contracts\Repositories\NewsRepositoryInterface;
use App\Contracts\Services\NewsServiceInterface;
use App\DTO\News\StoreNewsDto;
use App\DTO\News\UpdateNewsDto;
use App\Models\News;
use App\Support\MarineLocale;
use App\Support\NormalizeTranslationInput;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final class NewsService implements NewsServiceInterface
{
    public function __construct(
        private readonly NewsRepositoryInterface $newsRepository,
    ) {}

    public function paginate(int $perPage, int $page, array $filters = []): LengthAwarePaginator
    {
        return $this->newsRepository->index($perPage, $page, $filters);
    }

    public function getById(int|string $id): News
    {
        /** @var News */
        return $this->newsRepository->getOne($id)->load('translations');
    }

    public function getBySlug(string $slug): News
    {
        /** @var News */
        return News::query()->where('slug', $slug)->with('translations')->firstOrFail();
    }

    public function create(StoreNewsDto $dto): News
    {
        $default = (string) config('marine.default_locale');
        if (! isset($dto->translations[$default])) {
            throw new \InvalidArgumentException("translations.$default is required.");
        }

        $defaultRow = $dto->translations[$default];
        $slug = $dto->slug ?? News::ensureUniqueSlug(
            News::slugFromTitle(is_array($defaultRow) ? (string) ($defaultRow['title'] ?? '') : ''),
        );

        return DB::transaction(function () use ($dto, $slug, $default): News {
            /** @var News $news */
            $news = $this->newsRepository->createOne(array_filter([
                'slug' => $slug,
                'date' => $dto->date,
                'author' => $dto->author,
                'featured' => $dto->featured,
                'image' => $dto->image,
            ], static fn (mixed $v): bool => $v !== null));

            $this->syncNewsTranslations($news, $dto->translations);

            return $news->load('translations');
        });
    }

    public function update(News $news, UpdateNewsDto $dto): News
    {
        $payload = $this->filterNulls([
            'slug' => $dto->slug,
            'date' => $dto->date,
            'author' => $dto->author,
            'featured' => $dto->featured,
            'image' => $dto->image,
        ]);
        if ($payload !== []) {
            $this->newsRepository->updateOne($news, $payload);
        }

        if ($dto->translations !== null) {
            DB::transaction(function () use ($news, $dto): void {
                $this->syncNewsTranslations($news, $dto->translations);
            });
        }

        return $news->refresh()->load('translations');
    }

    public function delete(News $news, bool $soft = true): void
    {
        $this->newsRepository->deleteOne($news, $soft);
    }

    /**
     * @param  array<string, array<string, mixed>>  $translations
     */
    private function syncNewsTranslations(News $news, array $translations): void
    {
        foreach (config('marine.locales') as $locale) {
            if (! isset($translations[$locale])) {
                continue;
            }
            if (! MarineLocale::isSupported((string) $locale)) {
                continue;
            }
            $row = NormalizeTranslationInput::newsLocaleRow($translations[$locale]);
            $news->translations()->updateOrCreate(
                ['locale' => $locale],
                $row
            );
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function filterNulls(array $data): array
    {
        return array_filter($data, static fn (mixed $v): bool => $v !== null);
    }
}
