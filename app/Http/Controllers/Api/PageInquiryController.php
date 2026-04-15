<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\PageInquiryServiceInterface;
use App\DTO\PageInquiry\StorePageInquiryDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\PageInquiry\DestroyPageInquiryRequest;
use App\Http\Requests\PageInquiry\StorePageInquiryRequest;
use App\Http\Resources\PageInquiryCollection;
use App\Http\Resources\PageInquiryResource;
use App\Models\PageInquiry;
use App\Support\AdminListQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PageInquiryController extends Controller
{
    public function __construct(
        private readonly PageInquiryServiceInterface $pageInquiryService,
    ) {}

    public function store(StorePageInquiryRequest $request): PageInquiryResource
    {
        $inquiry = $this->pageInquiryService->store(new StorePageInquiryDto($request->validated()), $request->ip());

        return new PageInquiryResource($inquiry);
    }

    public function manageIndex(Request $request): PageInquiryCollection
    {
        $this->authorize('viewAny', PageInquiry::class);

        $perPage = min(max((int) $request->query('per_page', 500), 1), 500);
        $page = max(1, (int) $request->query('page', 1));

        $filters = array_merge(
            AdminListQuery::sortOrder($request, ['id', 'created_at', 'updated_at', 'read_at'], 'id', 'desc'),
            array_filter([
                'search' => AdminListQuery::search($request),
            ])
        );
        $filters['read'] = AdminListQuery::readTriState($request);

        return new PageInquiryCollection($this->pageInquiryService->paginateManage($perPage, $page, $filters));
    }

    public function show(PageInquiry $page_inquiry): PageInquiryResource
    {
        $this->authorize('view', $page_inquiry);

        return new PageInquiryResource($this->pageInquiryService->markReadIfUnread($page_inquiry));
    }

    public function destroy(DestroyPageInquiryRequest $request, PageInquiry $page_inquiry): JsonResponse
    {
        $this->pageInquiryService->delete($page_inquiry);

        return response()->json(null, 204);
    }
}
