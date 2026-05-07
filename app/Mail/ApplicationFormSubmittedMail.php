<?php

namespace App\Mail;

use App\Models\ApplicationForm;
use App\Support\MtsMailEnvelope;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
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
        $name = trim((string) $this->applicationForm->full_name);
        $replyTo = $name !== ''
            ? [new Address((string) $this->applicationForm->email, $name)]
            : [new Address((string) $this->applicationForm->email)];

        return MtsMailEnvelope::transactional(
            subject: sprintf(
                'Новая анкета #%d — %s · %s',
                $this->applicationForm->id,
                $this->applicationForm->full_name,
                config('app.name'),
            ),
            mailType: 'application-form-submitted',
            replyTo: $replyTo,
            entityId: $this->applicationForm->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.application-form-submitted',
            text: 'emails.application-form-submitted-text',
            with: [
                'pdfFileName' => $this->applicationForm->pdfFileName(),
            ],
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
                $this->applicationForm->pdfFileName(),
            )->withMime('application/pdf'),
        ];
    }
}
