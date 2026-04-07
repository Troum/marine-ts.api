<?php

namespace App\Http\Requests\Feedback;

use App\Models\FeedbackMessage;
use Illuminate\Foundation\Http\FormRequest;

class ReplyFeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        $feedback = $this->route('feedback');

        return $feedback instanceof FeedbackMessage && $this->user()->can('reply', $feedback);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:50000'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => [
                'file',
                'max:10240',
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,odt,ods,txt,png,jpg,jpeg,gif,webp,zip',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'body.required' => 'Введите текст ответа.',
            'attachments.max' => 'Не более 5 файлов.',
            'attachments.*.max' => 'Каждый файл не больше 10 МБ.',
            'attachments.*.mimes' => 'Недопустимый тип файла.',
        ];
    }
}
