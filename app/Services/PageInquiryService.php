<?php

namespace App\Services;

use App\Contracts\Repositories\PageInquiryRepositoryInterface;
use App\Contracts\Services\PageInquiryServiceInterface;
use App\DTO\PageInquiry\StorePageInquiryDto;
use App\Mail\PageInquirySubmittedMail;
use App\Models\PageInquiry;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

final class PageInquiryService implements PageInquiryServiceInterface
{
    public function __construct(
        private readonly PageInquiryRepositoryInterface $pageInquiryRepository,
    ) {}

    public function store(StorePageInquiryDto $dto, ?string $ip): PageInquiry
    {
        /** @var PageInquiry $inquiry */
        $inquiry = $this->pageInquiryRepository->createOne([
            'name' => $dto->name,
            'company' => $dto->company,
            'position' => $dto->position,
            'phone' => $dto->phone,
            'email' => $dto->email,
            'vessel_types' => $dto->vessel_types,
            'vessel_type_labels' => $dto->vessel_type_labels ?? null,
            'vessels_count' => $dto->vessels_count,
            'vessel_flag' => $dto->vessel_flag,
            'main_ports' => $dto->main_ports,
            'required_services' => $dto->required_services,
            'required_service_labels' => $dto->required_service_labels ?? null,
            'message' => $dto->message,
            'source_page' => $dto->source_page,
            'ip' => $ip,
        ]);

        $recipients = $this->pageInquiryRecipients($dto->source_page);
        if ($recipients === []) {
            Log::warning('Page inquiry saved but no notification recipients configured', [
                'page_inquiry_id' => $inquiry->id,
                'source_page' => $dto->source_page,
            ]);
        } else {
            try {
                Mail::to($recipients)->send(new PageInquirySubmittedMail($inquiry));
            } catch (Throwable $e) {
                Log::error('Page inquiry email failed', [
                    'page_inquiry_id' => $inquiry->id,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return $inquiry;
    }

    /**
     * @return list<string>
     */
    private function pageInquiryRecipients(string $sourcePage): array
    {
        $s = trim($sourcePage);
        $key = ($s === 'ship-management' || str_starts_with($s, 'ship-management/'))
            ? 'ship_management'
            : 'default';

        $list = config('mail.inquiries.'.$key);
        if (! is_array($list)) {
            return [];
        }

        $out = [];
        foreach ($list as $addr) {
            $t = trim((string) $addr);
            if ($t !== '') {
                $out[] = $t;
            }
        }

        return array_values(array_unique($out));
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
