<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateFooterNavigationSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->can('manage navigation');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'columns' => ['required', 'array', 'size:3'],
            'columns.*.title' => ['required', 'array'],
            'columns.*.title.ru' => ['required', 'string', 'max:80'],
            'columns.*.title.en' => ['required', 'string', 'max:80'],
            'columns.*.links' => ['required', 'array', 'max:30'],
            'columns.*.links.*.path' => ['required', 'string', 'max:500'],
            'columns.*.links.*.label' => ['required', 'array'],
            'columns.*.links.*.label.ru' => ['required', 'string', 'max:120'],
            'columns.*.links.*.label.en' => ['required', 'string', 'max:120'],

            'legal' => ['required', 'array', 'max:10'],
            'legal.*.path' => ['required', 'string', 'max:500'],
            'legal.*.label' => ['required', 'array'],
            'legal.*.label.ru' => ['required', 'string', 'max:120'],
            'legal.*.label.en' => ['required', 'string', 'max:120'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            foreach (['columns' => true, 'legal' => false] as $section => $isColumn) {
                $items = $this->input($section);
                if (! is_array($items)) {
                    continue;
                }
                if ($isColumn) {
                    foreach ($items as $ci => $col) {
                        if (! is_array($col) || ! isset($col['links']) || ! is_array($col['links'])) {
                            continue;
                        }
                        foreach ($col['links'] as $li => $link) {
                            $this->validatePath($validator, "columns.{$ci}.links.{$li}.path", $link);
                        }
                    }
                } else {
                    foreach ($items as $i => $link) {
                        $this->validatePath($validator, "legal.{$i}.path", is_array($link) ? $link : []);
                    }
                }
            }
        });
    }

    /**
     * @param  array<string, mixed>  $link
     */
    private function validatePath(Validator $validator, string $prefix, array $link): void
    {
        $path = isset($link['path']) ? trim((string) $link['path']) : '';
        if ($path === '' || $path === '#') {
            $validator->errors()->add($prefix, 'Укажите путь или полный URL (без #).');

            return;
        }
        if (preg_match('#^https?://#i', $path)) {
            return;
        }
        if ($path !== '/' && ! preg_match('#^/[a-zA-Z0-9/_-]*$#', $path)) {
            $validator->errors()->add($prefix, 'Путь должен начинаться с / или укажите полный http(s) URL.');
        }
    }
}
