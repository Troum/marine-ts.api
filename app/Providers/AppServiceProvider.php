<?php

namespace App\Providers;

use App\Contracts\Repositories\ApplicationFormRepositoryInterface;
use App\Contracts\Repositories\ContentPageRepositoryInterface;
use App\Contracts\Repositories\FeedbackMessageRepositoryInterface;
use App\Contracts\Repositories\GalleryItemRepositoryInterface;
use App\Contracts\Repositories\NewsRepositoryInterface;
use App\Contracts\Repositories\PageInquiryRepositoryInterface;
use App\Contracts\Repositories\PageViewRepositoryInterface;
use App\Contracts\Repositories\ProjectRepositoryInterface;
use App\Contracts\Repositories\PublicMediaStorageRepositoryInterface;
use App\Contracts\Repositories\RoleRepositoryInterface;
use App\Contracts\Repositories\ServiceRepositoryInterface;
use App\Contracts\Repositories\SiteSeoPageRepositoryInterface;
use App\Contracts\Repositories\SiteSettingRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Repositories\VacancyRepositoryInterface;
use App\Contracts\Services\AdminUserServiceInterface;
use App\Contracts\Services\AnalyticsServiceInterface;
use App\Contracts\Services\ApplicationFormServiceInterface;
use App\Contracts\Services\ApplicationFormSupplementaryDocumentServiceInterface;
use App\Contracts\Services\AuthServiceInterface;
use App\Contracts\Services\ContentPageServiceInterface;
use App\Contracts\Services\FeedbackServiceInterface;
use App\Contracts\Services\GalleryItemServiceInterface;
use App\Contracts\Services\MediaUploadServiceInterface;
use App\Contracts\Services\NewsServiceInterface;
use App\Contracts\Services\PageInquiryServiceInterface;
use App\Contracts\Services\ProjectServiceInterface;
use App\Contracts\Services\ServiceServiceInterface;
use App\Contracts\Services\SiteSeoServiceInterface;
use App\Contracts\Services\StatsServiceInterface;
use App\Contracts\Services\VacancyServiceInterface;
use App\Models\User;
use App\Repositories\ApplicationFormRepository;
use App\Repositories\ContentPageRepository;
use App\Repositories\FeedbackMessageRepository;
use App\Repositories\GalleryItemRepository;
use App\Repositories\NewsRepository;
use App\Repositories\PageInquiryRepository;
use App\Repositories\PageViewRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\PublicMediaStorageRepository;
use App\Repositories\RoleRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\SiteSeoPageRepository;
use App\Repositories\SiteSettingRepository;
use App\Repositories\UserRepository;
use App\Repositories\VacancyRepository;
use App\Services\AdminUserService;
use App\Services\AnalyticsService;
use App\Services\ApplicationFormService;
use App\Services\ApplicationFormSupplementaryDocumentService;
use App\Services\AuthService;
use App\Services\ContentPageService;
use App\Services\FeedbackService;
use App\Services\GalleryItemService;
use App\Services\MediaUploadService;
use App\Services\NewsService;
use App\Services\PageInquiryService;
use App\Services\ProjectService;
use App\Services\ServiceService;
use App\Services\SiteSeoService;
use App\Services\StatsService;
use App\Services\VacancyService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(NewsRepositoryInterface::class, NewsRepository::class);
        $this->app->bind(ProjectRepositoryInterface::class, ProjectRepository::class);
        $this->app->bind(ServiceRepositoryInterface::class, ServiceRepository::class);
        $this->app->bind(VacancyRepositoryInterface::class, VacancyRepository::class);
        $this->app->bind(ApplicationFormRepositoryInterface::class, ApplicationFormRepository::class);
        $this->app->bind(ContentPageRepositoryInterface::class, ContentPageRepository::class);
        $this->app->bind(GalleryItemRepositoryInterface::class, GalleryItemRepository::class);
        $this->app->bind(PageInquiryRepositoryInterface::class, PageInquiryRepository::class);
        $this->app->bind(FeedbackMessageRepositoryInterface::class, FeedbackMessageRepository::class);
        $this->app->bind(PageViewRepositoryInterface::class, PageViewRepository::class);
        $this->app->bind(SiteSettingRepositoryInterface::class, SiteSettingRepository::class);
        $this->app->bind(SiteSeoPageRepositoryInterface::class, SiteSeoPageRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(PublicMediaStorageRepositoryInterface::class, PublicMediaStorageRepository::class);

        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(NewsServiceInterface::class, NewsService::class);
        $this->app->bind(ProjectServiceInterface::class, ProjectService::class);
        $this->app->bind(ServiceServiceInterface::class, ServiceService::class);
        $this->app->bind(StatsServiceInterface::class, StatsService::class);
        $this->app->bind(VacancyServiceInterface::class, VacancyService::class);
        $this->app->bind(ApplicationFormServiceInterface::class, ApplicationFormService::class);
        $this->app->bind(ApplicationFormSupplementaryDocumentServiceInterface::class, ApplicationFormSupplementaryDocumentService::class);
        $this->app->bind(FeedbackServiceInterface::class, FeedbackService::class);
        $this->app->bind(PageInquiryServiceInterface::class, PageInquiryService::class);
        $this->app->bind(SiteSeoServiceInterface::class, SiteSeoService::class);
        $this->app->bind(AdminUserServiceInterface::class, AdminUserService::class);
        $this->app->bind(ContentPageServiceInterface::class, ContentPageService::class);
        $this->app->bind(AnalyticsServiceInterface::class, AnalyticsService::class);
        $this->app->bind(GalleryItemServiceInterface::class, GalleryItemService::class);
        $this->app->bind(MediaUploadServiceInterface::class, MediaUploadService::class);
    }

    public function boot(): void
    {
        Route::bind('project', fn (string $value) => app(ProjectRepositoryInterface::class)->getOne($value));
        Route::bind('news', fn (string $value) => app(NewsRepositoryInterface::class)->getOne($value));
        Route::bind('service', fn (string $value) => app(ServiceRepositoryInterface::class)->getOne($value));
        Route::bind('vacancy', fn (string $value) => app(VacancyRepositoryInterface::class)->getOne($value));
        Route::bind('user', fn (string $value) => app(UserRepositoryInterface::class)->getOne($value));
        Route::bind('application_form', fn (string $value) => app(ApplicationFormRepositoryInterface::class)->getOne($value));
        Route::bind('content_page', fn (string $value) => app(ContentPageRepositoryInterface::class)->getOne($value));
        Route::bind('gallery_item', fn (string $value) => app(GalleryItemRepositoryInterface::class)->getOne($value));
        Route::bind('feedback', fn (string $value) => app(FeedbackMessageRepositoryInterface::class)->getOne($value));
        Route::bind('page_inquiry', fn (string $value) => app(PageInquiryRepositoryInterface::class)->getOne($value));

        Gate::before(function ($user, string $ability) {
            if ($user instanceof User && $user->hasRole('admin')) {
                return true;
            }

            return null;
        });
    }
}
