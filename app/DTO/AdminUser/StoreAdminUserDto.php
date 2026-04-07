<?php

namespace App\DTO\AdminUser;

use Mycro\Core\Attributes\DefaultValue;
use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class StoreAdminUserDto extends BaseDto
{
    #[MapProperty(from: ['name'], required: true)]
    public readonly string $name;

    #[MapProperty(from: ['username'], required: true)]
    public readonly string $username;

    #[MapProperty(from: ['email'], required: true)]
    public readonly string $email;

    #[MapProperty(from: ['password'], required: true)]
    public readonly string $password;

    /**
     * @var list<string>
     */
    #[MapProperty(from: ['roles'], required: false)]
    #[DefaultValue([])]
    public readonly array $roles;
}
