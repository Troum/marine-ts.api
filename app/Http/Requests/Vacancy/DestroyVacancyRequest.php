<?php

namespace App\Http\Requests\Vacancy;

use App\Models\Vacancy;
use Illuminate\Foundation\Http\FormRequest;

class DestroyVacancyRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Vacancy $vacancy */
        $vacancy = $this->route('vacancy');

        return $this->user()->can('delete', $vacancy);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
