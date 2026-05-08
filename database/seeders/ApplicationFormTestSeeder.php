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
        $vacancy = Vacancy::query()->with('translations')->orderBy('id')->first();
        if ($vacancy === null) {
            $this->command->warn('Нет вакансий в БД. Сначала выполните: php artisan db:seed --class=VacanciesSeeder');

            return;
        }

        $repo = app(ApplicationFormRepositoryInterface::class);

        $positionTitle = $vacancy->translationForLocale((string) config('marine.default_locale'))?->title ?? '';

        $existing = ApplicationForm::query()->where('email', 'test.anketa@marine-ts.local')->first();
        if ($existing !== null) {
            $repo->updateOne($existing, [
                'vacancy_id' => $vacancy->id,
                'status' => ApplicationFormStatus::Pending,
                'full_name' => 'Иванов Пётр',
                'phone' => '+7 900 123-45-67',
                'payload' => [
                    'vacancySlug' => $vacancy->slug,
                    'positionApplyingFor' => $positionTitle !== '' ? [$positionTitle] : [],
                    'lastName' => 'Иванов',
                    'firstName' => 'Пётр',
                    'expectedMonthlySalary' => '4500',
                    'expectedMonthlySalaryCurrency' => 'RUB',
                    'email' => 'test.anketa@marine-ts.local',
                    'mobilePhone' => '+7 900 123-45-67',
                    'desiredVesselTypes' => ['Tanker', 'Bulker'],
                    'note' => 'Тестовая запись из ApplicationFormTestSeeder',
                ],
            ]);
        } else {
            $repo->createOne([
                'vacancy_id' => $vacancy->id,
                'status' => ApplicationFormStatus::Pending,
                'full_name' => 'Иванов Пётр',
                'email' => 'test.anketa@marine-ts.local',
                'phone' => '+7 900 123-45-67',
                'payload' => [
                    'vacancySlug' => $vacancy->slug,
                    'positionApplyingFor' => $positionTitle !== '' ? [$positionTitle] : [],
                    'lastName' => 'Иванов',
                    'firstName' => 'Пётр',
                    'expectedMonthlySalary' => '4500',
                    'expectedMonthlySalaryCurrency' => 'RUB',
                    'email' => 'test.anketa@marine-ts.local',
                    'mobilePhone' => '+7 900 123-45-67',
                    'desiredVesselTypes' => ['Tanker', 'Bulker'],
                    'note' => 'Тестовая запись из ApplicationFormTestSeeder',
                ],
            ]);
        }

        $this->command->info('Тестовая анкета добавлена (email: test.anketa@marine-ts.local, vacancy_id: '.$vacancy->id.').');
    }
}
