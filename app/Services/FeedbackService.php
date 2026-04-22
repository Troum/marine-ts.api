<?php

namespace App\Services;

use App\Contracts\Repositories\FeedbackMessageRepositoryInterface;
use App\Contracts\Services\FeedbackServiceInterface;
use App\DTO\Feedback\StoreFeedbackDto;
use App\Mail\FeedbackReplyMail;
use App\Models\FeedbackMessage;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

final class FeedbackService implements FeedbackServiceInterface
{
    public function __construct(
        private readonly FeedbackMessageRepositoryInterface $feedbackMessageRepository,
    ) {}

    public function store(StoreFeedbackDto $dto, ?string $ip): FeedbackMessage
    {
        /** @var FeedbackMessage */
        return $this->feedbackMessageRepository->createOne([
            'name' => $dto->name,
            'email' => $dto->email,
            'phone' => $dto->phone,
            'message' => $dto->message,
            'ip' => $ip,
        ]);
    }

    public function paginateManage(int $perPage, int $page, array $filters): LengthAwarePaginator
    {
        return $this->feedbackMessageRepository->paginateManageList($perPage, $page, $filters);
    }

    public function markReadIfUnread(FeedbackMessage $feedback): FeedbackMessage
    {
        if ($feedback->read_at === null) {
            $this->feedbackMessageRepository->updateOne($feedback, ['read_at' => now()]);
        }

        /** @var FeedbackMessage */
        return $this->feedbackMessageRepository->getOne($feedback->id);
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

            $this->feedbackMessageRepository->updateOne($feedback, ['replied_at' => now()]);

            /** @var FeedbackMessage */
            return $this->feedbackMessageRepository->getOne($feedback->id);
        } finally {
            foreach ($storedPaths as $relative) {
                $disk->delete($relative);
            }
        }
    }

    public function delete(FeedbackMessage $feedback): void
    {
        $this->feedbackMessageRepository->deleteOne($feedback);
    }
}
