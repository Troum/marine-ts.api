<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\AuthServiceInterface;
use App\DTO\Auth\LoginDto;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;

final class AuthService implements AuthServiceInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    /**
     * @return array{token: string, user: array<string, mixed>}
     * @throws AuthenticationException
     */
    public function login(LoginDto $dto): array
    {
        $user = $this->userRepository->findByUsername($dto->username);

        if (! $user || ! Hash::check($dto->password, $user->password)) {
            throw new AuthenticationException(__('auth.failed'));
        }

        $user->tokens()->delete();

        $token = $user->createToken('api')->plainTextToken;

        $primaryRole = $user->roles->first();

        return [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'role' => $primaryRole?->name,
                'roles' => $user->getRoleNames()->values()->all(),
                'permissions' => $user->getAllPermissions()->pluck('name')->values()->all(),
            ],
        ];
    }
}
