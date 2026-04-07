<?php

namespace App\Http\Requests\Analytics;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StorePageViewRequest extends FormRequest
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
            'path' => ['required', 'string', 'max:2048', 'regex:/^\//'],
            'title' => ['nullable', 'string', 'max:512'],
            'referrer' => ['nullable', 'string', 'max:2048'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $path = $this->input('path');
            if (! is_string($path)) {
                return;
            }
            if (preg_match('#^/admin(/|$)#i', $path) === 1) {
                $v->errors()->add('path', 'Admin routes are not tracked.');
            }
        });
    }
}
