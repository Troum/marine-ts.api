<?php

namespace App\Providers;

use App\Contracts\Repositories\ApplicationFormRepositoryInterface;
use App\Contracts\Repositories\ContentPageRepositoryInterface;
use App\Contracts\Repositories\GalleryItemRepositoryInterface;
use App\Contracts\Repositories\NewsRepositoryInterface;
use App\Contracts\Repositories\ProjectRepositoryInterface;
use App\Contracts\Repositories\ServiceRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Repositories\VacancyRepositoryInterface;
use App\Contracts\Services\AdminUserServiceInterface;
use App\Contracts\Services\AnalyticsServiceInterface;
use App\Contracts\Services\ApplicationFormServiceInterface;
use App\Contracts\Services\ApplicationFormSupplementaryDocumentServiceInterface;
use App\Contracts\Services\AuthServiceInterface;
use App\Contracts\Services\ContentPageServiceInterface;
use App\Contracts\Services\FeedbackServiceInterface;
use App\Contracts\Services\PageInquiryServiceInterface;
use App\Contracts\Services\GalleryItemServiceInterface;
use App\Contracts\Services\NewsServiceInterface;
use App\Contracts\Services\ProjectServiceInterface;
use App\Contracts\Services\ServiceServiceInterface;
use App\Contracts\Services\SiteSeoServiceInterface;
use App\Contracts\Services\StatsServiceInterface;
use App\Contracts\Services\VacancyServiceInterface;
use App\Models\User;
use App\Repositories\ApplicationFormRepository;
use App\Repositories\ContentPageRepository;
use App\Repositories\GalleryItemRepository;
use App\Repositories\NewsRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\UserRepository;
use App\Repositories\VacancyRepository;
use App\Services\AdminUserService;
use App\Services\AnalyticsService;
use App\Services\ApplicationFormService;
use App\Services\ApplicationFormSupplementaryDocumentService;
use App\Services\AuthService;
use App\Services\ContentPageService;
use App\Services\FeedbackService;
use App\Services\PageInquiryService;
use App\Services\GalleryItemService;
use App\Services\NewsService;
use App\Services\ProjectService;
use App\Services\ServiceService;
use App\Services\SiteSeoService;
use App\Services\StatsService;
use App\Services\VacancyService;
use Illuminate\Support\Facades\Gate;
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
    }

    public function boot(): void
    {
        Gate::before(function ($user, string $ability) {
            if ($user instanceof User && $user->hasRole('admin')) {
                return true;
            }

            return null;
        });
    }
}
