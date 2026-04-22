<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\PageInquiryServiceInterface;
use App\DTO\PageInquiry\StorePageInquiryDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\PageInquiry\DestroyPageInquiryRequest;
use App\Http\Requests\PageInquiry\IndexPageInquiriesManageRequest;
use App\Http\Requests\PageInquiry\StorePageInquiryRequest;
use App\Http\Resources\PageInquiryCollection;
use App\Http\Resources\PageInquiryResource;
use App\Models\PageInquiry;
use Illuminate\Http\JsonResponse;

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

    public function manageIndex(IndexPageInquiriesManageRequest $request): PageInquiryCollection
    {
        $dto = $request->toPaginatedTableDto();

        return new PageInquiryCollection($this->pageInquiryService->paginateManage($dto->perPage, $dto->page, $dto->filters));
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
