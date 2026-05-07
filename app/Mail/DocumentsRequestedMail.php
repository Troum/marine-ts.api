<?php

namespace App\Mail;

use App\Models\ApplicationForm;
use App\Support\MtsMailEnvelope;
use App\Support\RequestedDocumentCatalog;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentsRequestedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @param  list<string>  $documentKeys
     */
    public function __construct(
        public ApplicationForm $applicationForm,
        public string $uploadUrl,
        public array $documentKeys,
    ) {}

    public function envelope(): Envelope
    {
        $reply = config('mail.application_form.recipients');
        $replyAddr = is_array($reply) && isset($reply[0]) && trim((string) $reply[0]) !== ''
            ? trim((string) $reply[0])
            : 'cv@marin-ts.com';

        return MtsMailEnvelope::transactional(
            subject: 'Запрос дополнительных документов — '.config('app.name'),
            mailType: 'application-form-documents-requested',
            replyTo: [new Address($replyAddr, (string) config('app.name'))],
            entityId: $this->applicationForm->id,
        );
    }

    public function content(): Content
    {
        $labels = array_map(
            fn (string $k) => RequestedDocumentCatalog::labelFor($k),
            $this->documentKeys,
        );

        return new Content(
            html: 'emails.documents-requested',
            text: 'emails.documents-requested-text',
            with: [
                'labels' => $labels,
            ],
        );
    }
}
