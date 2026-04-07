<?php

namespace App\Http\Requests\ApplicationForm;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreApplicationFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $slug = $this->route('slug');
        if (! is_string($slug)) {
            $slug = '';
        }

        return [
            'vacancySlug' => ['required', 'string', 'max:255', Rule::in([$slug])],
            'lastName' => ['required', 'string', 'max:255'],
            'firstName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'mobilePhone' => ['required', 'string', 'max:128'],
            'consentRuAccuracy' => ['accepted'],
            'consentRuPd' => ['accepted'],
            'consentEnAccuracy' => ['accepted'],
            'consentEnPd' => ['accepted'],
        ];
    }
}
