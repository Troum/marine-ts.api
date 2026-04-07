<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vacancy;

class VacancyPolicy
{
    public function create(User $user): bool
    {
        return $user->can('manage vacancies');
    }

    public function update(User $user, Vacancy $vacancy): bool
    {
        return $user->can('manage vacancies');
    }

    public function delete(User $user, Vacancy $vacancy): bool
    {
        return $user->can('manage vacancies');
    }
}
