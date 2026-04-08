<?php

namespace App\Services;

use App\Contracts\Repositories\ApplicationFormRepositoryInterface;
use App\Contracts\Services\ApplicationFormServiceInterface;
use App\Contracts\Services\VacancyServiceInterface;
use App\DTO\ApplicationForm\StoreApplicationFormDto;
use App\Enums\ApplicationFormStatus;
use App\Mail\DocumentsRequestedMail;
use App\Models\ApplicationForm;
use App\Models\Vacancy;
use App\Support\ApplicationFormPdfTemplateData;
use App\Support\RequestedDocumentCatalog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Spatie\LaravelPdf\Enums\Format;
use Spatie\LaravelPdf\Facades\Pdf;

final class ApplicationFormService implements ApplicationFormServiceInterface
{
    public function __construct(
        private readonly ApplicationFormRepositoryInterface $applicationFormRepository,
        private readonly VacancyServiceInterface $vacancyService,
    ) {}

    public function storeForPublishedVacancy(StoreApplicationFormDto $dto): ApplicationForm
    {
        $vacancy = $this->vacancyService->getBySlug($dto->slug);
        /** Mycro DTO рекурсивно переводит ключи в snake_case — приводим обратно к camelCase, как в JSON с фронта. */
        $payload = $this->camelCasePayloadKeys($dto->payload);
        $fullName = $this->buildFullName($payload);

        /** @var ApplicationForm */
        return $this->applicationFormRepository->createOne([
            'vacancy_id' => $vacancy->id,
            'status' => ApplicationFormStatus::Pending,
            'full_name' => $fullName,
            'email' => (string) ($payload['email'] ?? ''),
            'phone' => $payload['mobilePhone'] ?? null,
            'payload' => $payload,
        ]);
    }

    public function paginateForVacancy(Vacancy $vacancy, int $perPage, int $page, array $filters = []): LengthAwarePaginator
    {
        $defaults = [
            'vacancy_id' => $vacancy->id,
            'order_column' => 'id',
            'order_direction' => 'desc',
        ];

        return $this->applicationFormRepository->index($perPage, $page, array_merge($defaults, $filters));
    }

    public function paginateAll(int $perPage, int $page, array $filters = []): LengthAwarePaginator
    {
        return $this->applicationFormRepository->paginateAll($perPage, $page, $filters);
    }

    public function updateStatus(ApplicationForm $applicationForm, ApplicationFormStatus $status): ApplicationForm
    {
        $this->applicationFormRepository->updateOne($applicationForm, ['status' => $status]);

        /** @var ApplicationForm */
        return $this->applicationFormRepository->getOne($applicationForm->id);
    }

    /**
     * @param  list<string>  $documentKeys
     */
    public function requestDocuments(ApplicationForm $applicationForm, array $documentKeys): ApplicationForm
    {
        $allowed = array_flip(RequestedDocumentCatalog::validKeys());
        foreach ($documentKeys as $key) {
            if (! isset($allowed[$key])) {
                throw new InvalidArgumentException('Invalid document key: '.$key);
            }
        }

        $documentKeys = array_values(array_unique($documentKeys));

        $plainToken = Str::random(64);
        $hashHex = hash('sha256', $plainToken);

        $ttlDays = (int) config('app.document_upload_token_ttl_days', 14);

        $this->applicationFormRepository->updateOne($applicationForm, [
            'status' => ApplicationFormStatus::DocumentsRequested,
            'document_upload_token_hash' => $hashHex,
            'document_upload_token_expires_at' => now()->addDays($ttlDays),
            'requested_document_keys' => $documentKeys,
        ]);

        /** @var $fresh ApplicationForm */
        $fresh = $this->applicationFormRepository->getOne($applicationForm->id);

        $frontend = rtrim((string) config('app.frontend_url'), '/');
        $uploadUrl = $frontend.'/application-forms/upload/'.$plainToken;

        Mail::to($fresh->email)->send(new DocumentsRequestedMail($fresh, $uploadUrl, $documentKeys));

        return $fresh;
    }

    public function pdfDownload(ApplicationForm $applicationForm)
    {
        $slug = $applicationForm->vacancy?->slug
            ? Str::slug($applicationForm->vacancy->slug)
            : 'vacancy';
        $filename = 'anketa-'.$applicationForm->id.'-'.$slug.'.pdf';

        return Pdf::view('pdf.application-form', ApplicationFormPdfTemplateData::make($applicationForm))
            ->format(Format::A4)
            ->name($filename)
            ->download();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function camelCasePayloadKeys(array $data): array
    {
        $out = [];
        foreach ($data as $key => $value) {
            $newKey = is_string($key) ? Str::camel($key) : $key;
            if (is_array($value)) {
                $out[$newKey] = $this->camelCasePayloadKeys($value);
            } else {
                $out[$newKey] = $value;
            }
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function buildFullName(array $payload): string
    {
        $fromParts = trim(implode(' ', array_filter([
            isset($payload['lastName']) ? (string) $payload['lastName'] : '',
            isset($payload['firstName']) ? (string) $payload['firstName'] : '',
            isset($payload['fathersName']) ? (string) $payload['fathersName'] : '',
        ])));

        if ($fromParts !== '') {
            return mb_substr($fromParts, 0, 500);
        }

        if (! empty($payload['surnameAndName']) && is_string($payload['surnameAndName'])) {
            return mb_substr(trim($payload['surnameAndName']), 0, 500);
        }

        return mb_substr((string) ($payload['email'] ?? ''), 0, 500);
    }
}
