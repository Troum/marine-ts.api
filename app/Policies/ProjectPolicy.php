<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function create(User $user): bool
    {
        return $user->can('manage projects');
    }

    public function update(User $user, Project $project): bool
    {
        return $user->can('manage projects');
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->can('manage projects');
    }
}
