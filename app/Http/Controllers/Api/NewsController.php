<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\NewsServiceInterface;
use App\DTO\News\StoreNewsDto;
use App\DTO\News\UpdateNewsDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\News\DestroyNewsRequest;
use App\Http\Requests\News\StoreNewsRequest;
use App\Http\Requests\News\UpdateNewsRequest;
use App\Http\Resources\NewsCollection;
use App\Http\Resources\NewsResource;
use App\Models\News;
use App\Support\AdminListQuery;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function __construct(
        private readonly NewsServiceInterface $newsService,
    ) {}

    public function index(Request $request): NewsCollection
    {
        $perPage = min(max((int) $request->query('per_page', 100), 1), 500);
        $page = max(1, (int) $request->query('page', 1));

        $filters = array_merge(
            AdminListQuery::sortOrder($request, ['id', 'title', 'date', 'category', 'author', 'slug'], 'id'),
            array_filter(['search' => AdminListQuery::search($request)])
        );

        return new NewsCollection($this->newsService->paginate($perPage, $page, $filters));
    }

    public function show(News $news): NewsResource
    {
        return new NewsResource($news);
    }

    public function showBySlug(string $slug): NewsResource
    {
        $news = $this->newsService->getBySlug($slug);

        return new NewsResource($news);
    }

    public function store(StoreNewsRequest $request)
    {
        $dto = new StoreNewsDto($request->validated());
        $news = $this->newsService->create($dto);

        return (new NewsResource($news))->response()->setStatusCode(201);
    }

    public function update(UpdateNewsRequest $request, News $news): NewsResource
    {
        $dto = new UpdateNewsDto($request->validated());
        $news = $this->newsService->update($news, $dto);

        return new NewsResource($news);
    }

    public function destroy(DestroyNewsRequest $request, News $news)
    {
        $this->newsService->delete($news, true);

        return response()->json(null, 204);
    }
}
