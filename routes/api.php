<?php

use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\ApplicationFormController;
use App\Http\Controllers\Api\ApplicationFormDocumentUploadController;
use App\Http\Controllers\Api\ApplicationFormSupplementaryDownloadController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContentPageController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\GalleryItemController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SiteSeoController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\VacancyController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);

Route::post('/analytics/page-view', [AnalyticsController::class, 'storePageView'])
    ->middleware('throttle:120,1');

Route::get('/news', [NewsController::class, 'index']);
Route::get('/news/{slug}', [NewsController::class, 'showBySlug'])->where('slug', '[a-z0-9\-]+');

Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/projects/{project}', [ProjectController::class, 'show']);

Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{service}', [ServiceController::class, 'show']);

Route::get('/vacancies', [VacancyController::class, 'index']);

Route::get('/stats', StatsController::class);

Route::post('/feedback', [FeedbackController::class, 'store'])->middleware('throttle:10,1');

Route::post('/vacancies/{slug}/application-forms', [ApplicationFormController::class, 'store'])
    ->where('slug', '[a-z0-9\-]+')
    ->middleware('throttle:10,1');

Route::get('/application-forms/document-upload/{token}', [ApplicationFormDocumentUploadController::class, 'show'])
    ->where('token', '[A-Za-z0-9]{64}')
    ->middleware('throttle:30,1');

Route::post('/application-forms/document-upload/{token}', [ApplicationFormDocumentUploadController::class, 'store'])
    ->where('token', '[A-Za-z0-9]{64}')
    ->middleware('throttle:20,1');

Route::get('/seo/pages', [SiteSeoController::class, 'index']);
Route::get('/seo/pages/{slug}', [SiteSeoController::class, 'show'])->where('slug', '[a-z0-9-]+');

Route::get('/gallery', [GalleryItemController::class, 'index']);

Route::get('/content-pages', [ContentPageController::class, 'publicIndex']);
/** Не совпадать с сегментом `manage` (иначе перехватит GET /content-pages/manage). */
Route::get('/content-pages/{slug}', [ContentPageController::class, 'publicShow'])->where('slug', '(?!manage$)[a-z0-9\-]+');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users/manage', [AdminUserController::class, 'index']);
    Route::get('/users/manage/roles', [AdminUserController::class, 'rolesCatalog']);
    Route::get('/users/manage/{user}', [AdminUserController::class, 'show'])->whereNumber('user');
    Route::post('/users', [AdminUserController::class, 'store']);
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->whereNumber('user');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->whereNumber('user');

    Route::get('/vacancies/manage', [VacancyController::class, 'manageIndex']);
    Route::get('/application-forms/manage', [ApplicationFormController::class, 'manageIndexAll']);
    Route::get('/application-forms/document-request-catalog', [ApplicationFormController::class, 'documentRequestCatalog']);
    Route::get('/vacancies/manage/{vacancy}/application-forms', [ApplicationFormController::class, 'manageIndex'])->whereNumber('vacancy');
    Route::get('/application-forms/{application_form}', [ApplicationFormController::class, 'show'])->whereNumber('application_form');
    Route::get('/application-forms/{application_form}/pdf', [ApplicationFormController::class, 'downloadPdf'])->whereNumber('application_form');
    Route::get('/application-forms/{application_form}/supplementary-files/{key}', ApplicationFormSupplementaryDownloadController::class)
        ->whereNumber('application_form')
        ->where('key', '[a-zA-Z0-9:_\-\.]+');
    Route::patch('/application-forms/{application_form}', [ApplicationFormController::class, 'updateStatus'])->whereNumber('application_form');
    Route::post('/application-forms/{application_form}/request-documents', [ApplicationFormController::class, 'requestDocuments'])->whereNumber('application_form');
    Route::get('/vacancies/manage/{vacancy}', [VacancyController::class, 'show'])->whereNumber('vacancy');
    Route::post('/vacancies', [VacancyController::class, 'store']);
    Route::put('/vacancies/{vacancy}', [VacancyController::class, 'update']);
    Route::delete('/vacancies/{vacancy}', [VacancyController::class, 'destroy']);

    Route::get('/news/manage/{news}', [NewsController::class, 'show'])->whereNumber('news');
    Route::post('/news', [NewsController::class, 'store']);
    Route::put('/news/{news}', [NewsController::class, 'update']);
    Route::delete('/news/{news}', [NewsController::class, 'destroy']);

    Route::post('/projects', [ProjectController::class, 'store']);
    Route::put('/projects/{project}', [ProjectController::class, 'update']);
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);

    Route::post('/services', [ServiceController::class, 'store']);
    Route::put('/services/{service}', [ServiceController::class, 'update']);
    Route::delete('/services/{service}', [ServiceController::class, 'destroy']);

    Route::put('/seo/pages/{slug}', [SiteSeoController::class, 'update'])->where('slug', '[a-z0-9-]+');

    Route::get('/analytics/manage/summary', [AnalyticsController::class, 'manageSummary']);

    Route::get('/feedback/manage', [FeedbackController::class, 'manageIndex']);
    Route::get('/feedback/manage/{feedback}', [FeedbackController::class, 'show'])->whereNumber('feedback');
    Route::post('/feedback/manage/{feedback}/reply', [FeedbackController::class, 'reply'])
        ->whereNumber('feedback')
        ->middleware('throttle:20,1');
    Route::delete('/feedback/{feedback}', [FeedbackController::class, 'destroy'])->whereNumber('feedback');

    Route::get('/content-pages/manage', [ContentPageController::class, 'manageIndex']);
    Route::get('/content-pages/manage/{content_page}', [ContentPageController::class, 'showManage'])->whereNumber('content_page');
    Route::post('/content-pages', [ContentPageController::class, 'store']);
    Route::put('/content-pages/{content_page}', [ContentPageController::class, 'update'])->whereNumber('content_page');
    Route::delete('/content-pages/{content_page}', [ContentPageController::class, 'destroy'])->whereNumber('content_page');

    Route::post('/gallery', [GalleryItemController::class, 'store']);
    Route::put('/gallery/{gallery_item}', [GalleryItemController::class, 'update'])->whereNumber('gallery_item');
    Route::post('/gallery/{gallery_item}/image', [GalleryItemController::class, 'replaceImage'])->whereNumber('gallery_item');
    Route::delete('/gallery/{gallery_item}', [GalleryItemController::class, 'destroy'])->whereNumber('gallery_item');
});

Route::get('/vacancies/{slug}', [VacancyController::class, 'showBySlug'])->where('slug', '[a-z0-9\-]+');
