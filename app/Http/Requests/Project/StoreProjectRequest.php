<?php

namespace App\Http\Requests\Project;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Project::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:500'],
            'type' => ['required', 'string', 'in:hull,engine,electrical'],
            'typeLabel' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'date' => ['required', 'string', 'max:32'],
            'description' => ['required', 'string'],
            'stats' => ['required', 'array'],
            'image' => ['nullable', 'string', 'max:500'],
            'seoTitle' => ['nullable', 'string', 'max:255'],
            'seoDescription' => ['nullable', 'string', 'max:8000'],
            'seoKeywords' => ['nullable', 'string', 'max:500'],
        ];
    }
}
