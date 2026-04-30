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
        return $this->canManagePage($user, $contentPage);
    }

    public function create(User $user): bool
    {
        return $user->can('manage content pages');
    }

    public function update(User $user, ContentPage $contentPage): bool
    {
        return $this->canManagePage($user, $contentPage);
    }

    public function delete(User $user, ContentPage $contentPage): bool
    {
        return $this->canManagePage($user, $contentPage);
    }

    private function canManagePage(User $user, ContentPage $contentPage): bool
    {
        if (! $user->can('manage content pages')) {
            return false;
        }

        if ($contentPage->slug === 'vacancies-page' && $user->hasRole('content_manager')) {
            return false;
        }

        return true;
    }
}
