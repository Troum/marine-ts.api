<?php

namespace App\Policies;

use App\Models\PageInquiry;
use App\Models\User;

class PageInquiryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage page inquiries');
    }

    public function view(User $user, PageInquiry $pageInquiry): bool
    {
        return $user->can('manage page inquiries');
    }

    public function delete(User $user, PageInquiry $pageInquiry): bool
    {
        return $user->can('manage page inquiries');
    }
}
