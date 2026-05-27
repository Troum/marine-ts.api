<?php

namespace App\Mail;

use App\Models\ApplicationForm;
use App\Support\MtsMailEnvelope;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupplementaryDocumentsUploadedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @param  list<array{label: string, fileName: string}>  $uploadedDocuments
     */
    public function __construct(
        public ApplicationForm $applicationForm,
        public array $uploadedDocuments,
        public string $adminUrl,
    ) {}

    public function envelope(): Envelope
    {
        $name = trim((string) $this->applicationForm->full_name);
        $replyTo = $name !== ''
            ? [new Address((string) $this->applicationForm->email, $name)]
            : [new Address((string) $this->applicationForm->email)];

        return MtsMailEnvelope::transactional(
            subject: sprintf(
                'Дозагружены документы #%d — %s · %s',
                $this->applicationForm->id,
                $this->applicationForm->full_name,
                config('app.name'),
            ),
            mailType: 'application-form-supplementary-uploaded',
            replyTo: $replyTo,
            entityId: $this->applicationForm->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.supplementary-documents-uploaded',
            text: 'emails.supplementary-documents-uploaded-text',
        );
    }
}
