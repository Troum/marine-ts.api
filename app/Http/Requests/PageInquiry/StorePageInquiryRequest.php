<?php

namespace App\Http\Requests\PageInquiry;

use Illuminate\Foundation\Http\FormRequest;

class StorePageInquiryRequest extends FormRequest
{
    /**
     * Допустимые id типов судна. Должен совпадать с константой `VESSEL_TYPES`
     * во фронте (`app/app/components/common/PageInquiryForm.vue`). Если
     * добавляешь новый id — обнови оба места.
     */
    private const ALLOWED_VESSEL_TYPES = [
        'dry_cargo',
        'tanker',
        'container',
        'tug',
        'service',
        'other',
    ];

    /**
     * Допустимые id требуемых услуг. Должен совпадать с константой
     * `REQUIRED_SERVICES` во фронте.
     */
    private const ALLOWED_REQUIRED_SERVICES = [
        'technical',
        'crewing',
        'audit',
        'commercial',
        'insurance',
        'other',
    ];

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
            'name' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:64'],
            'email' => ['required', 'string', 'email', 'max:255'],

            'vessel_types' => ['required', 'array', 'min:1'],
            'vessel_types.*' => ['string', 'in:'.implode(',', self::ALLOWED_VESSEL_TYPES)],

            'vessels_count' => ['required', 'integer', 'min:1', 'max:100000'],
            'vessel_flag' => ['required', 'string', 'max:255'],
            'main_ports' => ['nullable', 'string', 'max:1000'],

            'required_services' => ['required', 'array', 'min:1'],
            'required_services.*' => ['string', 'in:'.implode(',', self::ALLOWED_REQUIRED_SERVICES)],

            'message' => ['nullable', 'string', 'max:10000'],
            'source_page' => ['required', 'string', 'max:255'],
            'consent' => ['accepted'],
        ];
    }

    protected function prepareForValidation(): void
    {
        /**
         * Принимаем оба варианта именования — camelCase (фронт) и snake_case
         * (внутреннее представление). Это упрощает интеграцию форм без
         * сериализатора и сохраняет совместимость с прежними клиентами.
         */
        $map = [
            'sourcePage' => 'source_page',
            'vesselTypes' => 'vessel_types',
            'vesselsCount' => 'vessels_count',
            'vesselFlag' => 'vessel_flag',
            'mainPorts' => 'main_ports',
            'requiredServices' => 'required_services',
        ];
        foreach ($map as $camel => $snake) {
            if ($this->has($camel) && ! $this->has($snake)) {
                $this->merge([$snake => $this->input($camel)]);
            }
        }

        $trimmed = [];
        foreach ([
            'name',
            'company',
            'position',
            'phone',
            'email',
            'vessel_flag',
            'main_ports',
            'message',
            'source_page',
        ] as $key) {
            if (! $this->has($key) || ! is_string($this->input($key))) {
                continue;
            }

            $value = trim((string) $this->input($key));
            $trimmed[$key] = in_array($key, ['position', 'main_ports', 'message'], true) && $value === ''
                ? null
                : $value;
        }

        if ($trimmed !== []) {
            $this->merge($trimmed);
        }
    }
}
