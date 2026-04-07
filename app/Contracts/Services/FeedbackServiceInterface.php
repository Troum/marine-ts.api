<?php

namespace App\Contracts\Services;

use App\Models\FeedbackMessage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface FeedbackServiceInterface
{
    /**
     * @param  array{name: string, email: string, phone?: string|null, message: string}  $data
     */
    public function store(array $data, ?string $ip): FeedbackMessage;

    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginateManage(int $perPage, int $page, array $filters): LengthAwarePaginator;

    public function markReadIfUnread(FeedbackMessage $feedback): FeedbackMessage;

    public function delete(FeedbackMessage $feedback): void;
}
