<?php

namespace App\Http\Requests\News;

use App\Models\News;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', News::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:500'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('news', 'slug')],
            'excerpt' => ['required', 'string'],
            'content' => ['nullable', 'string'],
            'date' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'featured' => ['sometimes', 'boolean'],
            'image' => ['nullable', 'string', 'max:500'],
            'seoTitle' => ['nullable', 'string', 'max:255'],
            'seoDescription' => ['nullable', 'string', 'max:8000'],
            'seoKeywords' => ['nullable', 'string', 'max:500'],
        ];
    }
}
