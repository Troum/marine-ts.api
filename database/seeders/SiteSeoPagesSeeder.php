<?php

namespace Database\Seeders;

use App\Models\SiteSeoPage;
use Illuminate\Database\Seeder;

class SiteSeoPagesSeeder extends Seeder
{
    public function run(): void
    {
        $locale = (string) config('marine.default_locale', 'ru');

        $pages = [
            ['slug' => 'home', 'label' => 'Главная', 'seo_title' => 'Marine Technical Solutions — судоремонт и техобслуживание', 'seo_description' => 'Комплексный судоремонт, диагностика и инжиниринг для морского флота.', 'seo_keywords' => 'судоремонт, морской сервис, MTS'],
            ['slug' => 'about', 'label' => 'О компании', 'seo_title' => 'О компании — Marine Technical Solutions', 'seo_description' => 'История, команда и сертификаты Marine Technical Solutions.', 'seo_keywords' => 'о компании, судоремонт'],
            ['slug' => 'contacts', 'label' => 'Контакты', 'seo_title' => 'Контакты — Marine Technical Solutions', 'seo_description' => 'Свяжитесь с нами для консультации и коммерческого предложения.', 'seo_keywords' => 'контакты, заявка'],
            ['slug' => 'request', 'label' => 'Заявка', 'seo_title' => 'Оставить заявку — Marine Technical Solutions', 'seo_description' => 'Оставьте заявку на коммерческое предложение и консультацию по судовому и крюинг-менеджменту.', 'seo_keywords' => 'заявка, запрос, коммерческое предложение'],
            ['slug' => 'services', 'label' => 'Судоремонт', 'seo_title' => 'Судоремонт — ремонт и техническое обслуживание судов | Marine Technical Solutions', 'seo_description' => 'Судоремонт любой сложности: докование, аварийный ремонт, ремонт двигателей, автоматики и судовых систем в портах по всему миру.', 'seo_keywords' => 'судоремонт, ремонт судов, техническое обслуживание судов, аварийный ремонт судов, докование'],
            ['slug' => 'news', 'label' => 'Новости', 'seo_title' => 'Новости компании', 'seo_description' => 'Актуальные новости и события Marine Technical Solutions.', 'seo_keywords' => 'новости, пресс-релизы'],
            ['slug' => 'projects', 'label' => 'Проекты', 'seo_title' => 'Проекты и портфолио', 'seo_description' => 'Реализованные проекты по ремонту и модернизации судов.', 'seo_keywords' => 'проекты, портфолио'],
            ['slug' => 'vacancies', 'label' => 'Вакансии', 'seo_title' => 'Вакансии — Marine Technical Solutions', 'seo_description' => 'Открытые позиции в Marine Technical Solutions: инженеры, сварщики, механики и другие специалисты.', 'seo_keywords' => 'вакансии, работа, карьера, судоремонт'],
            ['slug' => 'ship-management', 'label' => 'Судовой менеджмент', 'seo_title' => 'Судовой менеджмент — Marine Technical Solutions', 'seo_description' => 'Операционное и техническое сопровождение флота: ТОиР, класс, отчётность судовладельцу.', 'seo_keywords' => 'судовой менеджмент, ship management, флот'],
            ['slug' => 'crewing-management', 'label' => 'Крюинг-менеджмент', 'seo_title' => 'Крюинг-менеджмент — Marine Technical Solutions', 'seo_description' => 'Подбор экипажа, кадровое сопровождение, документооборот и соответствие требованиям для морских судов.', 'seo_keywords' => 'крюинг, crew management, экипаж, моряки, STCW'],
            ['slug' => 'lnk', 'label' => 'ЛНК — лаборатория неразрушающего контроля', 'seo_title' => 'Лаборатория неразрушающего контроля (ЛНК) — Marine Technical Solutions', 'seo_description' => 'Ультразвуковая толщинометрия, диагностика судовых систем и подготовка к освидетельствованиям класса для морского флота.', 'seo_keywords' => 'ЛНК, неразрушающий контроль, UTM, дефектоскопия, Marine Technical Solutions'],
            ['slug' => 'privacy', 'label' => 'Политика конфиденциальности', 'seo_title' => 'Политика конфиденциальности', 'seo_description' => 'Обработка персональных данных на сайте Marine Technical Solutions.', 'seo_keywords' => 'privacy, персональные данные'],
            ['slug' => 'terms', 'label' => 'Условия использования', 'seo_title' => 'Условия использования сайта', 'seo_description' => 'Правила пользования сайтом Marine Technical Solutions.', 'seo_keywords' => 'условия, terms'],
        ];

        foreach ($pages as $row) {
            /** @var SiteSeoPage $page */
            $page = SiteSeoPage::query()->updateOrCreate(
                ['slug' => $row['slug']],
                []
            );

            $page->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'label' => $row['label'],
                    'seo_title' => $row['seo_title'],
                    'seo_description' => $row['seo_description'],
                    'seo_keywords' => $row['seo_keywords'],
                ]
            );
        }
    }
}
