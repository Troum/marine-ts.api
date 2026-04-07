<?php

namespace App\Policies;

use App\Models\SiteSeoPage;
use App\Models\User;

class SiteSeoPagePolicy
{
    public function update(User $user, SiteSeoPage $page): bool
    {
        return $user->can('manage seo');
    }
}
