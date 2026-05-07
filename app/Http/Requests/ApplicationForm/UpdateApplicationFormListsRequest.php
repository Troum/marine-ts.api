<?php

namespace App\Http\Requests\ApplicationForm;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicationFormListsRequest extends FormRequest
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
            'positionOptions' => ['required', 'array', 'min:1', 'max:200'],
            'positionOptions.*' => ['required', 'string', 'max:128'],
            'vesselTypeOptions' => ['required', 'array', 'min:1', 'max:200'],
            'vesselTypeOptions.*' => ['required', 'string', 'max:128'],
        ];
    }
}
