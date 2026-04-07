<?php

namespace App\Mail;

use App\Models\FeedbackMessage;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FeedbackReplyMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @param  array<int, array{path: string, name: string, mime: string}>  $attachmentDescriptors
     */
    public function __construct(
        public FeedbackMessage $feedbackMessage,
        public string $replyBody,
        public User $sender,
        public array $attachmentDescriptors = [],
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: sprintf('Ответ на ваше сообщение — %s', config('app.name')),
            replyTo: [
                new Address($this->sender->email, (string) ($this->sender->name ?? '')),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.feedback-reply',
            text: 'emails.feedback-reply-text',
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $out = [];
        foreach ($this->attachmentDescriptors as $a) {
            $out[] = Attachment::fromPath($a['path'])
                ->as($a['name'])
                ->withMime($a['mime']);
        }

        return $out;
    }
}
