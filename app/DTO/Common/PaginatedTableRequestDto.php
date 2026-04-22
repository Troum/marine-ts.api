<?php

namespace App\DTO\Common;

/**
 * @param  array<string, mixed>  $filters
 */
final readonly class PaginatedTableRequestDto
{
    public function __construct(
        public int $perPage,
        public int $page,
        public array $filters,
    ) {}
}
