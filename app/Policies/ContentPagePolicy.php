<?php

namespace App\Policies;

use App\Models\ContentPage;
use App\Models\User;

class ContentPagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage content pages');
    }

    public function view(User $user, ContentPage $contentPage): bool
    {
        return $user->can('manage content pages');
    }

    public function create(User $user): bool
    {
        return $user->can('manage content pages');
    }

    public function update(User $user, ContentPage $contentPage): bool
    {
        return $user->can('manage content pages');
    }

    public function delete(User $user, ContentPage $contentPage): bool
    {
        return $user->can('manage content pages');
    }
}
