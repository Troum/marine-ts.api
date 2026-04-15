<?php

namespace App\Http\Requests\ApplicationForm;

use Illuminate\Foundation\Http\FormRequest;

class StoreOpenApplicationFormRequest extends FormRequest
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
        return [
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
