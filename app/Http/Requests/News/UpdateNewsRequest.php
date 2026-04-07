<?php

namespace App\Http\Requests\News;

use App\Models\News;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var News $news */
        $news = $this->route('news');

        return $this->user()->can('update', $news);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var News $news */
        $news = $this->route('news');

        return [
            'title' => ['sometimes', 'string', 'max:500'],
            'slug' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('news', 'slug')->ignore($news->id)],
            'excerpt' => ['sometimes', 'string'],
            'content' => ['nullable', 'string'],
            'date' => ['sometimes', 'string', 'max:255'],
            'author' => ['sometimes', 'string', 'max:255'],
            'category' => ['sometimes', 'string', 'max:255'],
            'featured' => ['sometimes', 'boolean'],
            'image' => ['nullable', 'string', 'max:500'],
            'seoTitle' => ['sometimes', 'nullable', 'string', 'max:255'],
            'seoDescription' => ['sometimes', 'nullable', 'string', 'max:8000'],
            'seoKeywords' => ['sometimes', 'nullable', 'string', 'max:500'],
        ];
    }
}
