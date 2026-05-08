<?php

namespace App\Http\Requests\ApplicationForm;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOpenApplicationFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Поддержка multipart: распаковываем JSON-строку из поля `payload`,
     * чтобы обычные правила работали и с FormData (где сверху едет файл `photo`).
     */
    protected function prepareForValidation(): void
    {
        $rawPayload = $this->input('payload');
        if (is_string($rawPayload) && $rawPayload !== '') {
            $decoded = json_decode($rawPayload, true);
            if (is_array($decoded)) {
                $this->merge($decoded);
                $this->request->remove('payload');
            }
        }

        foreach ([
            'fathersName',
            'homePhone',
            'home_phone',
            'messenger',
            'messengers',
        ] as $deprecated) {
            $this->request->remove($deprecated);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'positionApplyingFor' => ['required', 'array', 'min:1', 'max:3'],
            'positionApplyingFor.*' => ['required', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'firstName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'mobilePhone' => ['required', 'string', 'max:128'],
            'consentRuAccuracy' => ['accepted'],
            'consentRuPd' => ['accepted'],
            'consentEnAccuracy' => ['accepted'],
            'consentEnPd' => ['accepted'],
            'photo' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'desiredVesselTypes' => ['required', 'array', 'min:1', 'max:3'],
            'desiredVesselTypes.*' => ['required', 'string', 'max:128'],
            'expectedMonthlySalary' => ['nullable', 'string', 'max:64'],
            'expectedMonthlySalaryCurrency' => ['nullable', 'string', Rule::in(['RUB', 'USD', 'EUR'])],
        ];
    }
}
