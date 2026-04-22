<?php

namespace App\Services;

use App\Contracts\Repositories\NewsRepositoryInterface;
use App\Contracts\Services\NewsServiceInterface;
use App\DTO\News\StoreNewsDto;
use App\DTO\News\UpdateNewsDto;
use App\Models\News;
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
        return $this->newsRepository->findBySlugWithTranslations($slug);
    }

    public function create(StoreNewsDto $dto): News
    {
        $default = (string) config('marine.default_locale');
        if (! isset($dto->translations[$default])) {
            throw new \InvalidArgumentException("translations.$default is required.");
        }

        $defaultRow = $dto->translations[$default];
        $slug = $dto->slug ?? $this->newsRepository->uniqueSlugForTitle(
            is_array($defaultRow) ? (string) ($defaultRow['title'] ?? '') : '',
        );

        return DB::transaction(function () use ($dto, $slug): News {
            /** @var News $news */
            $news = $this->newsRepository->createOne(array_filter([
                'slug' => $slug,
                'date' => $dto->date,
                'author' => $dto->author,
                'featured' => $dto->featured,
                'image' => $dto->image,
            ], static fn (mixed $v): bool => $v !== null));

            $this->newsRepository->syncNewsTranslations($news, $dto->translations);

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
                $this->newsRepository->syncNewsTranslations($news, $dto->translations);
            });
        }

        return $news->refresh()->load('translations');
    }

    public function delete(News $news, bool $soft = true): void
    {
        $this->newsRepository->deleteOne($news, $soft);
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
