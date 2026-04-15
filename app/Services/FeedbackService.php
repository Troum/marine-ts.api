<?php

namespace App\Services;

use App\Contracts\Services\FeedbackServiceInterface;
use App\DTO\Feedback\StoreFeedbackDto;
use App\Mail\FeedbackReplyMail;
use App\Models\FeedbackMessage;
use App\Models\User;
use App\Support\AdminListQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

final class FeedbackService implements FeedbackServiceInterface
{
    public function store(StoreFeedbackDto $dto, ?string $ip): FeedbackMessage
    {
        /** @var FeedbackMessage */
        return FeedbackMessage::query()->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'phone' => $dto->phone,
            'message' => $dto->message,
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

    public function sendReply(FeedbackMessage $feedback, string $body, array $uploadedFiles, User $sender): FeedbackMessage
    {
        $disk = Storage::disk('local');
        $storedPaths = [];
        $descriptors = [];

        try {
            foreach ($uploadedFiles as $file) {
                if (! $file instanceof UploadedFile) {
                    continue;
                }
                $relative = $file->store('feedback-reply-temp', 'local');
                $storedPaths[] = $relative;
                $descriptors[] = [
                    'path' => $disk->path($relative),
                    'name' => $file->getClientOriginalName(),
                    'mime' => $file->getMimeType() ?: 'application/octet-stream',
                ];
            }

            Mail::to($feedback->email)->send(
                new FeedbackReplyMail($feedback, $body, $sender, $descriptors)
            );

            $feedback->update(['replied_at' => now()]);

            /** @var FeedbackMessage */
            return $feedback->fresh();
        } finally {
            foreach ($storedPaths as $relative) {
                $disk->delete($relative);
            }
        }
    }

    public function delete(FeedbackMessage $feedback): void
    {
        $feedback->delete();
    }
}
