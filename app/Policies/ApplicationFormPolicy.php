<?php

namespace App\Policies;

use App\Models\ApplicationForm;
use App\Models\User;

class ApplicationFormPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage vacancies');
    }

    public function view(User $user, ApplicationForm $applicationForm): bool
    {
        return $user->can('manage vacancies');
    }

    public function update(User $user, ApplicationForm $applicationForm): bool
    {
        return $user->can('manage vacancies');
    }
}
