<?php

namespace App\Support;

use App\Models\ApplicationForm;

final class ApplicationFormAdminUrl
{
    public static function manageListUrl(ApplicationForm $applicationForm): string
    {
        $frontend = rtrim((string) config('app.frontend_url'), '/');

        if ($applicationForm->vacancy_id !== null) {
            return $frontend.'/admin/vacancies/'.$applicationForm->vacancy_id.'/application-forms';
        }

        return $frontend.'/admin/application-forms';
    }
}
