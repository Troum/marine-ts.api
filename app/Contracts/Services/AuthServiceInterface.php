<?php

namespace App\Contracts\Services;

use App\DTO\Auth\LoginDto;

interface AuthServiceInterface
{
    /**
     * @return array{token: string, user: array<string, mixed>}
     */
    public function login(LoginDto $dto): array;
}
