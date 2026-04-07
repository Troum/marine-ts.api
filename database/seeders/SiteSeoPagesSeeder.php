<?php

namespace Database\Seeders;

use App\Models\SiteSeoPage;
use Illuminate\Database\Seeder;

class SiteSeoPagesSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            ['slug' => 'home', 'label' => 'Главная', 'seo_title' => 'Marine Technical Solutions — судоремонт и техобслуживание', 'seo_description' => 'Комплексный судоремонт, диагностика и инжиниринг для морского флота.', 'seo_keywords' => 'судоремонт, морской сервис, MTS'],
            ['slug' => 'about', 'label' => 'О компании', 'seo_title' => 'О компании — Marine Technical Solutions', 'seo_description' => 'История, команда и сертификаты Marine Technical Solutions.', 'seo_keywords' => 'о компании, судоремонт'],
            ['slug' => 'contacts', 'label' => 'Контакты', 'seo_title' => 'Контакты — Marine Technical Solutions', 'seo_description' => 'Свяжитесь с нами для консультации и коммерческого предложения.', 'seo_keywords' => 'контакты, заявка'],
            ['slug' => 'services', 'label' => 'Услуги', 'seo_title' => 'Услуги — судоремонт и техобслуживание', 'seo_description' => 'Ремонт корпусов, двигателей, электрики и инжиниринг для судов.', 'seo_keywords' => 'услуги, ремонт судов'],
            ['slug' => 'news', 'label' => 'Новости', 'seo_title' => 'Новости компании', 'seo_description' => 'Актуальные новости и события Marine Technical Solutions.', 'seo_keywords' => 'новости, пресс-релизы'],
            ['slug' => 'projects', 'label' => 'Проекты', 'seo_title' => 'Проекты и портфолио', 'seo_description' => 'Реализованные проекты по ремонту и модернизации судов.', 'seo_keywords' => 'проекты, портфолио'],
            ['slug' => 'vacancies', 'label' => 'Вакансии', 'seo_title' => 'Вакансии — Marine Technical Solutions', 'seo_description' => 'Открытые позиции в Marine Technical Solutions: инженеры, сварщики, механики и другие специалисты.', 'seo_keywords' => 'вакансии, работа, карьера, судоремонт'],
            ['slug' => 'crewing-management', 'label' => 'Крюинг-менеджмент', 'seo_title' => 'Крюинг-менеджмент — Marine Technical Solutions', 'seo_description' => 'Подбор экипажа, кадровое сопровождение, документооборот и соответствие требованиям для морских судов.', 'seo_keywords' => 'крюинг, crew management, экипаж, моряки, STCW'],
            ['slug' => 'privacy', 'label' => 'Политика конфиденциальности', 'seo_title' => 'Политика конфиденциальности', 'seo_description' => 'Обработка персональных данных на сайте Marine Technical Solutions.', 'seo_keywords' => 'privacy, персональные данные'],
            ['slug' => 'terms', 'label' => 'Условия использования', 'seo_title' => 'Условия использования сайта', 'seo_description' => 'Правила пользования сайтом Marine Technical Solutions.', 'seo_keywords' => 'условия, terms'],
        ];

        foreach ($pages as $row) {
            SiteSeoPage::updateOrCreate(
                ['slug' => $row['slug']],
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
