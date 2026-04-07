<?php

namespace App\DTO\AdminUser;

use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class UpdateAdminUserDto extends BaseDto
{
    #[MapProperty(from: ['name'], required: false)]
    public readonly ?string $name;

    #[MapProperty(from: ['username'], required: false)]
    public readonly ?string $username;

    #[MapProperty(from: ['email'], required: false)]
    public readonly ?string $email;

    #[MapProperty(from: ['password'], required: false)]
    public readonly ?string $password;

    /**
     * @var list<string>|null
     */
    #[MapProperty(from: ['roles'], required: false)]
    public readonly ?array $roles;
}
