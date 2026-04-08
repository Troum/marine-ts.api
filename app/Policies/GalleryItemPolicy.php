<?php

namespace App\Policies;

use App\Models\GalleryItem;
use App\Models\User;

class GalleryItemPolicy
{
    public function create(User $user): bool
    {
        return $user->can('manage gallery');
    }

    public function update(User $user, GalleryItem $galleryItem): bool
    {
        return $user->can('manage gallery');
    }

    public function delete(User $user, GalleryItem $galleryItem): bool
    {
        return $user->can('manage gallery');
    }
}
