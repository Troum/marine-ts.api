<?php

namespace App\Services;

use App\Contracts\Repositories\ApplicationFormRepositoryInterface;
use App\Contracts\Services\ApplicationFormSupplementaryDocumentServiceInterface;
use App\Models\ApplicationForm;
use App\Support\RequestedDocumentCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class ApplicationFormSupplementaryDocumentService implements ApplicationFormSupplementaryDocumentServiceInterface
{
    public function __construct(
        private readonly ApplicationFormRepositoryInterface $applicationFormRepository,
    ) {}

    public function publicUploadSession(string $token): array
    {
        $form = $this->resolveFormByPlainToken($token);
        if ($form === null) {
            return ['status' => 404, 'payload' => ['message' => 'Ссылка недействительна.']];
        }

        if ($form->document_upload_token_expires_at?->isPast()) {
            return ['status' => 410, 'payload' => ['message' => 'Срок действия ссылки истёк.']];
        }

        $keys = $form->requested_document_keys ?? [];
        $requestedDocuments = [];
        foreach ($keys as $key) {
            $requestedDocuments[] = [
                'key' => $key,
                'label' => RequestedDocumentCatalog::labelFor($key),
            ];
        }

        $payload = $form->payload ?? [];
        $sup = $this->supplementaryMapFromPayload($payload);
        $uploaded = [];
        foreach ($keys as $key) {
            if (isset($sup[$key])) {
                $uploaded[$key] = $this->publicSupplementarySummary($sup[$key]);
            }
        }

        return [
            'status' => 200,
            'payload' => [
                'data' => [
                    'fullName' => $form->full_name,
                    'expiresAt' => $form->document_upload_token_expires_at?->toIso8601String(),
                    'requestedDocuments' => $requestedDocuments,
                    'uploaded' => $uploaded,
                ],
            ],
        ];
    }

    public function publicUploadStore(string $token, Request $request): array
    {
        $form = $this->resolveFormByPlainToken($token);
        if ($form === null) {
            return ['status' => 404, 'payload' => ['message' => 'Ссылка недействительна.']];
        }

        if ($form->document_upload_token_expires_at?->isPast()) {
            return ['status' => 410, 'payload' => ['message' => 'Срок действия ссылки истёк.']];
        }

        $keys = $form->requested_document_keys ?? [];
        if ($keys === []) {
            return ['status' => 422, 'payload' => ['message' => 'Документы не запрашивались.']];
        }

        $files = $request->file('documents', []);
        if (! is_array($files)) {
            throw ValidationException::withMessages([
                'documents' => ['Некорректный формат данных.'],
            ]);
        }

        $payload = $form->payload ?? [];
        $sup = $this->supplementaryMapFromPayload($payload);

        $uploadedKeys = [];
        foreach ($keys as $key) {
            $file = $files[$key] ?? null;
            if ($file === null || ! $file->isValid()) {
                continue;
            }

            Validator::make(
                ['file' => $file],
                ['file' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,webp']],
            )->validate();

            $ext = strtolower($file->getClientOriginalExtension() ?: 'bin');
            $basename = $key.'_'.Str::uuid()->toString().'.'.$ext;

            $path = $file->storeAs(
                'application-form-supplements/'.$form->id,
                $basename,
                'local',
            );

            $sup[$key] = [
                'originalName' => $file->getClientOriginalName(),
                'storedPath' => $path,
                'mime' => $file->getMimeType(),
                'size' => $file->getSize(),
                'uploadedAt' => now()->toIso8601String(),
            ];

            $uploadedKeys[] = $key;
        }

        if ($uploadedKeys !== []) {
            $payload['supplementaryFiles'] = $sup;
            $this->applicationFormRepository->updateOne($form, ['payload' => $payload]);
        }

        if ($uploadedKeys === []) {
            throw ValidationException::withMessages([
                'documents' => ['Загрузите хотя бы один файл из запрошенных типов.'],
            ]);
        }

        return [
            'status' => 200,
            'payload' => [
                'data' => [
                    'uploadedKeys' => $uploadedKeys,
                    'uploaded' => $this->collectUploadedSummaries($form->fresh(), $uploadedKeys),
                ],
            ],
        ];
    }

    public function getSupplementaryDownloadDescriptor(ApplicationForm $form, string $key): ?array
    {
        $payload = $form->payload ?? [];
        $sup = $this->supplementaryMapFromPayload($payload);
        if (! isset($sup[$key]) || ! is_array($sup[$key])) {
            return null;
        }

        $entry = $sup[$key];
        $path = $entry['storedPath'] ?? null;
        if (! is_string($path) || $path === '') {
            return null;
        }

        if (! Storage::disk('local')->exists($path)) {
            return null;
        }

        $original = is_string($entry['originalName'] ?? null) ? $entry['originalName'] : basename($path);

        return ['path' => $path, 'filename' => $original];
    }

    private function resolveFormByPlainToken(string $token): ?ApplicationForm
    {
        if (! preg_match('/^[A-Za-z0-9]{64}$/', $token)) {
            return null;
        }

        $hash = hash('sha256', $token);

        return $this->applicationFormRepository->findByDocumentUploadTokenHash($hash);
    }

    private function publicSupplementarySummary(mixed $entry): mixed
    {
        if (is_array($entry) && isset($entry['originalName'], $entry['uploadedAt'])) {
            return [
                'originalName' => $entry['originalName'],
                'uploadedAt' => $entry['uploadedAt'],
            ];
        }

        return [];
    }

    /**
     * @param  list<string>  $keys
     * @return array<string, mixed>
     */
    private function collectUploadedSummaries(ApplicationForm $form, array $keys): array
    {
        $payload = $form->payload ?? [];
        $sup = $this->supplementaryMapFromPayload($payload);
        $out = [];
        foreach ($keys as $key) {
            if (isset($sup[$key])) {
                $out[$key] = $this->publicSupplementarySummary($sup[$key]);
            }
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function supplementaryMapFromPayload(array $payload): array
    {
        if (is_array($payload['supplementaryFiles'] ?? null)) {
            return $payload['supplementaryFiles'];
        }

        return is_array($payload['supplementary_files'] ?? null) ? $payload['supplementary_files'] : [];
    }
}
