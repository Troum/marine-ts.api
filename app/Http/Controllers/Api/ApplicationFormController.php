<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\ApplicationFormServiceInterface;
use App\DTO\ApplicationForm\StoreApplicationFormDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApplicationForm\RequestDocumentsRequest;
use App\Http\Requests\ApplicationForm\StoreApplicationFormRequest;
use App\Http\Requests\ApplicationForm\UpdateApplicationFormStatusRequest;
use App\Support\RequestedDocumentCatalog;
use App\Http\Resources\ApplicationFormCollection;
use App\Http\Resources\ApplicationFormResource;
use App\Models\ApplicationForm;
use App\Models\Vacancy;
use App\Support\AdminListQuery;
use App\Support\ApplicationFormPdfTemplateData;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mycro\Core\Exceptions\DtoHydrationException;
use Mycro\Core\Exceptions\ReadonlyPropertyUpdateException;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\LaravelPdf\PdfBuilder;

class ApplicationFormController extends Controller
{
    public function __construct(
        private readonly ApplicationFormServiceInterface $applicationFormService,
    ) {}

    /**
     * @throws ReadonlyPropertyUpdateException
     * @throws DtoHydrationException
     */
    public function store(StoreApplicationFormRequest $request, string $slug): JsonResponse
    {
        $dto = new StoreApplicationFormDto([
            'slug' => $slug,
            'payload' => $request->all(),
        ]);

        $applicationForm = $this->applicationFormService->storeForPublishedVacancy($dto);

        return new ApplicationFormResource($applicationForm->fresh())
            ->response()
            ->setStatusCode(201);
    }

    public function manageIndex(Request $request, Vacancy $vacancy): ApplicationFormCollection
    {
        $this->authorize('viewAny', ApplicationForm::class);

        $perPage = min(max((int) $request->query('per_page', 100), 1), 500);
        $page = max(1, (int) $request->query('page', 1));

        $filters = array_merge(
            AdminListQuery::sortOrder($request, ['id', 'created_at', 'updated_at', 'full_name', 'email', 'status'], 'id', 'desc'),
            array_filter([
                'search' => AdminListQuery::search($request),
                'status' => AdminListQuery::statusFilter($request),
            ])
        );

        return new ApplicationFormCollection(
            $this->applicationFormService->paginateForVacancy($vacancy, $perPage, $page, $filters)
        );
    }

    public function manageIndexAll(Request $request): ApplicationFormCollection
    {
        $this->authorize('viewAny', ApplicationForm::class);

        $perPage = min(max((int) $request->query('per_page', 50), 1), 500);
        $page = max(1, (int) $request->query('page', 1));

        $filters = array_merge(
            AdminListQuery::sortOrder($request, ['id', 'created_at', 'updated_at', 'full_name', 'email', 'status', 'vacancy_id'], 'id', 'desc'),
            array_filter([
                'search' => AdminListQuery::search($request),
                'status' => AdminListQuery::statusFilter($request),
            ])
        );

        return new ApplicationFormCollection(
            $this->applicationFormService->paginateAll($perPage, $page, $filters)
        );
    }

    public function show(ApplicationForm $application_form): ApplicationFormResource
    {
        $this->authorize('view', $application_form);

        return new ApplicationFormResource($application_form->load('vacancy'));
    }

    /**
     * PDF с данными анкеты (тот же шаблон, что уходит в письмо crewing).
     */
    public function downloadPdf(ApplicationForm $application_form): Pdf|PdfBuilder
    {
        $this->authorize('view', $application_form);

        $slug = $application_form->vacancy?->slug
            ? Str::slug($application_form->vacancy->slug)
            : 'vacancy';
        $filename = 'anketa-'.$application_form->id.'-'.$slug.'.pdf';

        return Pdf::view('pdf.application-form', ApplicationFormPdfTemplateData::make($application_form))
            ->name($filename)
            ->download();
    }

    public function updateStatus(
        UpdateApplicationFormStatusRequest $request,
        ApplicationForm $application_form,
    ): ApplicationFormResource {
        $applicationForm = $this->applicationFormService->updateStatus(
            $application_form,
            $request->validated('status'),
        );

        return new ApplicationFormResource($applicationForm->load('vacancy'));
    }

    public function documentRequestCatalog(): JsonResponse
    {
        $this->authorize('viewAny', ApplicationForm::class);

        return response()->json([
            'data' => RequestedDocumentCatalog::entries(),
        ]);
    }

    public function requestDocuments(
        RequestDocumentsRequest $request,
        ApplicationForm $application_form,
    ): ApplicationFormResource {
        $applicationForm = $this->applicationFormService->requestDocuments(
            $application_form,
            $request->validated('document_keys'),
        );

        return new ApplicationFormResource($applicationForm->load('vacancy'));
    }
}
