<?php

namespace App\DTO\ApplicationForm;

use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class StoreApplicationFormDto extends BaseDto
{
    #[MapProperty(from: ['slug'], required: true)]
    public readonly string $slug;

    /**
     * Полное тело анкеты (JSON), как пришло с клиента после валидации запроса.
     * Актуальные скалярные поля включают `expectedMonthlySalary`; устаревшие
     * `fathersName`, `homePhone`, `messenger` из запроса удаляются в FormRequest.
     *
     * @var array<string, mixed>
     */
    #[MapProperty(from: ['payload'], required: true)]
    public readonly array $payload;
}
