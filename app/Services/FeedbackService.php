<?php

namespace App\Services;

use App\Contracts\Services\FeedbackServiceInterface;
use App\Models\FeedbackMessage;
use App\Support\AdminListQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class FeedbackService implements FeedbackServiceInterface
{
    public function store(array $data, ?string $ip): FeedbackMessage
    {
        /** @var FeedbackMessage */
        return FeedbackMessage::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'message' => $data['message'],
            'ip' => $ip,
        ]);
    }

    public function paginateManage(int $perPage, int $page, array $filters): LengthAwarePaginator
    {
        $query = FeedbackMessage::query();

        if (! empty($filters['search']) && is_string($filters['search'])) {
            $p = AdminListQuery::likePattern($filters['search']);
            $query->where(function ($q) use ($p): void {
                $q->where('name', 'like', $p)
                    ->orWhere('email', 'like', $p)
                    ->orWhere('message', 'like', $p);
            });
        }

        $read = $filters['read'] ?? null;
        if ($read === true) {
            $query->whereNotNull('read_at');
        } elseif ($read === false) {
            $query->whereNull('read_at');
        }

        $col = $filters['order_column'] ?? 'id';
        $dir = $filters['order_direction'] ?? 'desc';
        if (! is_string($col) || ! in_array($col, ['id', 'created_at', 'updated_at', 'read_at'], true)) {
            $col = 'id';
        }
        $dir = is_string($dir) && in_array(strtolower($dir), ['asc', 'desc'], true) ? strtolower($dir) : 'desc';
        $query->orderBy($col, $dir);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function markReadIfUnread(FeedbackMessage $feedback): FeedbackMessage
    {
        if ($feedback->read_at === null) {
            $feedback->update(['read_at' => now()]);
        }

        /** @var FeedbackMessage */
        return $feedback->fresh();
    }

    public function delete(FeedbackMessage $feedback): void
    {
        $feedback->delete();
    }
}
