<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\AuthServiceInterface;
use App\DTO\Auth\LoginDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\LoginResource;
use Illuminate\Http\JsonResponse;
use Mycro\Core\Exceptions\DtoHydrationException;
use Mycro\Core\Exceptions\ReadonlyPropertyUpdateException;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthServiceInterface $authService,
    ) {}

    /**
     * @throws ReadonlyPropertyUpdateException
     * @throws DtoHydrationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $dto = new LoginDto($request->validated());

        return (new LoginResource($this->authService->login($dto)))->response();
    }
}
