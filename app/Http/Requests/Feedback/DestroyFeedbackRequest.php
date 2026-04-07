<?php

namespace App\Http\Requests\Feedback;

use App\Models\FeedbackMessage;
use Illuminate\Foundation\Http\FormRequest;

class DestroyFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var FeedbackMessage $feedback */
        $feedback = $this->route('feedback');

        return $this->user()->can('delete', $feedback);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
