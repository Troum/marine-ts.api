<?php

namespace App\Console\Commands;

use App\Models\ApplicationForm;
use App\Support\ApplicationFormPdfTemplateData;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\View;
use Spatie\LaravelPdf\Enums\Format;
use Spatie\LaravelPdf\Facades\Pdf;

#[Signature('pdf:preview {--id= : ID конкретной анкеты (по умолчанию — последняя)}')]
#[Description('Генерирует HTML и PDF превью анкеты')]
class PreviewApplicationFormPdf extends Command
{
    public function handle(): int
    {
        $query = ApplicationForm::query()->orderByDesc('id');

        if ($id = $this->option('id')) {
            $form = $query->where('id', $id)->first();
        } else {
            $form = $query->first();
        }

        if (!$form) {
            $this->components->error('Анкета не найдена');
            return self::FAILURE;
        }

        $data = ApplicationFormPdfTemplateData::make($form);

        $htmlPath = storage_path('app/application-form-preview.html');
        $pdfPath  = storage_path('app/application-form-preview.pdf');

        file_put_contents(
            $htmlPath,
            View::make('pdf.application-form', $data)->render()
        );

        file_put_contents(
            $pdfPath,
            Pdf::view('pdf.application-form', $data)->format(Format::A4)->generatePdfContent()
        );

        $this->components->info("Анкета #{$form->id}");
        $this->components->bulletList([
            "HTML: $htmlPath",
            "PDF:  $pdfPath",
        ]);

        return self::SUCCESS;
    }
}
