<?php

namespace App\Http\Requests\ContentPage;

use App\Models\ContentPage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StoreContentPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ContentPage::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('content_pages', 'slug')],
            'title' => ['required', 'string', 'max:500'],
            'excerpt' => ['nullable', 'string', 'max:2000'],
            'body' => ['required', 'string', 'max:500000'],
            'isPublished' => ['sometimes', 'boolean'],
            'is_published' => ['sometimes', 'boolean'],
            'sortOrder' => ['sometimes', 'integer', 'min:0', 'max:999999'],
            'sort_order' => ['sometimes', 'integer', 'min:0', 'max:999999'],
            'seoTitle' => ['nullable', 'string', 'max:255'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seoDescription' => ['nullable', 'string', 'max:8000'],
            'seo_description' => ['nullable', 'string', 'max:8000'],
            'seoKeywords' => ['nullable', 'string', 'max:500'],
            'seo_keywords' => ['nullable', 'string', 'max:500'],
            'contentableType' => ['sometimes', 'nullable', 'string', 'in:service,project'],
            'contentableId' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'contentable_type' => ['sometimes', 'nullable', 'string', 'in:service,project'],
            'contentable_id' => ['sometimes', 'nullable', 'integer', 'min:1'],
        ];
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
    }

    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $validator): void {
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
