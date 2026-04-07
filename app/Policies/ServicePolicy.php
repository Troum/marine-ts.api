<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    public function create(User $user): bool
    {
        return $user->can('manage services');
    }

    public function update(User $user, Service $service): bool
    {
        return $user->can('manage services');
    }

    public function delete(User $user, Service $service): bool
    {
        return $user->can('manage services');
    }
}
