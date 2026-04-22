<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\FeedbackServiceInterface;
use App\DTO\Feedback\StoreFeedbackDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Feedback\DestroyFeedbackRequest;
use App\Http\Requests\Feedback\IndexFeedbackManageRequest;
use App\Http\Requests\Feedback\ReplyFeedbackRequest;
use App\Http\Requests\Feedback\StoreFeedbackRequest;
use App\Http\Resources\FeedbackCollection;
use App\Http\Resources\FeedbackResource;
use App\Models\FeedbackMessage;
use Illuminate\Http\JsonResponse;

class FeedbackController extends Controller
{
    public function __construct(
        private readonly FeedbackServiceInterface $feedbackService,
    ) {}

    public function store(StoreFeedbackRequest $request): FeedbackResource
    {
        $message = $this->feedbackService->store(new StoreFeedbackDto($request->validated()), $request->ip());

        return new FeedbackResource($message);
    }

    public function manageIndex(IndexFeedbackManageRequest $request): FeedbackCollection
    {
        $dto = $request->toPaginatedTableDto();

        return new FeedbackCollection($this->feedbackService->paginateManage($dto->perPage, $dto->page, $dto->filters));
    }

    public function show(FeedbackMessage $feedback): FeedbackResource
    {
        $this->authorize('view', $feedback);

        return new FeedbackResource($this->feedbackService->markReadIfUnread($feedback));
    }

    public function reply(ReplyFeedbackRequest $request, FeedbackMessage $feedback): FeedbackResource
    {
        $files = $request->file('attachments', []);
        if (! is_array($files)) {
            $files = $files ? [$files] : [];
        }

        $message = $this->feedbackService->sendReply(
            $feedback,
            $request->validated('body'),
            $files,
            $request->user(),
        );

        return new FeedbackResource($message);
    }

    public function destroy(DestroyFeedbackRequest $request, FeedbackMessage $feedback): JsonResponse
    {
        $this->feedbackService->delete($feedback);

        return response()->json(null, 204);
    }
}
