<?php

namespace App\Http\Requests\Vacancy;

use App\Models\Vacancy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVacancyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Vacancy::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:500'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('vacancies', 'slug')],
            'excerpt' => ['required', 'string'],
            'content' => ['nullable', 'string'],
            'requirements' => ['nullable', 'array'],
            'requirements.*' => ['string', 'max:2000'],
            'location' => ['nullable', 'string', 'max:255'],
            'employmentType' => ['nullable', 'string', 'max:255'],
            'sortOrder' => ['sometimes', 'integer', 'min:0'],
            'isPublished' => ['sometimes', 'boolean'],
            'seoTitle' => ['nullable', 'string', 'max:255'],
            'seoDescription' => ['nullable', 'string', 'max:8000'],
            'seoKeywords' => ['nullable', 'string', 'max:500'],
        ];
    }
}
