<?php

namespace App\Contracts\Services;

use App\DTO\ApplicationForm\StoreApplicationFormDto;
use App\DTO\ApplicationForm\StoreOpenApplicationFormDto;
use App\Enums\ApplicationFormStatus;
use App\Models\ApplicationForm;
use App\Models\Vacancy;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
     * Удалить анкету и файлы, которые были сохранены вместе с ней.
     */
    public function destroy(ApplicationForm $applicationForm): void;

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

    /**
     * Прикрепить фото кандидата к уже созданной анкете.
     * Файл сохраняется на disk `local`, метаданные пишутся в `payload`.
     */
    public function attachPhoto(ApplicationForm $applicationForm, UploadedFile $file): ApplicationForm;

    /**
     * Сгенерировать PDF и отправить уведомление crewing (после сохранения анкеты и опционального фото).
     */
    public function sendCrewingSubmittedNotification(ApplicationForm $applicationForm): void;

    /**
     * Уведомить crewing о дозагрузке документов кандидатом по ссылке из письма.
     *
     * @param  list<string>  $uploadedKeys
     */
    public function sendSupplementaryDocumentsUploadedNotification(ApplicationForm $applicationForm, array $uploadedKeys): void;

    /**
     * Скачать (или показать) фото кандидата (admin-эндпоинт).
     */
    public function downloadPhoto(ApplicationForm $applicationForm): StreamedResponse;
}
