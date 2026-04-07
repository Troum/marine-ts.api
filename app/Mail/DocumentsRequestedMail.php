<?php

namespace App\Mail;

use App\Models\ApplicationForm;
use App\Support\RequestedDocumentCatalog;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
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
        return new Envelope(
            subject: 'Запрос дополнительных документов — Marine Technical Solutions',
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
