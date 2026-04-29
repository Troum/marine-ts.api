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
     * Поддерживаем оба формата:
     *  - чистый JSON (как раньше) — все поля попадают сразу в input;
     *  - multipart/form-data, где payload приходит JSON-строкой в поле `payload`,
     *    а `photo` — отдельным файлом. Распаковываем JSON и подмешиваем к input,
     *    чтобы все обычные правила работали без изменений.
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
            /** Фото кандидата: опционально, до 5 МБ, jpg/png/webp. */
            'photo' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}
