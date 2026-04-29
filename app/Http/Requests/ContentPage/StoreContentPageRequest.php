<?php

namespace App\Http\Requests\ContentPage;

use App\Models\ContentPage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreContentPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ContentPage::class);
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('is_published') && ! $this->has('isPublished')) {
            $this->merge(['isPublished' => $this->boolean('is_published')]);
        }
        if ($this->has('sort_order') && ! $this->has('sortOrder')) {
            $this->merge(['sortOrder' => $this->input('sort_order')]);
        }
        if ($this->has('contentable_type') && ! $this->has('contentableType')) {
            $this->merge(['contentableType' => $this->input('contentable_type')]);
        }
        if ($this->has('contentable_id') && ! $this->has('contentableId')) {
            $this->merge(['contentableId' => $this->input('contentable_id')]);
        }
        if ($this->has('show_inquiry_form') && ! $this->has('showInquiryForm')) {
            $this->merge(['showInquiryForm' => $this->boolean('show_inquiry_form')]);
        }
        if ($this->has('show_public_title') && ! $this->has('showPublicTitle')) {
            $this->merge(['showPublicTitle' => $this->boolean('show_public_title')]);
        }

        $default = (string) config('marine.default_locale');
        if (! $this->has('translations') && $this->has('title')) {
            $this->merge([
                'translations' => [
                    $default => [
                        'title' => $this->input('title'),
                        'excerpt' => $this->input('excerpt'),
                        'body' => $this->input('body'),
                        'seoTitle' => $this->input('seoTitle') ?? $this->input('seo_title'),
                        'seoDescription' => $this->input('seoDescription') ?? $this->input('seo_description'),
                        'seoKeywords' => $this->input('seoKeywords') ?? $this->input('seo_keywords'),
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
        $locales = config('marine.locales');
        if (! is_array($locales)) {
            $locales = ['ru', 'en'];
        }
        $default = (string) config('marine.default_locale');

        $rules = [
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('content_pages', 'slug')],
            'isPublished' => ['sometimes', 'boolean'],
            'is_published' => ['sometimes', 'boolean'],
            'sortOrder' => ['sometimes', 'integer', 'min:0', 'max:999999'],
            'sort_order' => ['sometimes', 'integer', 'min:0', 'max:999999'],
            'translations' => ['required', 'array'],
            'contentableType' => ['sometimes', 'nullable', 'string', 'in:service,project'],
            'contentableId' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'contentable_type' => ['sometimes', 'nullable', 'string', 'in:service,project'],
            'contentable_id' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'showInquiryForm' => ['sometimes', 'boolean'],
            'show_inquiry_form' => ['sometimes', 'boolean'],
            'showPublicTitle' => ['sometimes', 'boolean'],
            'show_public_title' => ['sometimes', 'boolean'],
        ];

        foreach ($locales as $loc) {
            $isDefault = $loc === $default;
            $prefix = "translations.$loc";
            $rules[$prefix] = [$isDefault ? 'required' : 'nullable', 'array'];
            $rules["$prefix.title"] = [$isDefault ? 'required' : 'sometimes', 'nullable', 'string', 'max:500'];
            $rules["$prefix.excerpt"] = ['nullable', 'string', 'max:2000'];
            $rules["$prefix.body"] = [$isDefault ? 'required' : 'sometimes', 'nullable', 'string', 'max:500000'];
            $rules["$prefix.seoTitle"] = ['nullable', 'string', 'max:255'];
            $rules["$prefix.seoDescription"] = ['nullable', 'string', 'max:8000'];
            $rules["$prefix.seoKeywords"] = ['nullable', 'string', 'max:500'];
        }

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $type = $this->input('contentableType') ?? $this->input('contentable_type');
            $id = $this->input('contentableId') ?? $this->input('contentable_id');
            if ($type === null && $id === null) {
                return;
            }
            if ($type === null || $id === null) {
                $validator->errors()->add('contentableId', 'Укажите тип и id карточки (или оставьте оба пустыми).');

                return;
            }
            $table = $type === 'service' ? 'services' : 'projects';
            if (! DB::table($table)->where('id', $id)->exists()) {
                $validator->errors()->add('contentableId', 'Карточка не найдена.');
            }
        });
    }
}
