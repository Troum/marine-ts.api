<?php

namespace App\Support;

/**
 * Список типов документов для запроса у кандидата (ключи совпадают с полями загрузки на клиенте).
 */
final class RequestedDocumentCatalog
{
    private const TRAVEL_LABELS = [
        'Travelling passport',
        'Civil passport',
        'SBK',
        'SID',
        'Schengen visa',
        'US VISA (C1/D)',
    ];

    private const COMPETENCY_LABELS = [
        'Certificate of Competency № (CoC)',
        'CoC Certificate of Endorsement №',
        'GMDSS Operator Certificate № (GOC)',
        'GOC Certificate of Endorsement №',
    ];

    private const OTHER_CERT_LABELS = [
        'Basic Safety Training & Instruction (Table A-VI/1-1)',
        'Proficiency in Survival craft & Rescue Boats (Table A-VI/2-1)',
        'Advanced Fire Fighting (Table A-VI/3)',
        'Medical First Aid (Table A-VI/4-1)',
        'Medical Care (Table A-VI/4-2)',
        'Security training for seafarers with designated security duties (Table A-VI/6-2)',
        'Security Awareness Training (Table A-VI/ 6-1)',
        'Operational use of automatic radar plotting aids (ARPA)',
        'Radar observation and plotting (RADAR)',
        'ECDIS',
        'HAZMAT',
        'Bridge team and resource management',
        'Vaccination/YF',
        'Medical Health Certificate',
        'ISPS/SSO',
        'Preventing and responding to violence and harassment, including sexual harassment, bullying, and sexual violence',
        'Tanker certificates:',
    ];

    /**
     * @return list<array{key: string, label: string, group: string}>
     */
    public static function entries(): array
    {
        $rows = [];
        $rows[] = ['key' => 'photo', 'label' => 'Фотография', 'group' => 'Общее'];

        foreach (self::TRAVEL_LABELS as $i => $label) {
            $rows[] = ['key' => 'travel:'.$i, 'label' => $label, 'group' => 'Документы для поездок'];
        }
        for ($i = 0; $i < 8; $i++) {
            $rows[] = [
                'key' => 'travel_other:'.$i,
                'label' => 'Доп. документ для поездок (строка '.($i + 1).')',
                'group' => 'Документы для поездок',
            ];
        }

        foreach (self::COMPETENCY_LABELS as $i => $label) {
            $rows[] = ['key' => 'competency:'.$i, 'label' => $label, 'group' => 'Сертификаты компетенций'];
        }
        for ($i = 0; $i < 8; $i++) {
            $rows[] = [
                'key' => 'competency_other:'.$i,
                'label' => 'Доп. сертификат компетенций (строка '.($i + 1).')',
                'group' => 'Сертификаты компетенций',
            ];
        }

        foreach (self::OTHER_CERT_LABELS as $i => $label) {
            $rows[] = ['key' => 'other_cert:'.$i, 'label' => $label, 'group' => 'Прочие сертификаты'];
        }
        for ($i = 0; $i < 8; $i++) {
            $rows[] = [
                'key' => 'other_cert_extra:'.$i,
                'label' => 'Доп. прочий сертификат (строка '.($i + 1).')',
                'group' => 'Прочие сертификаты',
            ];
        }

        $rows[] = ['key' => 'passport_scan', 'label' => 'Скан паспорта (общий)', 'group' => 'Прочее'];
        $rows[] = ['key' => 'cv', 'label' => 'Резюме / CV', 'group' => 'Прочее'];
        $rows[] = ['key' => 'other', 'label' => 'Иной документ (по согласованию)', 'group' => 'Прочее'];

        return $rows;
    }

    /**
     * @return list<string>
     */
    public static function validKeys(): array
    {
        return array_map(fn (array $row) => $row['key'], self::entries());
    }

    public static function labelFor(string $key): string
    {
        foreach (self::entries() as $row) {
            if ($row['key'] === $key) {
                return $row['label'];
            }
        }

        return $key;
    }
}
