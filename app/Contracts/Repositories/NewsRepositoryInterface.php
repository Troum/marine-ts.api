<?php

namespace App\Contracts\Repositories;

interface NewsRepositoryInterface extends BaseRepositoryInterface
{
    public function countFeatured(): int;
}
