<?php

namespace App\Http\Requests\Project;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Project $project */
        $project = $this->route('project');

        return $this->user()->can('update', $project);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:500'],
            'type' => ['sometimes', 'string', 'in:hull,engine,electrical'],
            'typeLabel' => ['sometimes', 'string', 'max:255'],
            'location' => ['sometimes', 'string', 'max:255'],
            'date' => ['sometimes', 'string', 'max:32'],
            'description' => ['sometimes', 'string'],
            'stats' => ['sometimes', 'array'],
            'image' => ['nullable', 'string', 'max:500'],
            'seoTitle' => ['sometimes', 'nullable', 'string', 'max:255'],
            'seoDescription' => ['sometimes', 'nullable', 'string', 'max:8000'],
            'seoKeywords' => ['sometimes', 'nullable', 'string', 'max:500'],
        ];
    }
}
