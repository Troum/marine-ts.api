<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServicesSeeder extends Seeder
{
    public function run(): void
    {
        Service::query()->forceDelete();

        $locales = config('marine.locales', ['ru', 'en']);

        /** @var list<array{icon_key: string, sort_order: int, ru: array, en: array}> */
        $items = [
            [
                'icon_key' => 'ship',
                'sort_order' => 10,
                'ru' => [
                    'title' => 'Ремонт корпусов',
                    'description' => 'Ультразвуковая дефектоскопия, сварка корпусных конструкций, замена обшивки. Соответствие стандартам IACS.',
                    'features' => ['УЗ дефектоскопия', 'Сварка корпуса', 'Замена обшивки', 'Гидроиспытания'],
                ],
                'en' => [
                    'title' => 'Hull repair',
                    'description' => 'Ultrasonic testing, hull welding and shell plating replacement. Compliance with IACS standards.',
                    'features' => ['UT inspection', 'Hull welding', 'Shell plating', 'Hydrostatic tests'],
                ],
            ],
            [
                'icon_key' => 'cog',
                'sort_order' => 20,
                'ru' => [
                    'title' => 'Ремонт двигателей',
                    'description' => 'Капитальный ремонт главных и вспомогательных двигателей. Ремонт головок блоков, коленвалов.',
                    'features' => ['Капитальный ремонт', 'Ремонт ГБЦ', 'Ремонт коленвала', 'Диагностика'],
                ],
                'en' => [
                    'title' => 'Engine repair',
                    'description' => 'Overhaul of main and auxiliary engines. Cylinder head and crankshaft repair.',
                    'features' => ['Major overhaul', 'Cylinder heads', 'Crankshaft', 'Diagnostics'],
                ],
            ],
            [
                'icon_key' => 'zap',
                'sort_order' => 30,
                'ru' => [
                    'title' => 'Электротехнические работы',
                    'description' => 'Ремонт генераторов, электродвигателей, трансформаторов. Модернизация щитового оборудования.',
                    'features' => ['Ремонт генераторов', 'Электродвигатели', 'Трансформаторы', 'Щитовое оборудование'],
                ],
                'en' => [
                    'title' => 'Electrical works',
                    'description' => 'Repair of generators, motors and transformers. Switchboard upgrades.',
                    'features' => ['Generators', 'Electric motors', 'Transformers', 'Switchboards'],
                ],
            ],
            [
                'icon_key' => 'settings2',
                'sort_order' => 40,
                'ru' => [
                    'title' => 'Трубопроводные системы',
                    'description' => 'Изготовление и замена трубопроводов, ремонт арматуры, гидравлические испытания.',
                    'features' => ['Изготовление труб', 'Ремонт арматуры', 'Гидроиспытания', 'Сварка труб'],
                ],
                'en' => [
                    'title' => 'Piping systems',
                    'description' => 'Fabrication and replacement of piping, valve repair, hydraulic tests.',
                    'features' => ['Pipe fabrication', 'Valves', 'Pressure tests', 'Pipe welding'],
                ],
            ],
            [
                'icon_key' => 'anchor',
                'sort_order' => 50,
                'ru' => [
                    'title' => 'Доковые работы',
                    'description' => 'Подготовка к докованию, работы на стапеле, окраска подводной части, замена анодов.',
                    'features' => ['Подготовка к доку', 'Работы на стапеле', 'Окраска', 'Замена анодов'],
                ],
                'en' => [
                    'title' => 'Dry-dock works',
                    'description' => 'Docking preparation, slipway work, underwater hull coating, anode replacement.',
                    'features' => ['Dock prep', 'Slipway', 'Coating', 'Anodes'],
                ],
            ],
            [
                'icon_key' => 'clipboard_check',
                'sort_order' => 60,
                'ru' => [
                    'title' => 'Инжиниринг и консалтинг',
                    'description' => '3D-сканирование, разработка проектной документации, сопровождение классификационных обследований.',
                    'features' => ['3D сканирование', 'Проектная документация', 'Классификация', 'Консалтинг'],
                ],
                'en' => [
                    'title' => 'Engineering & consulting',
                    'description' => '3D scanning, design documentation, support for classification surveys.',
                    'features' => ['3D scanning', 'Documentation', 'Classification', 'Consulting'],
                ],
            ],
        ];

        foreach ($items as $row) {
            /** @var Service $service */
            $service = Service::query()->create([
                'icon_key' => $row['icon_key'],
                'sort_order' => $row['sort_order'],
            ]);

            foreach ($locales as $locale) {
                if (! isset($row[$locale])) {
                    continue;
                }
                $t = $row[$locale];
                $service->translations()->create([
                    'locale' => $locale,
                    'title' => $t['title'],
                    'description' => $t['description'],
                    'features' => $t['features'],
                    'seo_title' => null,
                    'seo_description' => null,
                    'seo_keywords' => null,
                ]);
            }
        }
    }
}
