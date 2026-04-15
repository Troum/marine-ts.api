<?php

namespace App\Support;

use App\Models\ApplicationForm;

final class ApplicationFormPdfPresenter
{
    /**
     * @return list<array{0: string, 1: string}>
     */
    public static function rows(ApplicationForm $form): array
    {
        $form->loadMissing('vacancy.translations');
        $payload = $form->payload ?? [];
        $vacancyTitle = $form->vacancy_id === null
            ? 'Открытая заявка (без вакансии)'
            : ($form->vacancy?->translationForLocale((string) config('marine.default_locale'))?->title ?? '—');

        $rows = [
            ['ID анкеты', (string) $form->id],
            ['Дата отправки', $form->created_at?->format('d.m.Y H:i') ?? '—'],
            ['Вакансия', $vacancyTitle],
            ['ФИО', $form->full_name],
            ['Email', $form->email],
            ['Телефон', $form->phone ?? '—'],
        ];

        $rows[] = ['', ''];
        $rows[] = ['Данные анкеты', ''];

        foreach ($payload as $key => $value) {
            $rows[] = [self::fieldLabel((string) $key), self::formatValue($value)];
        }

        return $rows;
    }

    private static function fieldLabel(string $key): string
    {
        return match ($key) {
            'vacancySlug' => 'Slug вакансии',
            'positionApplyingFor' => 'Должность',
            'surnameAndName' => 'Фамилия и имя (одной строкой)',
            'dateOfBirth' => 'Дата рождения',
            'photoFileName' => 'Файл фото',
            'lastName' => 'Фамилия',
            'firstName' => 'Имя',
            'fathersName' => 'Отчество',
            'maritalStatus' => 'Семейное положение',
            'placeOfBirth' => 'Место рождения',
            'availableFrom' => 'Доступен с',
            'citizenship' => 'Гражданство',
            'englishLevel' => 'Уровень английского',
            'mobilePhone' => 'Мобильный телефон',
            'homePhone' => 'Домашний телефон',
            'email' => 'Email (из анкеты)',
            'messenger' => 'Мессенджер',
            'homeAddress' => 'Адрес',
            'nearestAirport' => 'Ближайший аэропорт',
            'nokLastName' => 'Родственник: фамилия',
            'nokFirstName' => 'Родственник: имя',
            'nokContactNumber' => 'Родственник: контакт',
            'nokEmail' => 'Родственник: email',
            'nokRelationship' => 'Родственник: степень родства',
            'nokAddress' => 'Родственник: адрес',
            'travelRows' => 'Документы для поездок',
            'travelOtherRows' => 'Доп. документы для поездок',
            'competencyRows' => 'Сертификаты компетенций',
            'competencyOtherRows' => 'Доп. сертификаты компетенций',
            'otherCertificateRows' => 'Прочие сертификаты',
            'otherCertificateExtraRows' => 'Доп. прочие сертификаты',
            'seaServiceRows' => 'Морская служба',
            'educationRows' => 'Образование',
            'safetyOverallSize' => 'Размер одежды (overall)',
            'safetyHeight' => 'Рост',
            'safetyShoeSize' => 'Размер обуви',
            'safetyWeight' => 'Вес',
            'consentRuAccuracy' => 'Согласие (RU): достоверность',
            'consentRuPd' => 'Согласие (RU): ПД',
            'consentEnAccuracy' => 'Согласие (EN): достоверность',
            'consentEnPd' => 'Согласие (EN): ПД',
            'supplementaryFiles' => 'Дополнительно загруженные файлы (по ссылке)',
            default => $key,
        };
    }

    private static function formatValue(mixed $value): string
    {
        if ($value === null) {
            return '—';
        }
        if (is_bool($value)) {
            return $value ? 'Да' : 'Нет';
        }
        if (is_scalar($value)) {
            return (string) $value;
        }
        if (is_array($value)) {
            return self::formatArrayValue($value);
        }

        return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param  array<mixed>  $value
     */
    private static function formatArrayValue(array $value): string
    {
        if ($value === []) {
            return '—';
        }

        if (array_is_list($value)) {
            $blocks = [];
            foreach ($value as $i => $item) {
                if (is_array($item)) {
                    $blocks[] = '— Запись '.($i + 1).":\n".self::formatAssocOrList($item);
                } else {
                    $blocks[] = '— '.(string) $item;
                }
            }

            return implode("\n\n", $blocks);
        }

        return self::formatAssocOrList($value);
    }

    /**
     * @param  array<mixed>  $item
     */
    private static function formatAssocOrList(array $item): string
    {
        $lines = [];
        foreach ($item as $k => $v) {
            if (is_array($v)) {
                $lines[] = self::fieldLabel((string) $k).":\n".self::formatValue($v);
            } else {
                $lines[] = self::fieldLabel((string) $k).': '.self::formatValue($v);
            }
        }

        return implode("\n", $lines);
    }
}
