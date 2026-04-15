<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\ApplicationFormServiceInterface;
use App\DTO\ApplicationForm\StoreApplicationFormDto;
use App\DTO\ApplicationForm\StoreOpenApplicationFormDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApplicationForm\RequestDocumentsRequest;
use App\Http\Requests\ApplicationForm\StoreApplicationFormRequest;
use App\Http\Requests\ApplicationForm\StoreOpenApplicationFormRequest;
use App\Http\Requests\ApplicationForm\UpdateApplicationFormStatusRequest;
use App\Http\Resources\ApplicationFormCollection;
use App\Http\Resources\ApplicationFormResource;
use App\Http\Resources\RequestedDocumentCatalogEntryResource;
use App\Models\ApplicationForm;
use App\Models\Vacancy;
use App\Support\AdminListQuery;
use App\Support\RequestedDocumentCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Mycro\Core\Exceptions\DtoHydrationException;
use Mycro\Core\Exceptions\ReadonlyPropertyUpdateException;

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

    /**
     * @throws ReadonlyPropertyUpdateException
     * @throws DtoHydrationException
     */
    public function storeOpen(StoreOpenApplicationFormRequest $request): JsonResponse
    {
        $applicationForm = $this->applicationFormService->storeOpenApplication(
            new StoreOpenApplicationFormDto($request->validated()),
        );

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

        return new ApplicationFormResource($application_form->load('vacancy.translations'));
    }

    /**
     * PDF с данными анкеты (тот же шаблон, что уходит в письмо crewing).
     */
    public function downloadPdf(ApplicationForm $application_form)
    {
        $this->authorize('view', $application_form);

        return $this->applicationFormService->pdfDownload($application_form);
    }

    public function updateStatus(
        UpdateApplicationFormStatusRequest $request,
        ApplicationForm $application_form,
    ): ApplicationFormResource {
        $applicationForm = $this->applicationFormService->updateStatus(
            $application_form,
            $request->validated('status'),
        );

        return new ApplicationFormResource($applicationForm->load('vacancy.translations'));
    }

    public function documentRequestCatalog(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', ApplicationForm::class);

        return RequestedDocumentCatalogEntryResource::collection(RequestedDocumentCatalog::entries());
    }

    public function requestDocuments(
        RequestDocumentsRequest $request,
        ApplicationForm $application_form,
    ): ApplicationFormResource {
        $applicationForm = $this->applicationFormService->requestDocuments(
            $application_form,
            $request->validated('document_keys'),
        );

        return new ApplicationFormResource($applicationForm->load('vacancy.translations'));
    }
}
