<?php

namespace App\Contracts\Services;

use App\DTO\ApplicationForm\StoreApplicationFormDto;
use App\DTO\ApplicationForm\StoreOpenApplicationFormDto;
use App\Enums\ApplicationFormStatus;
use App\Models\ApplicationForm;
use App\Models\Vacancy;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ApplicationFormServiceInterface
{
    public function storeForPublishedVacancy(StoreApplicationFormDto $dto): ApplicationForm;

    /**
     * Анкета без привязки к вакансии (открытая заявка в базу кандидатов).
     */
    public function storeOpenApplication(StoreOpenApplicationFormDto $dto): ApplicationForm;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginateForVacancy(Vacancy $vacancy, int $perPage, int $page, array $filters = []): LengthAwarePaginator;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginateAll(int $perPage, int $page, array $filters = []): LengthAwarePaginator;

    public function updateStatus(ApplicationForm $applicationForm, ApplicationFormStatus $status): ApplicationForm;

    /**
     * @param  list<string>  $documentKeys
     */
    public function requestDocuments(ApplicationForm $applicationForm, array $documentKeys): ApplicationForm;

    /**
     * PDF с данными анкеты (тот же шаблон, что уходит в письмо crewing).
     *
     * @return mixed ответ Spatie Laravel PDF (download)
     */
    public function pdfDownload(ApplicationForm $applicationForm);
}
