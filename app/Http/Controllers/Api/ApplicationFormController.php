<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\ApplicationFormServiceInterface;
use App\DTO\ApplicationForm\StoreApplicationFormDto;
use App\DTO\ApplicationForm\StoreOpenApplicationFormDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApplicationForm\IndexApplicationFormsForVacancyRequest;
use App\Http\Requests\ApplicationForm\IndexApplicationFormsManageRequest;
use App\Http\Requests\ApplicationForm\RequestDocumentsRequest;
use App\Http\Requests\ApplicationForm\StoreApplicationFormRequest;
use App\Http\Requests\ApplicationForm\StoreOpenApplicationFormRequest;
use App\Http\Requests\ApplicationForm\UpdateApplicationFormStatusRequest;
use App\Http\Resources\ApplicationFormCollection;
use App\Http\Resources\ApplicationFormResource;
use App\Http\Resources\RequestedDocumentCatalogEntryResource;
use App\Models\ApplicationForm;
use App\Models\Vacancy;
use App\Support\RequestedDocumentCatalog;
use Illuminate\Http\JsonResponse;
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
            'payload' => $request->validated(),
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

    public function manageIndex(IndexApplicationFormsForVacancyRequest $request, Vacancy $vacancy): ApplicationFormCollection
    {
        $dto = $request->toPaginatedTableDto();

        return new ApplicationFormCollection(
            $this->applicationFormService->paginateForVacancy($vacancy, $dto->perPage, $dto->page, $dto->filters)
        );
    }

    public function manageIndexAll(IndexApplicationFormsManageRequest $request): ApplicationFormCollection
    {
        $dto = $request->toPaginatedTableDto();

        return new ApplicationFormCollection(
            $this->applicationFormService->paginateAll($dto->perPage, $dto->page, $dto->filters)
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
