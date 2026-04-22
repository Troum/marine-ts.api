<?php

namespace App\Services;

use App\Contracts\Repositories\PageInquiryRepositoryInterface;
use App\Contracts\Services\PageInquiryServiceInterface;
use App\DTO\PageInquiry\StorePageInquiryDto;
use App\Models\PageInquiry;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class PageInquiryService implements PageInquiryServiceInterface
{
    public function __construct(
        private readonly PageInquiryRepositoryInterface $pageInquiryRepository,
    ) {}

    public function store(StorePageInquiryDto $dto, ?string $ip): PageInquiry
    {
        /** @var PageInquiry */
        return $this->pageInquiryRepository->createOne([
            'name' => $dto->name,
            'company' => $dto->company,
            'position' => $dto->position,
            'phone' => $dto->phone,
            'email' => $dto->email,
            'vessel_types' => $dto->vessel_types,
            'vessels_count' => $dto->vessels_count,
            'vessel_flag' => $dto->vessel_flag,
            'main_ports' => $dto->main_ports,
            'required_services' => $dto->required_services,
            'message' => $dto->message,
            'source_page' => $dto->source_page,
            'ip' => $ip,
        ]);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginateManage(int $perPage, int $page, array $filters): LengthAwarePaginator
    {
        return $this->pageInquiryRepository->paginateManageList($perPage, $page, $filters);
    }

    public function markReadIfUnread(PageInquiry $inquiry): PageInquiry
    {
        if ($inquiry->read_at === null) {
            $this->pageInquiryRepository->updateOne($inquiry, ['read_at' => now()]);
        }

        /** @var PageInquiry */
        return $this->pageInquiryRepository->getOne($inquiry->id);
    }

    public function delete(PageInquiry $inquiry): void
    {
        $this->pageInquiryRepository->deleteOne($inquiry);
    }
}
