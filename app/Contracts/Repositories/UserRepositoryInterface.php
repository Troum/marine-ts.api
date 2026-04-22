<?php

namespace App\Contracts\Repositories;

use App\Models\User;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function findByUsernameForAuth(string $username): ?User;

    public function revokeAllApiTokens(User $user): void;

    public function createApiToken(User $user, string $name): string;
}
