<?php

namespace App\Contracts\Repositories;

interface VacancyRepositoryInterface extends BaseRepositoryInterface
{
    public function countPublished(): int;
}
