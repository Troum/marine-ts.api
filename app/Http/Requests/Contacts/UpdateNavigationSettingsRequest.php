<?php

namespace App\Http\Requests\Contacts;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateNavigationSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->can('manage navigation');
    }

    protected function prepareForValidation(): void
    {
        $bc = $this->input('burgerContacts');
        if (! is_array($bc)) {
            return;
        }
        $this->merge(['burgerContacts' => $this->camelizeBurgerContactsKeysDeep($bc)]);
    }

    /**
     * @param  array<string, mixed>  $arr
     * @return array<string, mixed>
     */
    private function camelizeBurgerContactsKeysDeep(array $arr): array
    {
        $out = [];
        foreach ($arr as $key => $value) {
            $k = is_string($key) && str_contains($key, '_')
                ? Str::camel($key)
                : $key;
            if (is_array($value)) {
                $isList = array_keys($value) === range(0, count($value) - 1);
                if ($isList) {
                    $out[$k] = array_map(function ($item): mixed {
                        if (is_array($item)) {
                            return $this->camelizeBurgerContactsKeysDeep($item);
                        }

                        return $item;
                    }, $value);
                } else {
                    $out[$k] = $this->camelizeBurgerContactsKeysDeep($value);
                }
            } else {
                $out[$k] = $value;
            }
        }

        return $out;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'main' => ['required', 'array', 'min:0', 'max:20'],
            'main.*.path' => ['required', 'string', 'max:500'],
            'main.*.label' => ['required', 'array'],
            'main.*.label.ru' => ['required', 'string', 'max:120'],
            'main.*.label.en' => ['required', 'string', 'max:120'],
            'main.*.children' => ['nullable', 'array', 'max:20'],
            'main.*.children.*.path' => ['required', 'string', 'max:500'],
            'main.*.children.*.label' => ['required', 'array'],
            'main.*.children.*.label.ru' => ['required', 'string', 'max:120'],
            'main.*.children.*.label.en' => ['required', 'string', 'max:120'],

            'more' => ['present', 'array', 'max:20'],
            'more.*.path' => ['required', 'string', 'max:500'],
            'more.*.label' => ['required', 'array'],
            'more.*.label.ru' => ['required', 'string', 'max:120'],
            'more.*.label.en' => ['required', 'string', 'max:120'],
            'more.*.children' => ['nullable', 'array', 'max:20'],
            'more.*.children.*.path' => ['required', 'string', 'max:500'],
            'more.*.children.*.label' => ['required', 'array'],
            'more.*.children.*.label.ru' => ['required', 'string', 'max:120'],
            'more.*.children.*.label.en' => ['required', 'string', 'max:120'],

            'menuVariant' => ['sometimes', 'string', Rule::in(['overlay', 'horizontal'])],
            'menuFontSize' => ['sometimes', 'string', Rule::in(['sm', 'base', 'lg', 'xl', '2xl'])],
            'menuFontWeight' => ['sometimes', 'string', Rule::in(['light', 'normal', 'medium', 'semibold', 'bold'])],
            'menuTextCase' => ['sometimes', 'string', Rule::in(['none', 'lowercase', 'uppercase', 'capitalize'])],
            'menuJustify' => ['sometimes', 'string', Rule::in(['center', 'between'])],

            'menuItemHoverColor' => ['nullable', 'string', 'max:32'],
            'menuItemColor' => ['nullable', 'string', 'max:32'],

            'horizItems' => ['nullable', 'array', 'max:20'],
            'horizItems.*.path' => ['required', 'string', 'max:500'],
            'horizItems.*.label' => ['required', 'array'],
            'horizItems.*.label.ru' => ['required', 'string', 'max:120'],
            'horizItems.*.label.en' => ['required', 'string', 'max:120'],
            'horizItems.*.children' => ['nullable', 'array', 'max:20'],
            'horizItems.*.children.*.path' => ['required', 'string', 'max:500'],
            'horizItems.*.children.*.label' => ['required', 'array'],
            'horizItems.*.children.*.label.ru' => ['required', 'string', 'max:120'],
            'horizItems.*.children.*.label.en' => ['required', 'string', 'max:120'],

            'burgerContacts' => ['nullable', 'array'],
            'burgerContacts.phonesTitle' => ['nullable', 'string', 'max:8000'],
            'burgerContacts.phones' => ['nullable', 'array', 'max:10'],
            'burgerContacts.phones.*' => ['string', 'max:40'],
            'burgerContacts.emailTitle' => ['nullable', 'string', 'max:8000'],
            'burgerContacts.emails' => ['nullable', 'array', 'max:10'],
            'burgerContacts.emails.*' => ['string', 'max:200'],
            'burgerContacts.socials' => ['nullable', 'array', 'max:10'],
            'burgerContacts.socials.*.url' => ['required', 'string', 'max:500'],
            'burgerContacts.socials.*.label' => ['nullable', 'string', 'max:120'],
            'burgerContacts.officesColumnTitle' => ['nullable', 'string', 'max:8000'],
            'burgerContacts.offices' => ['nullable', 'array', 'max:10'],
            'burgerContacts.offices.*.title' => ['nullable', 'string', 'max:120'],
            'burgerContacts.offices.*.address' => ['required', 'string', 'max:2000'],
            /** Обратная совместимость */
            'burgerContacts.email' => ['nullable', 'string', 'max:200'],
            'burgerContacts.socialUrl' => ['nullable', 'string', 'max:500'],
            'burgerContacts.socialLabel' => ['nullable', 'string', 'max:120'],
            'burgerContacts.officeTitle' => ['nullable', 'string', 'max:120'],
            'burgerContacts.officeAddress' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            foreach (['main', 'more', 'horizItems'] as $section) {
                $items = $this->input($section);
                if (! is_array($items)) {
                    continue;
                }
                foreach ($items as $i => $item) {
                    if (! is_array($item)) {
                        continue;
                    }
                    $path = isset($item['path']) ? trim((string) $item['path']) : '';
                    $prefix = "{$section}.{$i}.path";
                    if ($path === '') {
                        $validator->errors()->add($prefix, 'Укажите путь или URL.');

                        continue;
                    }
                    if ($path === '#') {
                        $children = $item['children'] ?? [];
                        if (! is_array($children) || count($children) === 0) {
                            $validator->errors()->add($prefix, 'Для пути # нужна хотя бы одна вложенная ссылка.');
                        }

                        continue;
                    }
                    if (preg_match('#^https?://#i', $path)) {
                        continue;
                    }
                    if ($path !== '/' && ! preg_match('#^/[a-zA-Z0-9/_-]*$#', $path)) {
                        $validator->errors()->add($prefix, 'Путь должен начинаться с / (латиница, цифры, дефис, слэш), быть # при подменю без страницы, или укажите полный http(s) URL.');
                    }
                }

                foreach ($items as $i => $item) {
                    if (! is_array($item) || ! isset($item['children']) || ! is_array($item['children'])) {
                        continue;
                    }
                    foreach ($item['children'] as $ci => $child) {
                        if (! is_array($child)) {
                            continue;
                        }
                        $cpath = isset($child['path']) ? trim((string) $child['path']) : '';
                        $cprefix = "{$section}.{$i}.children.{$ci}.path";
                        if ($cpath === '' || $cpath === '#') {
                            $validator->errors()->add($cprefix, 'У вложенного пункта укажите обычный путь или URL (не #).');

                            continue;
                        }
                        if (preg_match('#^https?://#i', $cpath)) {
                            continue;
                        }
                        if ($cpath !== '/' && ! preg_match('#^/[a-zA-Z0-9/_-]*$#', $cpath)) {
                            $validator->errors()->add($cprefix, 'Путь должен начинаться с / (латиница, цифры, дефис, слэш) или укажите полный http(s) URL.');
                        }
                    }
                }
            }
        });
    }
}
