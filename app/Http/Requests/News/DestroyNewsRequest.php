<?php

namespace App\Http\Requests\News;

use App\Models\News;
use Illuminate\Foundation\Http\FormRequest;

class DestroyNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var News $news */
        $news = $this->route('news');

        return $this->user()->can('delete', $news);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
