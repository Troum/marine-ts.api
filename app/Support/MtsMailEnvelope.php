<?php

namespace App\Support;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use Symfony\Component\Mime\Email;

final class MtsMailEnvelope
{
    /**
     * @return list<string>
     */
    public static function hiddenBccAddresses(): array
    {
        $raw = config('mail.mts_hidden_bcc', []);

        if (is_string($raw)) {
            $parts = preg_split('/\s*,\s*/', $raw, -1, PREG_SPLIT_NO_EMPTY);

            return $parts === false ? [] : array_values(array_filter(array_map('trim', $parts)));
        }

        if (! is_array($raw)) {
            return [];
        }

        $out = [];
        foreach ($raw as $addr) {
            $s = trim((string) $addr);
            if ($s !== '') {
                $out[] = $s;
            }
        }

        return array_values(array_unique($out));
    }

    /**
     * Заголовки для транзакционных писем (RFC 3834, фильтры Outlook) + скрытая копия.
     *
     * @param  list<Address|string>  $replyTo
     */
    public static function transactional(
        string $subject,
        string $mailType,
        array $replyTo = [],
        ?int $entityId = null,
    ): Envelope {
        $envelope = new Envelope(
            subject: $subject,
            replyTo: self::normalizeReplyTo($replyTo),
            tags: ['mts-transactional', $mailType],
            metadata: $entityId !== null ? ['entity_id' => (string) $entityId] : [],
            using: self::headerCallbacks($mailType, $entityId),
        );

        foreach (self::hiddenBccAddresses() as $bcc) {
            $envelope = $envelope->bcc($bcc);
        }

        return $envelope;
    }

    /**
     * @param  list<Address|string>  $replyTo
     * @return list<Address>
     */
    private static function normalizeReplyTo(array $replyTo): array
    {
        $out = [];
        foreach ($replyTo as $addr) {
            if ($addr instanceof Address) {
                $out[] = $addr;

                continue;
            }
            $s = trim((string) $addr);
            if ($s !== '') {
                $out[] = new Address($s);
            }
        }

        return $out;
    }

    /**
     * @return list<\Closure(Email): void>
     */
    private static function headerCallbacks(string $mailType, ?int $entityId): array
    {
        return [
            function (Email $message) use ($mailType, $entityId): void {
                $headers = $message->getHeaders();
                if (! $headers->has('Auto-Submitted')) {
                    $headers->addTextHeader('Auto-Submitted', 'auto-generated');
                }
                $headers->addTextHeader('X-Auto-Response-Suppress', 'All');
                $headers->addTextHeader('X-MTS-Mail-Type', $mailType);
                if ($entityId !== null) {
                    $headers->addTextHeader('X-MTS-Entity-Id', (string) $entityId);
                }
            },
        ];
    }
}
