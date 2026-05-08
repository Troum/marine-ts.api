<?php

namespace App\DTO\ApplicationForm;

use Mycro\Core\Attributes\MapProperty;
use Mycro\Core\Contracts\BaseDto;

final class StoreOpenApplicationFormDto extends BaseDto
{
    /**
     * Полное тело анкеты (JSON), как пришло с клиента. Хранится «как есть»,
     * чтобы PDF / письмо crewing смогли отрисовать все поля шаблона.
     * Актуальные скалярные поля включают `expectedMonthlySalary`; устаревшие
     * `fathersName`, `homePhone`, `messenger` из запроса удаляются в FormRequest.
     *
     * @var array<string, mixed>
     */
    #[MapProperty(from: ['payload'], required: true)]
    public readonly array $payload;
}
