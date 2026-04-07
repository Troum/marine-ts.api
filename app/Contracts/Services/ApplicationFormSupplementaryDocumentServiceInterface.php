<?php

namespace App\Contracts\Services;

use App\Models\ApplicationForm;
use Illuminate\Http\Request;

interface ApplicationFormSupplementaryDocumentServiceInterface
{
    /**
     * Публичная сессия загрузки по токену из письма.
     *
     * @return array{status: int, payload: array<string, mixed>}
     */
    public function publicUploadSession(string $token): array;

    /**
     * Сохранение загруженных файлов по токену.
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return array{status: int, payload: array<string, mixed>}
     */
    public function publicUploadStore(string $token, Request $request): array;

    /**
     * Путь и имя файла для скачивания админом (или null).
     *
     * @return array{path: string, filename: string}|null
     */
    public function getSupplementaryDownloadDescriptor(ApplicationForm $form, string $key): ?array;
}
