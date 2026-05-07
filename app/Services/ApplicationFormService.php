<?php

namespace App\Services;

use App\Contracts\Repositories\ApplicationFormRepositoryInterface;
use App\Contracts\Services\ApplicationFormServiceInterface;
use App\Contracts\Services\VacancyServiceInterface;
use App\DTO\ApplicationForm\StoreApplicationFormDto;
use App\DTO\ApplicationForm\StoreOpenApplicationFormDto;
use App\Enums\ApplicationFormStatus;
use App\Mail\ApplicationFormSubmittedMail;
use App\Mail\DocumentsRequestedMail;
use App\Models\ApplicationForm;
use App\Models\Vacancy;
use App\Support\ApplicationFormPdfTemplateData;
use App\Support\RequestedDocumentCatalog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;
use Spatie\LaravelPdf\Enums\Format;
use Spatie\LaravelPdf\Facades\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

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

    public function storeOpenApplication(StoreOpenApplicationFormDto $dto): ApplicationForm
    {
        $payload = $this->camelCasePayloadKeys($dto->payload);
        $fullName = $this->buildFullName($payload);

        /** @var ApplicationForm */
        return $this->applicationFormRepository->createOne([
            'vacancy_id' => null,
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

    public function destroy(ApplicationForm $applicationForm): void
    {
        $id = (int) $applicationForm->getKey();
        $paths = $this->applicationFormStoragePaths($applicationForm);

        $this->applicationFormRepository->deleteOne($applicationForm, false);

        $disk = Storage::disk('local');
        foreach ($paths as $path) {
            if ($this->isSafeApplicationFormStoragePath($path, $id)) {
                $disk->delete($path);
            }
        }
        foreach ($this->applicationFormStorageDirectories($id) as $directory) {
            $disk->deleteDirectory($directory);
        }
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
        return Pdf::view('pdf.application-form', ApplicationFormPdfTemplateData::make($applicationForm))
            ->format(Format::A4)
            ->name($applicationForm->pdfFileName())
            ->download();
    }

    public function attachPhoto(ApplicationForm $applicationForm, UploadedFile $file): ApplicationForm
    {
        if (! $file->isValid()) {
            throw new InvalidArgumentException('Photo upload is not valid.');
        }

        /** Удаляем старое фото, если кандидат повторно отправляет анкету и файл уже есть. */
        $payload = is_array($applicationForm->payload) ? $applicationForm->payload : [];
        $previousPath = isset($payload['photoStoredPath']) && is_string($payload['photoStoredPath'])
            ? $payload['photoStoredPath']
            : null;
        if ($previousPath !== null && Storage::disk('local')->exists($previousPath)) {
            Storage::disk('local')->delete($previousPath);
        }

        $ext = strtolower($file->getClientOriginalExtension() ?: 'bin');
        $basename = 'photo_'.Str::uuid()->toString().'.'.$ext;
        $path = $file->storeAs(
            'application-form-photos/'.$applicationForm->id,
            $basename,
            'local',
        );

        if (! is_string($path) || $path === '') {
            throw new RuntimeException('Failed to store application form photo.');
        }

        $payload['photoFileName'] = $file->getClientOriginalName();
        $payload['photoStoredPath'] = $path;
        $payload['photoMime'] = $file->getMimeType();
        $payload['photoSize'] = $file->getSize();
        $payload['photoUploadedAt'] = now()->toIso8601String();

        $this->applicationFormRepository->updateOne($applicationForm, ['payload' => $payload]);

        /** @var ApplicationForm */
        return $this->applicationFormRepository->getOne($applicationForm->id);
    }

    public function sendCrewingSubmittedNotification(ApplicationForm $applicationForm): void
    {
        try {
            $pdfContent = Pdf::view('pdf.application-form', ApplicationFormPdfTemplateData::make($applicationForm))
                ->format(Format::A4)
                ->generatePdfContent();

            $recipients = array_values(array_filter(config('mail.application_form.recipients', [])));
            if ($recipients === []) {
                $fallback = trim((string) config('mail.crewing_notification.address'));
                $recipients = $fallback !== '' ? [$fallback] : [];
            }
            if ($recipients === []) {
                Log::error('ApplicationForm email skipped: no recipients configured', [
                    'application_form_id' => $applicationForm->id,
                ]);

                return;
            }

            Mail::to($recipients)->send(new ApplicationFormSubmittedMail($applicationForm, $pdfContent));
        } catch (Throwable $e) {
            Log::error('ApplicationForm PDF or crewing email failed', [
                'application_form_id' => $applicationForm->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    public function downloadPhoto(ApplicationForm $applicationForm): StreamedResponse
    {
        $payload = is_array($applicationForm->payload) ? $applicationForm->payload : [];
        $path = isset($payload['photoStoredPath']) && is_string($payload['photoStoredPath'])
            ? $payload['photoStoredPath']
            : null;
        if ($path === null || ! Storage::disk('local')->exists($path)) {
            abort(404);
        }

        $original = isset($payload['photoFileName']) && is_string($payload['photoFileName']) && $payload['photoFileName'] !== ''
            ? $payload['photoFileName']
            : basename($path);

        return Storage::disk('local')->download($path, $original);
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

    /**
     * Все файлы анкеты складываются в директории по ID, поэтому удаление
     * директорий покрывает фото, дозагруженные документы и будущие PDF-кэши.
     *
     * @return list<string>
     */
    private function applicationFormStorageDirectories(int $applicationFormId): array
    {
        return [
            'application-form-photos/'.$applicationFormId,
            'application-form-supplements/'.$applicationFormId,
            'application-form-pdfs/'.$applicationFormId,
        ];
    }

    /**
     * @return list<string>
     */
    private function applicationFormStoragePaths(ApplicationForm $applicationForm): array
    {
        $payload = is_array($applicationForm->payload) ? $applicationForm->payload : [];
        $paths = [];

        foreach (['photoStoredPath', 'photo_stored_path', 'pdfStoredPath', 'pdf_stored_path'] as $key) {
            if (isset($payload[$key]) && is_string($payload[$key]) && $payload[$key] !== '') {
                $paths[] = $payload[$key];
            }
        }

        foreach (['supplementaryFiles', 'supplementary_files'] as $mapKey) {
            if (! is_array($payload[$mapKey] ?? null)) {
                continue;
            }
            foreach ($payload[$mapKey] as $entry) {
                if (! is_array($entry)) {
                    continue;
                }
                foreach (['storedPath', 'stored_path'] as $pathKey) {
                    if (isset($entry[$pathKey]) && is_string($entry[$pathKey]) && $entry[$pathKey] !== '') {
                        $paths[] = $entry[$pathKey];
                    }
                }
            }
        }

        return array_values(array_unique($paths));
    }

    private function isSafeApplicationFormStoragePath(string $path, int $applicationFormId): bool
    {
        $path = ltrim($path, '/');

        foreach ($this->applicationFormStorageDirectories($applicationFormId) as $directory) {
            if (Str::startsWith($path, $directory.'/')) {
                return true;
            }
        }

        return false;
    }
}
