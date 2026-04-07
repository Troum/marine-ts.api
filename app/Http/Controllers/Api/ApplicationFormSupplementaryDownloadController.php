<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\ApplicationFormSupplementaryDocumentServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\ApplicationForm;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicationFormSupplementaryDownloadController extends Controller
{
    public function __construct(
        private readonly ApplicationFormSupplementaryDocumentServiceInterface $supplementaryDocumentService,
    ) {}

    /**
     * Скачивание файла, загруженного кандидатом по ссылке (local disk).
     */
    public function __invoke(ApplicationForm $application_form, string $key): StreamedResponse
    {
        $this->authorize('view', $application_form);

        $file = $this->supplementaryDocumentService->getSupplementaryDownloadDescriptor($application_form, $key);
        if ($file === null) {
            abort(404);
        }

        return Storage::disk('local')->download($file['path'], $file['filename']);
    }
}
