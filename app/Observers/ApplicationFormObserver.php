<?php

namespace App\Observers;

use App\Mail\ApplicationFormSubmittedMail;
use App\Models\ApplicationForm;
use App\Support\ApplicationFormPdfTemplateData;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\LaravelPdf\Facades\Pdf;
use Throwable;

final class ApplicationFormObserver
{
    public function created(ApplicationForm $applicationForm): void
    {
        try {
            $pdfContent = Pdf::view('pdf.application-form', ApplicationFormPdfTemplateData::make($applicationForm))
                ->generatePdfContent();

            $to = config('mail.crewing_notification.address');

            Mail::to($to)->send(new ApplicationFormSubmittedMail($applicationForm, $pdfContent));
        } catch (Throwable $e) {
            Log::error('ApplicationForm PDF or crewing email failed', [
                'application_form_id' => $applicationForm->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
