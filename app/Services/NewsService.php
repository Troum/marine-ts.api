<?php

namespace App\Services;

use App\Contracts\Repositories\NewsRepositoryInterface;
use App\Contracts\Services\NewsServiceInterface;
use App\DTO\News\StoreNewsDto;
use App\DTO\News\UpdateNewsDto;
use App\Models\News;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
        return $this->newsRepository->getOne($id);
    }

    public function getBySlug(string $slug): News
    {
        /** @var News */
        return News::query()->where('slug', $slug)->firstOrFail();
    }

    public function create(StoreNewsDto $dto): News
    {
        $data = [
            'title' => $dto->title,
            'slug' => $dto->slug,
            'excerpt' => $dto->excerpt,
            'content' => $dto->content,
            'date' => $dto->date,
            'author' => $dto->author,
            'category' => $dto->category,
            'featured' => $dto->featured,
            'image' => $dto->image,
            'seo_title' => $dto->seo_title,
            'seo_description' => $dto->seo_description,
            'seo_keywords' => $dto->seo_keywords,
        ];

        return $this->newsRepository->createOne(array_filter(
            $data,
            static fn (mixed $v): bool => $v !== null
        ));
    }

    public function update(News $news, UpdateNewsDto $dto): News
    {
        $payload = $this->filterNulls($dto->toArray());
        if ($payload === []) {
            return $news->fresh() ?? $news;
        }
        $this->newsRepository->updateOne($news, $payload);

        return $news->refresh();
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
