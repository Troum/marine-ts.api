<?php

namespace Database\Seeders;

use App\Contracts\Repositories\ApplicationFormRepositoryInterface;
use App\Enums\ApplicationFormStatus;
use App\Models\ApplicationForm;
use App\Models\Vacancy;
use Illuminate\Database\Seeder;

class ApplicationFormTestSeeder extends Seeder
{
    public function run(): void
    {
        $vacancy = Vacancy::query()->orderBy('id')->first();
        if ($vacancy === null) {
            $this->command->warn('Нет вакансий в БД. Сначала выполните: php artisan db:seed --class=VacanciesSeeder');

            return;
        }

        $repo = app(ApplicationFormRepositoryInterface::class);

        $existing = ApplicationForm::query()->where('email', 'test.anketa@marine-ts.local')->first();
        if ($existing !== null) {
            $repo->updateOne($existing, [
                'vacancy_id' => $vacancy->id,
                'status' => ApplicationFormStatus::Pending,
                'full_name' => 'Иванов Пётр Сергеевич',
                'phone' => '+7 900 123-45-67',
                'payload' => [
                    'vacancySlug' => $vacancy->slug,
                    'positionApplyingFor' => $vacancy->title,
                    'lastName' => 'Иванов',
                    'firstName' => 'Пётр',
                    'fathersName' => 'Сергеевич',
                    'email' => 'test.anketa@marine-ts.local',
                    'mobilePhone' => '+7 900 123-45-67',
                    'note' => 'Тестовая запись из ApplicationFormTestSeeder',
                ],
            ]);
        } else {
            $repo->createOne([
                'vacancy_id' => $vacancy->id,
                'status' => ApplicationFormStatus::Pending,
                'full_name' => 'Иванов Пётр Сергеевич',
                'email' => 'test.anketa@marine-ts.local',
                'phone' => '+7 900 123-45-67',
                'payload' => [
                    'vacancySlug' => $vacancy->slug,
                    'positionApplyingFor' => $vacancy->title,
                    'lastName' => 'Иванов',
                    'firstName' => 'Пётр',
                    'fathersName' => 'Сергеевич',
                    'email' => 'test.anketa@marine-ts.local',
                    'mobilePhone' => '+7 900 123-45-67',
                    'note' => 'Тестовая запись из ApplicationFormTestSeeder',
                ],
            ]);
        }

        $this->command->info('Тестовая анкета добавлена (email: test.anketa@marine-ts.local, vacancy_id: '.$vacancy->id.').');
    }
}
