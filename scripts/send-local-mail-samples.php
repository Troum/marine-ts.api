<?php

/**
 * Локальная отправка образцов транзакционных писем (Herd Mail / Mailpit на MAIL_PORT).
 * Запуск: php scripts/send-local-mail-samples.php
 *
 * После каждого письма вызывается Mail::purge(), иначе Herd SMTP может ответить
 * «421 too many unauthenticated commands» на одном соединении.
 */

use App\Enums\ApplicationFormStatus;
use App\Mail\ApplicationFormSubmittedMail;
use App\Mail\DocumentsRequestedMail;
use App\Mail\PageInquirySubmittedMail;
use App\Models\ApplicationForm;
use App\Models\PageInquiry;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Mail;

require __DIR__.'/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$n = PHP_EOL;

Mail::raw(
    'Проверка SMTP для Herd.'.PHP_EOL.'Время: '.now()->toIso8601String(),
    static function ($m): void {
        $m->to('local-raw@herd.test')->subject('[Marine API] Сырой SMTP-тест');
    },
);
Mail::purge();

$ship = PageInquiry::query()->create([
    'name' => 'Локальный тест (судовой менеджмент)',
    'company' => 'MTS QA',
    'position' => 'Superintendent',
    'phone' => '+70000000000',
    'email' => 'client@example.com',
    'vessel_types' => ['bulk_carrier'],
    'vessels_count' => 2,
    'vessel_flag' => 'PA',
    'main_ports' => 'Panama',
    'required_services' => ['spares'],
    'message' => 'Тест письма заявки с source_page=ship-management.',
    'source_page' => 'ship-management',
]);
Mail::to(['sblokhin@marin-ts.com', 'info@marin-ts.com'])->send(new PageInquirySubmittedMail($ship));
Mail::purge();
$ship->delete();

$other = PageInquiry::query()->create([
    'name' => 'Локальный тест (другой раздел)',
    'company' => 'MTS QA',
    'position' => null,
    'phone' => '+70000000001',
    'email' => 'client2@example.com',
    'vessel_types' => ['tanker'],
    'vessels_count' => 1,
    'vessel_flag' => 'LR',
    'main_ports' => null,
    'required_services' => ['audit'],
    'message' => 'Тест заявки с source_page=services.',
    'source_page' => 'services',
]);
Mail::to(['info@marin-ts.com'])->send(new PageInquirySubmittedMail($other));
Mail::purge();
$other->delete();

$minimalPdf = "%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF\n";

$appForm = new ApplicationForm([
    'vacancy_id' => null,
    'status' => ApplicationFormStatus::Pending,
    'full_name' => 'Иван Тестовый',
    'email' => 'seafarer@example.com',
    'phone' => '+79991234567',
    'payload' => [
        '_local_test' => true,
        'positionApplyingFor' => ['Chief Engineer'],
        'lastName' => 'Ivanov',
        'firstName' => 'Alexey',
        'desiredVesselTypes' => ['Tanker'],
    ],
]);
$appForm->id = 999001;
$appForm->uuid = 'a0eebc99-9c0b-4ef8-bb6d-6bb9bd380a11';
$appForm->exists = true;
$appForm->syncOriginal();

$recipients = array_values(array_filter(config('mail.application_form.recipients', [])));
if ($recipients === []) {
    $recipients = ['cv@marin-ts.com'];
}
Mail::to($recipients)->send(new ApplicationFormSubmittedMail($appForm, $minimalPdf));
Mail::purge();

Mail::to($appForm->email)->send(new DocumentsRequestedMail(
    $appForm,
    rtrim((string) config('app.frontend_url'), '/').'/application-forms/upload/test-token',
    ['passport_scan'],
));
Mail::purge();

echo "Готово. Проверьте входящие в Herd (Mail).{$n}Отправлено: raw, 2× заявка с сайта, анкета + запрос документов.{$n}";
