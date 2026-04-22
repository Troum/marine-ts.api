<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\AdminUserServiceInterface;
use App\DTO\AdminUser\StoreAdminUserDto;
use App\DTO\AdminUser\UpdateAdminUserDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUser\DestroyAdminUserRequest;
use App\Http\Requests\AdminUser\IndexAdminUsersRequest;
use App\Http\Requests\AdminUser\RolesCatalogRequest;
use App\Http\Requests\AdminUser\ShowAdminUserRequest;
use App\Http\Requests\AdminUser\StoreAdminUserRequest;
use App\Http\Requests\AdminUser\UpdateAdminUserRequest;
use App\Http\Resources\AdminUserCollection;
use App\Http\Resources\AdminUserResource;
use App\Http\Resources\RoleCatalogEntryResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Mycro\Core\Exceptions\DtoHydrationException;
use Mycro\Core\Exceptions\ReadonlyPropertyUpdateException;

class AdminUserController extends Controller
{
    public function __construct(
        private readonly AdminUserServiceInterface $adminUserService,
    ) {}

    public function index(IndexAdminUsersRequest $request): AdminUserCollection
    {
        $dto = $request->toPaginatedTableDto();

        return new AdminUserCollection($this->adminUserService->paginate($dto->perPage, $dto->page, $dto->filters));
    }

    public function rolesCatalog(RolesCatalogRequest $request): AnonymousResourceCollection
    {
        return RoleCatalogEntryResource::collection($this->adminUserService->rolesCatalog());
    }

    public function show(ShowAdminUserRequest $request, User $user): AdminUserResource
    {
        $user->load('roles');

        return new AdminUserResource($user);
    }

    /**
     * @throws ReadonlyPropertyUpdateException
     * @throws DtoHydrationException
     */
    public function store(StoreAdminUserRequest $request)
    {
        $validated = $request->validated();
        if (! array_key_exists('roles', $validated)) {
            $validated['roles'] = [];
        }
        $dto = new StoreAdminUserDto($validated);
        $user = $this->adminUserService->create($dto);

        return (new AdminUserResource($user))->response()->setStatusCode(201);
    }

    /**
     * @throws ReadonlyPropertyUpdateException
     * @throws DtoHydrationException
     */
    public function update(UpdateAdminUserRequest $request, User $user): AdminUserResource
    {
        $validated = $request->validated();
        $dto = new UpdateAdminUserDto($validated);
        $syncRoles = array_key_exists('roles', $validated);
        $user = $this->adminUserService->update($user, $dto, $syncRoles);

        return new AdminUserResource($user);
    }

    public function destroy(DestroyAdminUserRequest $request, User $user)
    {
        $this->adminUserService->delete($user, $request->user());

        return response()->json(null, 204);
    }
}
