<?php

/**
 * Дефолтное меню шапки (до сохранения в site_settings).
 *
 * @var array{main: list<array{path: string, label: array{ru: string, en: string}}>, more: list<array{path: string, label: array{ru: string, en: string}}>}
 */
return [
    'main' => [
        ['path' => '/', 'label' => ['ru' => 'Главная', 'en' => 'Home']],
        ['path' => '/about', 'label' => ['ru' => 'О компании', 'en' => 'About']],
        ['path' => '/services', 'label' => ['ru' => 'Услуги', 'en' => 'Services']],
        ['path' => '/vacancies', 'label' => ['ru' => 'Вакансии', 'en' => 'Careers']],
        ['path' => '/contacts', 'label' => ['ru' => 'Контакты', 'en' => 'Contacts']],
    ],
    'more' => [
        ['path' => '/projects', 'label' => ['ru' => 'Проекты', 'en' => 'Projects']],
        ['path' => '/gallery', 'label' => ['ru' => 'Галерея', 'en' => 'Gallery']],
        ['path' => '/news', 'label' => ['ru' => 'Новости', 'en' => 'News']],
    ],
];
