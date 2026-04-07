<?php

namespace App\Mail;

use App\Models\ApplicationForm;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationFormSubmittedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public ApplicationForm $applicationForm,
        public string $pdfBinary,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: sprintf(
                'Новая анкета #%d — %s',
                $this->applicationForm->id,
                $this->applicationForm->full_name,
            ),
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.application-form-submitted',
            text: 'emails.application-form-submitted-text',
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(
                fn (): string => $this->pdfBinary,
                sprintf('anketa-%d.pdf', $this->applicationForm->id),
            )->withMime('application/pdf'),
        ];
    }
}
