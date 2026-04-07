<?php

namespace App\Policies;

use App\Models\FeedbackMessage;
use App\Models\User;

class FeedbackMessagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage feedback');
    }

    public function view(User $user, FeedbackMessage $feedbackMessage): bool
    {
        return $user->can('manage feedback');
    }

    public function delete(User $user, FeedbackMessage $feedbackMessage): bool
    {
        return $user->can('manage feedback');
    }
}
