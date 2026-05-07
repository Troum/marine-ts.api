<?php

namespace App\Listeners;

use App\Contracts\Services\ApplicationFormServiceInterface;
use App\Events\ApplicationFormSubmitted;

final class SendApplicationFormCrewingNotification
{
    public function __construct(
        private readonly ApplicationFormServiceInterface $applicationFormService,
    ) {}

    public function handle(ApplicationFormSubmitted $event): void
    {
        $this->applicationFormService->sendCrewingSubmittedNotification($event->applicationForm);
    }
}
