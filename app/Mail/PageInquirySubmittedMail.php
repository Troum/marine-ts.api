<?php

namespace App\Mail;

use App\Models\PageInquiry;
use App\Support\MtsMailEnvelope;
use App\Support\PageInquiryLabelResolver;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PageInquirySubmittedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public PageInquiry $pageInquiry,
    ) {}

    public function envelope(): Envelope
    {
        $replyName = trim((string) $this->pageInquiry->name);
        $replyTo = $replyName !== ''
            ? [new Address((string) $this->pageInquiry->email, $replyName)]
            : [new Address((string) $this->pageInquiry->email)];

        return MtsMailEnvelope::transactional(
            subject: sprintf(
                'Заявка с сайта #%d — %s · %s',
                $this->pageInquiry->id,
                $this->pageInquiry->company,
                config('app.name'),
            ),
            mailType: 'page-inquiry',
            replyTo: $replyTo,
            entityId: $this->pageInquiry->id,
        );
    }

    public function content(): Content
    {
        $vesselTypesHuman = PageInquiryLabelResolver::resolveList(
            $this->pageInquiry->vessel_types,
            $this->pageInquiry->vessel_type_labels,
        );
        $requiredServicesHuman = PageInquiryLabelResolver::resolveList(
            $this->pageInquiry->required_services,
            $this->pageInquiry->required_service_labels,
        );

        return new Content(
            html: 'emails.page-inquiry-submitted',
            text: 'emails.page-inquiry-submitted-text',
            with: [
                'vesselTypesHuman' => $vesselTypesHuman,
                'requiredServicesHuman' => $requiredServicesHuman,
            ],
        );
    }
}
