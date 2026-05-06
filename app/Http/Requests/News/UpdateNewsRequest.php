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

    protected function prepareForValidation(): void
    {
        $default = (string) config('marine.default_locale');
        if (! $this->has('translations') && $this->has('title')) {
            $this->merge([
                'translations' => [
                    $default => [
                        'title' => $this->input('title'),
                        'excerpt' => $this->input('excerpt'),
                        'content' => $this->input('content'),
                        'category' => $this->input('category'),
                        'seoTitle' => $this->input('seoTitle') ?? $this->input('seo_title'),
                        'seoDescription' => $this->input('seoDescription') ?? $this->input('seo_description'),
                        'seoKeywords' => $this->input('seoKeywords') ?? $this->input('seo_keywords'),
                        'seoImage' => $this->input('seoImage') ?? $this->input('seo_image'),
                    ],
                ],
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var News $news */
        $news = $this->route('news');

        $locales = config('marine.locales');
        if (! is_array($locales)) {
            $locales = ['ru', 'en'];
        }
        $default = (string) config('marine.default_locale');

        $rules = [
            'slug' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('news', 'slug')->ignore($news->id)],
            'date' => ['sometimes', 'string', 'max:255'],
            'author' => ['sometimes', 'string', 'max:255'],
            'featured' => ['sometimes', 'boolean'],
            'image' => ['nullable', 'string', 'max:500'],
            'translations' => ['sometimes', 'array'],
        ];

        foreach ($locales as $loc) {
            $isDefault = $loc === $default;
            $prefix = "translations.$loc";
            $rules[$prefix] = ['sometimes', 'nullable', 'array'];
            $rules["$prefix.title"] = ['sometimes', 'nullable', 'string', 'max:500'];
            $rules["$prefix.excerpt"] = ['sometimes', 'nullable', 'string'];
            $rules["$prefix.content"] = ['nullable', 'string'];
            $rules["$prefix.category"] = ['sometimes', 'nullable', 'string', 'max:255'];
            $rules["$prefix.seoTitle"] = ['sometimes', 'nullable', 'string', 'max:255'];
            $rules["$prefix.seoDescription"] = ['sometimes', 'nullable', 'string', 'max:8000'];
            $rules["$prefix.seoKeywords"] = ['sometimes', 'nullable', 'string', 'max:500'];
            $rules["$prefix.seoImage"] = ['sometimes', 'nullable', 'string', 'max:2000'];
        }

        return $rules;
    }
}
